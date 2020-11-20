<?php
/*
need to:
1. see if a page exists in the mbr dir.
2. see if it's and insert, include, or whole page
3. set the session variable 'mbrmap' to remember what was found
4. return either the path and/or code, or FALSE to just run the standard page.

 */


$mbr_test_code = '';
$mbr_test_path = '';
$mbrtype = '';


if(!isset($_GET['where'])){
    $_GET['where'] = 'index';
}
if(!isset($_GET['what'])){
    $_GET['what'] = 'index';
}

if(file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $_SESSION['mbrPath'] . '/' . $_GET['where'])){
	//we have a folder...
	if(file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $_SESSION['mbrPath'] . '/' . $_GET['where'] . '/i_' . $_GET['what'])){
		// we need to put this in an iframe or object...
		$mbr_test_path = $_SESSION['mbrPath'] . '/' . $_GET['where'] . '/i_' . $_GET['what'] . '/';
		$mbrtype='i';
		
	}elseif(file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $_SESSION['mbrPath'] . '/' . $_GET['where'] . '/p_' . $_GET['what'])){
		// we need to load this as a whole page...
		$mbr_test_path = $_SESSION['mbrPath'] . '/' . $_GET['where'] . '/p_' . $_GET['what'] . '/';
		$mbrtype='p';
		
	}elseif(file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $_SESSION['mbrPath'] . '/' . $_GET['where'] . '/c_' . $_GET['what'])){
		// we need to strip in thr code between the <body> tags.....
		$mbr_test_path = $_SESSION['mbrPath'] . '/' . $_GET['where'] . '/c_' . $_GET['what'] . '/';
		$mbrtype='c';
		
	}elseif(file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $_SESSION['mbrPath'] . '/' . $_GET['where'] . '/' . $_GET['what'])){
		// we junst include the page...
		$mbr_test_path = $_SESSION['mbrPath'] . '/' . $_GET['where'] . '/' . $_GET['what'] . '/';
		$mbrtype=false;
		
	}
	
}

//$mbr_test_path = $_SESSION['mbrPath'] . '/' . $_GET['where'] . '/' . $_GET['what'] . '/';



if(file_exists($mbr_test_path . 'index.html')){
	// we have an html file...
	$mbrext='html';
} 
if(file_exists($mbr_test_path . 'index.php')){
	// we have a php file...
	$mbrext='php';
} 
// how do we include the page


if((file_exists($mbr_test_path . 'index.html'))||(file_exists($mbr_test_path . 'index.php'))){
	
	switch($mbrtype){
		case 'i':
			$mbr_test_code = '<object id="' . $_GET['where'] . '_' . $_GET['what'] . '" class="inlineMbrPage" src="' . $mbr_test_path . 'index.' . $mbrext . '"></object>';
		break;
		
		case 'p':
			$mbr_test_code = file_get_contents($mbr_test_path . 'index.' . $mbrext);
		break;
		
		case 'c':
			$c = file_get_contents($mbr_test_path . 'index.' . $mbrext);
			if(stripos('<body>', $c)){
				$ca = split('<body>', $c);
				$c0 = split('</body>', $ca[1]);
				$mbr_test_code = $c0;
			} else {
				$mbr_test_code = $c;
			}
		break;
		
		default:
			$mbr_test_code = file_get_contents($mbr_test_path . 'index.' . $mbrext);
		break;
	}
	
	print $mbr_test_code;
	



}


?>