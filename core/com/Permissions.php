<?php

/***
 * Permissions class
 * 
 * Permissions are granted from lowest number to highest number or by letter code (configured to numbers).
 * 0000000 is the lowest possible and thus the most powerful (such as system admin).
 * by default, 00100000 is basic read permission and is assigned to just about everyone (unidentified guests).
 *  
 * @author rick
 *
 */
class Permissions {

	private static $_instance;
	private $levels = array();
	private $perms = array();
	protected $myperms = 'bob';

	function __construct() {
		
		include dirname(__FILE__) . '/permissions/permissionsConfig.php';
		/**
		
		
		$component_rule = array('param1=val1,param2=val2_user@@group_daecr);
		// $where && $what
		'*&&*' => array ('**_*@@*_'), // [r]ead, [c]omment, [e]dit, [a]pprove, [d]elete   
		'index&&*' => array ('**_*@@users_r'),
		'index&&foo' => array ('**_*@@users_ecr'),
		'content&&edit' => array ('author=me_me@@*_decr'),
		'content&&*' => array ('**_*@@editors_aecr')
		
		);
		
		'**_*@@*_00100000'
		rcead,
		*/
		
		//$_SESSION['motor']['currentUser'] = 'bob';
		//$_SESSION['motor']['currentGroup'] = 'users,bobgrp';

		
	}

