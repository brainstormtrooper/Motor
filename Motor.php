<?php

class Motor {

 

	var $URL = '';		// The current page url with the request string
	var $Result;	// The output object (status=-1,0,1; content=whatever; messages=error and debug)
	var $State = array();		// The current state of affairs of motor
	
	
	private static $_instance;
	
	private function __construct($args){
		// constructor code here it will be called once only
		$this->Result = (object)array();
		$this->Result->status = 1;
		$this->Result->messages = array();
		
		$fb_what = (array_key_exists('what',$_GET)?$_GET['what']:'index');
		$fb_where = (array_key_exists('where',$_GET)?$_GET['where']:'index');

		$this->what = (array_key_exists('what', $args)?$args['what']:$fb_what);
		$this->where = (array_key_exists('where', $args)?$args['where']:$fb_where);
		
		
	}
	
	public static function init($args=array()){
		if(self::$_instance == null){
			self::$_instance = new self($args);
		}
		return self::$_instance;
	}


	public function log($message, $level='debug'){
		$args = array(
				'depth'=>3,
				'level'=>$level,
				'message'=>$message
		);
		$this->message($args);
	}
	
	/**
	 * message function.
	 * adds a message to the debug logging mechanism.
	 * also allows for state flagging.
	 * 
	 * @param array 	$args
	 * @param int		$args['level'] 		the message level (notice, debug, warning, error...)
	 * @param string	$args['message']	the message text
	 * @param bool		$args['degrade']	whether to simply subtract 1 from the current status of motor
	 * @param bool		$args['halt']		if the reason for the message is fatal - this will halt the execution of the rest of the page and give an appropriate failure page (503, for example...
	 * 
	 */
	public function message($args=array()){
		$time = microtime();
		if (!array_key_exists('depth', $args)) {
			$depth=2;
		}else{
			$depth = $args['depth'];
		}
		if (!array_key_exists('tags', $args)||$args['tags']==false) {
			$args['message']=strip_tags($args['message']);
		}
		$caller = $this->GetCallingMethod($depth);
		$from = ' FROM : ' . $caller['function'] . ' in ' . $caller['file'] . ' on line ' . $caller['line'] . '. ';
		// level, message, degrade, halt
		$this->Result->messages[$time] = $time . ':: ' . strtoupper($args['level']) . ' (' . $this->where . ', ' . $this->what . ') ' . $args['message'] . $from;
	}
	
	/**
	 * Array
(
    [file] => /home/lufigueroa/Desktop/test.php
    [line] => 12
    [function] => theCall
    [args] => Array
        (
            [0] => lucia
            [1] => php
        )

)
	 * @param int	depth	the steps back in trace history to take: default is 2...
	 */
	protected function GetCallingMethod($depth=2){
		$e = new Exception();
		$trace = $e->getTrace();
		//position 0 would be the line that called this function so we ignore it
		$last_call = $trace[$depth];
		return $last_call;
	}
	//////////////////////////////////////////////////////
	//
	//		SESSION HANDLING
	//
	//		A session identifier can be given as a cookie.
	//		That identifier can be used to create a cache file name
	//
	//////////////////////////////////////////////////////
	public function loadSessionCache(){
		
	}
	
	public function saveSessionCache(){
		
	}
	
	
	
    function mtr_object_getSysMenu(){
        // go through the active modules that have an avaiable index function (not API)

    }

    function mtr_object_getModMenu(){
        // go through the active modules and find public functions listed in the module config or with _PUBLIC_ in their name.

    }
    
    public function mtr_modcall($args) {
    	
    	
    	
    	
    	
    	$m=(isset($args['where'])?$args['where']:$_GET['where']);
    	$f=(isset($args['what'])?$args['what']:$_GET['what']);
    	$mtr_modfolder = "modules/" . $m;
    	$mtrv_modpath_str = "modules/" . $m . "/" . $m . "Class.php";
    	
    	if(file_exists($mtr_modfolder . '/' . $m . 'Config.php')){
    		include_once $mtr_modfolder . '/' . $m . 'Config.php';
    	}
    	include 'config/config.php';
    	
    	$result = False;
    	if(file_exists($mtrv_modpath_str)){
    		include_once $mtrv_modpath_str;
    	
    		if(isset($f)){
    	
    	
    			if(function_exists($f)){
    				$path = dirname($mtr_modfolder);
    				if (stripos(get_include_path(), $path)!==false) {
    					set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    				}
    				$result = $f($args);
    	
    				// call_user_func_array(array($mtro_currentMod_object, $func), array(arg1, arg2));
    				// call_user_func_array(array($mtro_currentMod_object, $func), array());
    			}else{
    				die ('could not execute ' . $f . ' in ' . $m . '.');
    			}
    	
    	
    	
    	
    		}
    	}
    	
    	return $result;
    }

    public function mtr_ctrlcall($args){
        //
        // If we have a mod/func array load that, otherwise check the URL...
        //
        $m=(isset($args['where'])?$args['where']:$_GET['where']);
        $f=(isset($args['what'])?$args['what']:$_GET['what']);
        //$t=(array_key_exists('type', $args)?$args['type']:$_GET['type']);
        $mtr_modfolder = "modules/" . $m;
        $mtrv_modpath_str = "modules/" . $m . "/" . $m . "Ctrl.php";
 
        if(file_exists($mtr_modfolder . '/' . $m . 'Config.php')){
        	include_once $mtr_modfolder . '/' . $m . 'Config.php';
        }
        include 'config/config.php';
        
        if(file_exists($mtrv_modpath_str)){
            include $mtrv_modpath_str;
            
            if(isset($f)){
            
                
                    if(function_exists($f)){   
                    	$path = dirname($mtr_modfolder);
                    	if (stripos(get_include_path(), $path)!==false) {
                    		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
                    	}
                        $result = $f($args);
                        
                        // call_user_func_array(array($mtro_currentMod_object, $func), array(arg1, arg2));
                        // call_user_func_array(array($mtro_currentMod_object, $func), array());
                    }else{
                        die ('could not execute ' . $f . ' in ' . $m . '.');
                    }
                
            
            
                
            }else{
                $result = $index($args);
            }
            return $result;
        } else {
            die('nothing to do');
        }
         

    }





}
