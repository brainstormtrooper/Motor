<?php
class Calls {

    private static $_instance;

    private function __construct($args){
				
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
    
    public function modCall($args) {
    	
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

    public function ctrlCall($args){
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
?>