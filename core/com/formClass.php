<?php
class Form {
function makeform($args){

	/*
	 * This is the functino that makes a form based on a table and other available information.
	 *
	 * $tbl - this is the database table to be examined to make the form. The form is configured based on different rule sets in the cfg files under cfg/forms/...
	 *
 * @param $tbl
 * @return string
	 */

/*	if(!isset($tbl)){
		die('no table for libforms');
	}*/



	global $TRADS;
	//$TRADS->dbLoadStrings();


	$cfgtbl = 'cfg/forms/table/form_' . $args['tbl'] . '.cfg.php';
	$cfggrp = 'cfg/forms/grp/form_' . $_SESSION['usrGroup'] . '.cfg.php';
	$cfgwhere = 'cfg/forms/where/form_' . $_GET['where'] . '.cfg.php';


	include_once 'formBlock2.class.php';

	// include_once 'db_link.php';
	//include_once 'tableDesc.class.php';
	// Doesn't exist any more. The module object will need to provide a description in the proper format:
	/*==============================================
	 * creates an array of elements:
	 *
	 * 		[row]=>([field][type][null][key][default][extra])
	 */

	$tf = new formBlock();

		//
		// $td=new tableDesc($args['tbl']);
		// should now have $args['desc'] from the module object...
		//
		$td = $args['desc'];
		$tf->sourceAry = $td->getDesc();
		$tf->elementAry = $tf->sourceAry;

		include_once 'default.cfg.php';


	if(file_exists($cfgtbl)){
	include $cfgtbl;
	//$form_sr_users_login_cfg
	$tbl_cfg_array = 'form_' . $args['tbl'] . '_cfg';
	// die(print_r($$tbl_cfg_array));// empty for new user..
		if(is_array(${$tbl_cfg_array})){
		foreach(${$tbl_cfg_array} as $k => $v){
			//print_r($$tbl_cfg_array);
			$tf->$k = $v;
		}
	}

	}




	if(file_exists($cfgwhere)){
		include_once $cfgwhere;
		$where_cfg_array = 'form_' . $_GET['where'] . '_cfg';

		//die(print_r(${$where_cfg_array}));

	if(is_array(${$where_cfg_array})){
		foreach(${$where_cfg_array} as $k => $v){
			$tf->$k = $v;
		}
	}
	}

	if(file_exists($cfggrp)){
		include_once $cfggrp;
		$grp_cfg_array = 'form_' . $_SESSION['usrGroup'] . '_cfg';


	if(is_array(${$grp_cfg_array})){
		foreach(${$grp_cfg_array} as $k => $v){
			$tf->$k = $v;
		}
	}
	}


	global $TRADS;
	//$tf->formStrings = $TRADS->arrayGetStrings();






		/*$tf->elementAry["sr_adstreet"]["etype"] = 'textarea';
		$tf->elementAry["sr_addescription"]["etype"] = 'textarea';*/


		foreach ($args as $key => $variable) {

			$tf->$key = $variable;
		}

		//print_r($tf->formStrings);
		if(isset($args['errorArray'])){
			//$this->errorArray
			//$tf->errorArray = $args['errorArray'];
			//$tf->errorArray=$args['errorArray'];

			$tf->currentArray = $_POST;
			//die(print_r($args['errorArray']));
		}


	$tf->build();

	$_SESSION['formDesc'] = $tf->elementAry;
	//die(print_r($tf->elementAry));



	$body_content = $tf->codeString;
	$header_append = $tf->header_append;
	$body_params = $tf->body_params;

/*	global $PAGE;

	$PAGE->body_params .= $body_params;
	$PAGE->body_content .= $body_content;
	$PAGE->header_append .= $header_append;*/


	$code_set=array('body_content' => $body_content,
					'header_append' => $header_append,
					'body_params' => $body_params);
	return $code_set;
}
function checkform($ary){
	include_once 'formVerif.class.php';
	include_once 'formBlock2.class.php';


	$cr = new formChecker();
	if($cr->verifyForm($ary['formDesc'])==0){
			$code_set=array('tbl' => $ary['tbl'],
							'result' => 'passed',
							'body_params' => '');
			return $code_set;
		//die (print_r($_POST));
	} else {
		//die(print_r($cr->elementAry));
		//$tf2 = new formBlock();
		global $TRADS;
				foreach ($ary as $key => $variable) {

			$args[$key] = $variable;
		}
		//print_r($args[formStrings]);
		$args['errorArray'] = $cr->errorArray;
		//$PAGE->body_error .= '<br/>' . $TRADS->popTrad('your_form_has_errors');
			$code_set=array('tbl' => $ary['tbl'],
							'result' => 'failed',
							'HTML' => makeform($args),
							'errorAry' => $cr->errorArray);



		//print_r($cr->errorArray);

		//$code_set = makeform($args);


	}
	return $code_set;
}

function storedata($ary){

}

function configform($tbl){

}
	
}