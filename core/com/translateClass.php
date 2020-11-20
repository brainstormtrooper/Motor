<?php
 
class translate{
	 
	 
	var $constant;
	 
	var $trad;
	 
	var $STRINGS;
	 
	var $locale;
	 
	var $targetFile;
	 
	var $defaultFilePath;
	 
	var $localeVar;
	 
	var $defaultLocale;
	 
	var $defaultFileName;
	 
	var $defaultFileSuffix;
	 
	 
	 
	function translate(){
		if(!is_array($this->STRINGS)){

			$this->STRINGS = array();
		}
		$this->defaultFilePath = $GLOBALS['INSTALL_ROOT'] . '/locale';
		$this->localeVar = 'sr_usrLocaleStr';
		$this->defaultLocale = 'FR';
		$this->defaultFileName = 'STRINGS';
		$this->defaultFileSuffix = '_all';
	}
	 
	function setUsrLocale($locale){
		$this->locale = $locale;
	}
	 
	function setTargetFile($targetFile){
		$this->targetFile = $targetFile;
	}
	 
	function getTradLocale(){
		$l='';

		if(isset($_COOKIE[$this->localeVar])){
			$l=$_COOKIE[$this->localeVar];

		} elseif(isset( $_SESSION[$this->localeVar])){
			$l=$_SESSION[$this->localeVar];
		}elseif((isset($this->locale))&&(!empty($this->locale))){
			$l=$this->locale;


		}else{
			$l = $this->defaultLocale;
		}
		return $l;
	
	}
	
	function loadStrings(){
		 $STRINGS = array();
		//$f='';
		$l = $this->getTradLocale();
		
		
		$p = $this->defaultFilePath . '/' . $l . '/' . $this->defaultFileName . $this->defaultFileSuffix . '.php';
        if(file_exists($p)){
				include($p);
			}
		
		if((isset($this->targetFile))&&(!empty($this->targetFile))){
			$p = $this->defaultFilePath . '/' . $l . '/' . $this->defaultFileName . $this->targetFile . '.php';
			if(file_exists($p)){
				include($p);
			}
		}

		array_push($this->STRINGS , $STRINGS);
		return count($STRINGS);
		 
	}
	/* 
	function dbLoadStrings(){
		
		$l = $this->getTradLocale();
		
		include('../inc/db_link.php');
		$sql = 'SELECT * FROM sr_dbstrings WHERE sr_dbstring_locale = "' . $l  . '"';
		$result = mysql_query($sql, GetMyConnection());

		$tdata=array();
		mysql_data_seek($result, 0);
		while ($row_b = mysql_fetch_array($result, MYSQL_BOTH)) {
			array_merge($tdata, $row_b);
		}

		foreach($tdata as $tk => $tv){
			//print 'k= ' . $tv[0] . ', v= ' . $tv[1] . '</br>';
			$tvk = $tv[0];
			$trads[$tvk] = $tv[1];
		}
		array_merge($this->STRINGS , $trads);
		return count($trads);

	}
	 */
	function stringsToSession($STRINGS){
		$_SESSION['STRINGS'] = $this->STRINGS;
		 
	}


	 
	function popTrad($str=NULL, $STRINGSary=NULL){
		 
		#
		#  see if there's a translated string in a strings array ($STRINGS[$str])...
		#
		 
		// check to see if there are string arrays
		if(empty($this->STRINGS)){
			$this->trad = '<span class="Error">No STRINGS array found for [ ' . $str . ' ].</sapn>';
		} else {
			// check to see if the string is in the array
			if(in_array($str,$this->STRINGS)){
				// yes
				$this->trad = $this->STRINGS[$str];
			} else {
				//no
				$this->trad = '<span class="Error">[ ' . $str . ' ] not found in STRINGS array.</span>';
			}
		}
		// return the results
		return $this->trad;

	}
	 
	 
	 
}
 
 
 
 
?>
