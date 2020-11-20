<?php
/*------------------------
this is the file that controlls
the creation of forms...

-------------------------*/
class formBlock{


var $formAction; 		// the form action
var $formMethod;
var $errorArray; 		// 'element' => 'error msg'
var $hiddenArray; 		// elements to set to hidden. array.
var $forceValArray; 	// elements forced to a certain value... 'element' => 'value'
var $forbidGrepAry; 	// if the column name contains this it won't be displayed. array.
var $requiredArray;		// required elements - this class doesn't verify them, it just marks them for the user.
var $forceOptsAry;		// forces an element to have options...(element => optsArray)
var $forceOptsType;		// is it a SELECT or MENU...
var $formStrings; 		// array of translations to use in the form...
// var $formVerrTypes; 	// array of 'element' => 'rule'. rule can be a type (e-mail...), or a value (=[value])... // no... I'm going to use a different class for that...
var $codeString;		// this is the code for the form block...
var $devideType;		// devide long form into : table, div...
var $devideAt;			// devide long forms at the elements named... array...
var $devidePrefix;		// the name to give the parts of the devided form
var $devideClass;
var $count;
var $inblock;
var $formDesc;			// array describing the form elements (derived from MySQL DESCRIBE)


/*
to build the form, I need to describe the table and then call the
formelement class to get the parts.

The form needs to be wrapped with the appropriate action.

Elements need to be posted with any particulier warnings/errors.

Entries need to be doubled into a back-up (old value) set of hedden forms (?).

labels - localized - need to be applied...


*/
function formBlock(){
	$this->errorArray = array();
	$this->hiddenArray = array();
	$this->forceValArray = array();
	$this->requiredArray = array();
	$this->formMethod = 'post';
	$this->forbidGrepAry = array('idsr_','_ON');
	$this->codeString = '';
	$this->devideType = 'div';
	//$this->devideAt = array();
	$this->devidePrefix = 'form_div';
	$this->devideClass = 'formPart';
	$this->inblock = false;
	$this->forceOptsType = 's'; // or 'm'...
}


function open(){
	// function to open a form block
	$this->codeString = '<form action="' . $this->formAction . '" method="' . $this->formMethod . '" accept-charset="utf-8">';
}
function close(){
	// function to close a form block

	$this->codeString .= '</form>';
}

function popen(){
	$this->open();
	echo $this->codeString;
	$this->codeString = '';
}
function pclose(){
	$this->codeString = '';
	$this->close();
	echo $this->codeString;
	$this->codeString = '';
}

function pbuttons(){
	$this->codeString = '';
	$this->mkbuttons();
	echo $this->codeString;
	$this->codeString = '';
}
function mkbuttons(){
	$this->codeString .= '<input type="button" value="reset" name="clear" /> || <input value="submit" type="button" name="submit" />';
}

function build(){
	//build the form

	#
	# make the elements
	#

	//$this->codeString = '';
	$trads = new tradtool();
	$tc = $trads->dbLoadStrings();
	$this->count = 0;

	//echo('-> devideAt has ' . count($this->devideAt) . ' elements. <br/>');
	while ($row = $this->formDesc) {

		$showelement = false;
		/* echo 'Row ' . $row['Field'];
    echo '| ' . $row['Type'];
    echo '| ' . $row['Default'] . '<br />';*/
	// ok, so make form elements...

		$n = $row['Field'];

	// test element...
		$showelement = $this->displaytest($n);



		if($showelement){

			if($this->hidetest($n)){




			$f = new formelement;
			$this->count++;

			if((is_array($this->devideAt))&&($this->inblock==0)){

					//echo("-> opening " . $this->devideType . " for " . $n . " at element " . $this->count . ". <br />");
					$this->openFormPart();
					$this->inblock = true;


			}
			if($this->errortest($n)!==NULL){
				$this->codeString .= '<span class="error">' . $this->errortest($n) . '</span><br/>';
			}
			$rmarker = '';
			$rclass = '';
			if($this->requiredtest($n)){
				$rmarker = '*';
				$rclasscode = ' class="formrequired"';
			}
 			$this->codeString .= '<label' . $rclasscode . '><!--' . $row['Field'] . '-->' . $trads->popTrad($n) . $rmarker . ': <br/>';


			if($this->selecttest($n)!==NULL){
				if($this->forceOptsType=='s'){
					// make a select object
					$this->codeString .= $f->sa($n, $this->selecttest($n));
				} else {
					// make a menu
					$this->codeString .= $f->sm($n, $this->selecttest($n));
				}
			} else {
	// is there something to do?

			if(stripos($row['Field'],'ENUM')){
		// we need to look fo enum select options...

		// first we need to define the options set...
				$oa = split('_', $row['Field']);
				$optstype = array_pop($oa);
				$opts = array_pop($oa);
		// $opts = array_slice($oa,-2);
		// $opts = $opts[0];
				$this->codeString .= 'looking for opts : ' . $opts . '.';
		// get the options...
				$query2 = sprintf('SELECT * FROM %s WHERE %s = "%s"',
					mysql_real_escape_string($this->optsTable, GetMyConnection()),
					mysql_real_escape_string($this->optstypecol, GetMyConnection()),
					mysql_real_escape_string($opts, GetMyConnection()));

				$result2 = mysql_query($query2, GetMyConnection());
				unset($opts);
		// Check result
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.
				if (!$result2) {
    				$message  = 'Invalid query: ' . mysql_error() . "\n";
    				$message .= 'Whole query: ' . $query2;
    				die($message);
				}

		// build the options set... maybe...
				while($r2=mysql_fetch_assoc($result2)){
			// make a simple val => lable array...
					if($r2['opts_labelconst']!==''){
				// get the translated string...
						$opts2[$r2['opts_labelconst']]=$r2['opts_val'];
					} else {
						$opts2[$r2['opts_label']]=$r2['opts_val'];
					}

				}
				$this->codeString .= $f->sa($row['Field'], $opts2);
				mysql_free_result($result2);
				unset($result2, $opts2);

			} elseif(stripos($row['Field'],'SET')){

		// we need to look fo enum select options...

		// first we need to define the options set...
				$oa = split('_', $row['Field']);
				$optstype = array_pop($oa);
				$opts = array_pop($oa);
		// $opts = array_slice($oa,-2);
		// $opts = $opts[0];
				$this->codeString .= 'looking for opts : ' . $opts . '.';
		// get the options...
				$query2 = sprintf('SELECT * FROM %s WHERE %s = "%s"',
					mysql_real_escape_string($this->optsTable, GetMyConnection()),
					mysql_real_escape_string($this->optstypecol, GetMyConnection()),
					mysql_real_escape_string($opts, GetMyConnection()));

				$result2 = mysql_query($query2, GetMyConnection());
				unset($opts);
		// Check result
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.
				if (!$result2) {
    				$message  = 'Invalid query: ' . mysql_error() . "\n";
    				$message .= 'Whole query: ' . $query2;
    				die($message);
				}

		// build the options set... maybe...
				while($r2=mysql_fetch_assoc($result2)){
			// make a simple val => lable array...
					if($r2['opts_labelconst']!==''){
				// get the translated string...
						$opts2[$r2['opts_labelconst']]=$r2['opts_val'];
					} else {
						$opts2[$r2['opts_label']]=$r2['opts_val'];
					}

				}
				$this->codeString .= $f->sm($row['Field'], $opts2);
				mysql_free_result($result2);
				unset($result2, $opts2);

			}else{
				$this->codeString .= $f->r($row['Field'], $row['Type'], $row['Default']);
			}
			// if($n == $this->devideAt){ // this needs to be a strstr type call...

	}

			unset($f, $showelement);
			$this->codeString .= '</label><br />';
		}// end hide test...
		}//end forbid test
					if(($this->devidetest($n))&&(is_array($this->devideAt))){
						if($this->inblock){
							//echo('--> closing block for ' . $n . '. <br/>');
							$this->closeFormPart();
							$this->inblock = false;
						}


			}
	}
	if($this->inblock){
		$this->closeFormPart();
		//echo('--> closing last block at ' . $n . '. <br/>');

	}
	//$this->close();
	//$this->codeString = $this->codeString;
	return $this->codeString;
}
function pform(){


}

function hidetest($element=NULL){
	$result=false;

	foreach($this->hiddenArray as $k => $v){
		if(stripos($element, $v)<=-1){
			$result = true;
		}

	}
	return $result;
}

function selecttest($element=NULL){
	$result = NULL;
	foreach($this->forceOptsAry as $k => $v){
		if($element == $k){
			$result = $v;
			break;
		}
	}
	return $result;
}

function errortest($element=NULL){
	$result = NULL;
	foreach($this->errorArray as $k => $v){
		if($element == $k){
			$result = $v;
			break;
		}
	}
	return $result;
}

function requiredtest($element=NULL){
	$result = false;
	foreach($this->requiredArray as $k => $v){
		if($element == $v){
			$result = true;
			break;
		}
	}
	return $result;
}

function devidetest($element=NULL){
	$result=false;

	foreach($this->devideAt as $k => $v){
		if($element == $v){
			$result = true;
		} else {
			$result = false;
		}

	}
	return $result;
}
function openFormPart(){
	if($this->devideType=='div'){
		$this->codeString .= '<div id="' . $this->devidePrefix . '_' . $this->count . '" class="' . $this->devideClass . '">';
	} else {
		$this->codeString .= '<table id="' . $this->devidePrefix . '_' . $this->count . '" class="' . $this->devideClass . '">';

	}
}
function closeFormPart(){
	if($this->devideType=='div'){
		$this->codeString .= '</div>';
	} else {
		$this->codeString .= '</table>';
	}
}
function startDevidedContent(){

}
function endDevidedContent(){

}

#
# this function tells whether or not to create an element...
#
function displaytest($element=NULL){
	//
	//if($element=NULL){
	//	die("displaytest didn't get an element in the formblock class.");
	//}

	//$passed = -1;
	//ok, here go the scenarios...
	// is it forbidden?
	// echo ("displaytest for " . $element . ' : ');
	foreach($this->forbidGrepAry as $k => $v){
		$result=false;
		if(stripos($element,$v)>= -1){
			// echo (" --- forbid : " . $element . " = " . stripos($element,$v) . '. ');
			$result= false;
			break;
		} else {
			// echo (" --- ok: " . $element . " = " . stripos($element,$v) . '. ');
			$result= true;
		}
	}
	// echo('> forbidtest will return - ' . $result . '. <br/>');


	/*foreach($this->hiddenArray as $k => $v)	{
		if(stripos($element, $v)){
			echo (" --- hide: " . $element . " = " . stripos($element,$v) . '. ');
			$result='hide';
			//$passed=false;
		}
	}
	// set result to true if nothing happened...
	/*if($result!==''){
		return $result;
	} else{
		return $passed;
	}
	*/
	return $result;
}

}
?>
