<?php
/*
 * Also lifted from : http://www.phpit.net/article/using-globals-php/4/
 * @author rick
 *
 */

class Request {
        private $_request = array();
       // private $_server = array();

        function Request() {
                // Get request variables
                // $this->_request = (isset($HTTP_SESSION_VARS)) ? (array_merge($HTTP_GET_VARS, $HTTP_POST_VARS)) : $_REQUEST;
				// $this->_server = (isset($HTTP_SERVER_VARS)) ? $HTTP_SERVER_VARS : $_SERVER;

        	$this->_request = $_REQUEST;
        }

        function get($name) {
                return $this->_request[$name];
        }

		function getRequestString(){

			$url='http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$urlary = $this->parseURL($url);
		}
		function parseURL($url){

		}
    
        function authorized(){
            /*
            Does the visitor have the right to access this function/content...
            */
            
        }
    
        function getAuthorization(){
            /*
            Run the preferred module function to get authorization...
            */
            // $_SESSION['motor']['request']['default_auth_module'] = 'users';
            // $_SESSION['motor']['request']['default_auth_function'] = 'login';
            $args=array(
                'where' => $_SESSION['motor']['request']['default_auth_module'],
                'what' => $_SESSION['motor']['request']['default_auth_function']
            );
            $this->mtr_modcall($args);
            
        }

}
?>