	public static function init(){
		if(self::$_instance == null){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * get the highest perm level for a given letter based rule set
	 * 
	 * @param string	$perm	the string of letter values for permissions granted.
	 */
	private function getNumVal($perm){
		//$GLOBALS['config']['permissions']['codes']
		foreach ($GLOBALS['config']['permissions']['codes'] as $key => $value) {
			if(!is_numeric($perm)){
				if (strcmp($key, $perm)==0) {
					return $value;
				}
			}
			return $perm;
		}
		return -1;
	}
	
	private function getString($num){
		foreach ($GLOBALS['config']['permissions']['codes'] as $key => $value) {
			if (strcmp($value, $perm)==0) {
				return $key;
			}
		}
		return '';
	}
	
	private function _me($udata) {
		$M = Messages::init();
		$me = (isset($_SESSION['motor']['currentUser'])?$_SESSION['motor']['currentUser']:'*');
		$mygrp = (isset($_SESSION['motor']['currentGroup'])?$_SESSION['motor']['currentGroup']:'*');
		$curusr = $me . "@@" . $mygrp;
		
		$isgrp = false;
		$isusr = false;
		
		if (strstr($mygrp, ',')) {
			$ugrps = explode(',', $mygrp);
		}else{
			$ugrps = array($mygrp);
		}
		


		$args=array('level'=>'debug', 'message'=>'<br/>$udata = <pre>' . print_r($udata,true) . '</pre>');
		$M->message($args);
		
		
		$args=array('level'=>'debug', 'message'=>'<br/>$ugrps = <pre>' . print_r($ugrps,true) . '</pre>');
		$M->message($args);
		
		
		if((in_array($udata[1], $ugrps))||($udata[1]=='*')){
			//
			// We have the group
			//
			$M->log('<br/>$udata[1] = ' . $udata[1]);
		
			$isgrp = true;
			$M->log('<br/>$isgrp = ' . $isgrp );
		
		}
		
		
		if(($udata[0] == $me)||($udata[0]=='*' && $isgrp==true)){
			//
			// We have the right user
			//
			$M->log('<br/>$udata[0] = ' . $udata[0] . ', $me = ' . $me);
			$isusr = true;
			$M->log('<br/>$isusr = ' . $isusr );
		
		}
		//
		// if we have a user and group match, we're good. If we have a grp match and *, we're good. If we have right grp but NOT right user, no match...
		//
		
		if(($isgrp)&&($isusr)){
			return true;
		
		}else{
			return false;
		}
		
	}

	function setPermission($args){
		$GLOBALS['config']['permissions'][$args['component']][] = $args['rule'];
	}
    
    function isLoggedinSession($args){
        
        
    }

	function getPermissions($args=array()){

		/*
		 * get permissions for a given item or the current item
		 * 
		 * returns the rules for a given request.
		 * 
		 */
		$M = Messages::init();
        
		
		
        
        $mylevel = '99999999';

        
        /*
         * what are we trying to see? - we will get an array of all perms for the componant
         */
		if (isset($args['component'])){
			
			$perms = $GLOBALS['config']['permissions'][$args['component']];
			//$M->log('Using component from GLOBAL ' . $perms);
		} else {

			$what = (isset($_GET['what'])?$_GET['what']:$M->where);
			$where = (isset($_GET['where'])?$_GET['where']:$M->what);

			$catchall = $where . "&&*";
            $curcomponent = $where . "&&" . $what;
            $args=array('level'=>'debug', 'message'=>'> component : ' . $curcomponent . ' < ');
			$M->message($args);
            if(isset($GLOBALS['config']['permissions'][$curcomponent])){
                $perms = $GLOBALS['config']['permissions'][$curcomponent];
                $M->log('>> using exact component for permissions : ' . $curcomponent);
            }elseif(isset($GLOBALS['config']['permissions'][$catchall])){
                $perms = $GLOBALS['config']['permissions'][$catchall];
                $M->log('>> using catchall component for permissions : ' . $catchall);
            }else{
                $perms = $GLOBALS['config']['permissions']['*&&*'];
                $M->log('>> using generic component for permissions :  *&&*');
            }
            $M->log('<b>This may not be working right! </b> Using compiled component ' . print_r($perms, true));
        }
        
        foreach($perms as $k => $r){
        	
            $isgrp = false;
            $isusr = false;
            
            $margs=array('level'=>'debug', 'message'=>'Checking rule : <b>' . $r . '...');
            $M->message($margs);
            $p = explode('_', $r);
            
            $M->log('p = <pre>' . print_r($p,true) . '</pre>');
            $this->myperms = $p[2];
            
            $M->log('$myperms = ' . $this->myperms);
            
            $udata = explode('@@', $p[1]);
            
            
            
            //
            // if we have a user and group match, we're good. If we have a grp match and *, we're good. If we have right grp but NOT right user, no match...
            //
			if ($this->_me($udata)) {
				$M->log('<br/><u>PASSED</u> New permissions : ' . $this->myperms);
				if(is_numeric($this->myperms)&&strlen($this->myperms)==8){
					$this->levels[]=$this->myperms;
				}else {
					$this->perms[]=$this->myperms;
				}
			}else{
				$M->log('<br/><u>XXXXX  FAILED  XXXXXX</u> User : ' . $p[1]);
			}
            
            $this->myperms = '';
           // return '';
        }
        
		
		$M->log('<br/><b>PERMS = <pre>' . print_r($this->perms,true) . '</pre> </b>');
        return array(sort($this->levels), implode(' ', $this->perms));
	}
    
	function chkPermission($args){
		
		/*
		 * $args...
		 * 
		 * Checks the current user and current component and returns the correct bit (0, or 1)...
		 * $_SESSION['motor']['permissions']['index&&*'] = array ('**', '*@@*', '000001');
		 * delete, approve, edit, create, comment, read
		 * 
		 * $_SESSION['motor']['permissions'][' $where && $what '] = array (' $params_CSV _ $me @@ $mygrp _ daecr ');
		 * 
		 */

        $M = Messages::init();
		
		$perms = $this->getPermissions($args); //now I have the component array, get the user and actual bit...
		//if(($perms!='')&&(stristr($perms, $args['perm'])!==false))
		//
		// get the right array(s) from the current user
		//
		$M->log('**** <pre>' . print_r($perms,true) . ' </pre> ***** ' . $args['perm'] . ' *****');
		
		$ok = false;
		
        if(($perms[1]!='')&&(stristr($perms[1], $args['perm'])!==false)){
            $ok = true;
        } 
        
        return $ok;

	}
}

?>