<?php
/*
 * Starting point...
 * We need to see what we're doing and what we're sending back...
 * 
 *
 * 1. Include the basics (language, location, data sets, and a theme if we're sending back a page)
 * 2. Load a module and execute its functions as requested
 * 3. Combine the results with a theme of spit out whatever output was requested...
 * 4. Clean up...
 * 
 *
 */

session_start();
error_reporting(E_ALL);

//$config = array();
$GLOBALS['INSTALL_ROOT'] = dirname(__FILE__);
include 'config/config.php';


/*
 * Get motor and create an instance
 */

require 'Motor.php';

//$path = dirname(__FILE__);
//set_include_path(get_include_path() . PATH_SEPARATOR . $path);
spl_autoload_register(function ($class) {
	global $config;
	
	foreach ($config['path'] as $p) {
		$cp = $p . $class . '.php';
		if (file_exists($cp)) {
			include $cp;
			
		}
	}
	
	
});

$M = Motor::init();


/*
 *  ...
 */

//
// If we have a mod/func array load that, otherwise check the URL...
//
$m=(isset($_GET['where'])?$_GET['where']:'index');
$f=(isset($_GET['what'])?$_GET['what']:'index');
//$t=(isset($_GET['type'])?$_GET['type']:''); // useless for initial url loading
//
//
//
$args = array('where'=>$m, 'what'=>$f);
$result = $M->mtr_ctrlcall($args);

print $result;

?>
