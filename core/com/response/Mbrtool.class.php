<?php

/*
determines what parts to use when building a response...
*/

class mbrtool{

	var $mbrPath;
	var $mbrBase;

	function mbrtool(){
		//constructor...
		$this->mbrBase="mbr/";
       
	}



	function strGetMbrPath($lang='*', $latlng='', $site=''){
		// returns the mbr path
		/*
		 * this function gets the mbr path.
		 * It looks at the lat and lng available, the entry point URL or site (remblai or JS), and the preferred language.
		 *
		 */
		if($lang=''){
			$lang = $_SESSION['sr_usr_locale'];
		}



		//'SELECT folder FROM `sr_mbrs` WHERE (country = "' . $lang . '" OR country IS NULL) AND sites LIKE "%' . $site . '%"';
		$mbrsql='SELECT folder FROM `sr_mbrs` WHERE (country = "' . $lang . '" OR country IS NULL) AND sites LIKE "%' . $site . '%" LIMIT 1';

		// now we have a basic set of mbr paths... now lets see if we need to filter it by location...
        
		
		$path = 'mbr/default/default/';

		$this->boolSetMbrPath($path);
        include(dirname(__FILE__) . '/libMbrHelper.php');
		return $path;
	}
	function boolMbrMapPage(){

	}
	function strGetPagePath(){

	}

	function mbrPageTest(){
		/*
		 * checks the current function folder (mbrPath/where/what_*) to see if it needs to use that page or use the default page in /mbr/domain/defaul/where/what_* or just use the function in ?where&what...
		 *
	 * @return string
		 */


	}

	function boolSetMbrPath($path=NULL){
		if($path!=NULL){
			$this->mbrPath = $path;
			$_SESSION['mbrPath'] = $path;
			return true;
		} else {
			return false;
		}
	}
}
?>