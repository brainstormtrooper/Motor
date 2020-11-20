<?php
class Page{

	/*
	 * the page has different parts...
	 * header,
	 * messages (session, feedback, error)...
	 * content,
	 * base (for bottom scripts and debugging)...
	 * 
	 */
	var $header_append;
	var $body_params;
	var $body_session;	/* Link to session data or a login page - see config */
	var $body_message;
	var $body_error;
	var $body_content;
	var $body_base_script;

	var $url_root;

	function Page(){

		//global $TRADS;
		//$this->body_session = '<div id="sessionInfo">'
		$this->header_append ='';
		$this->body_params='';
	//	$this->body_session='';
		$this->body_message='';
		$this->body_error='';
		$this->body_content='';
		$this->body_base_script='';

		$this->url_root=$_SERVER['PHP_SELF'];

		
		/*
		 * TODO : Do not automatically check for login details if disabled in config
		 */
		/*
		if(isset($_SESSION['idsr_usrs_login'])){
			$this->body_session = '[OK] : ' . parent::Tradtool.popTrad('logged_in_as') .  ' : ' . $_SESSION['sr_usrname'] . ' : <a href="?where=users&what=logout">' . parent::TRADS.popTrad('logout') . '</a> : <a href="?where=users&what=view">' . parent::TRADS.popTrad('my_account') . '</a> ';
		} else {
			
			$this->body_session =  '<a href="?where=users&what=login">' . $this->Tradtool.popTrad('login') . '</a> : <a href="?where=users&what=create">' . $this->Tradtool.popTrad('sign_up_now') . '</a> ';
		}

		if(isset($_SESSION['PAGE']['target'])){
			if(($_SESSION['PAGE']['target']=='?' . $_SERVER['QUERY_STRING'])||($_SESSION['PAGE']['target']==$_SERVER['REQUEST_URI'])||($_SESSION['PAGE']['target']=='next')){
							$ta = array('error','session','message','content');
			foreach($ta as $k => $p){
				$t = body_ . $p;
				$this->{$t} .= $_SESSION['PAGE'][$p];
				//die($this->body_{$p});
				//print '';
			}

			}
			unset($_SESSION['PAGE']);
		}
		//print_r($_SESSION['PAGE']);
	
*/
	}
	function getHTMLcontents(){
		$str = $this->wrap($this->body_session, 'session') . $this->wrap($this->body_message, 'message') . $this->wrap($this->body_error, 'error') . $this->wrap($this->body_content, 'content') . $this->body_base_script;
		return $str;
	}
	/*
	function voidDoLogin($next=NULL,$message=NULL,$part='message'){
		//global $TRADS;
		$urlstr = '?where=users&what=login';
		if($next!==NULL){
			

			$urlstr .= '&next=' . urlencode($next);
		}else{
			$urlstr .= '&next=' . urlencode($_SERVER['REQUEST_URI']);
		}
		if($message!==NULL){
			$this->forwardMessage($message, $part, $urlstr);
		} else {
			$this->forwardMessage(parent::TRADS.popTrad('You_must_be_logged_in_to_continue'), $part, $urlstr);
		}
		header('Location:' . $urlstr);

	}
	*/

	function wrap($what, $as){
		$str = '';
		switch ($as){
			case 'session':
				$str = '<div class="session">' . $what . '</div>';
			break;
			case 'message':
				$str = '<div class="message">' . $what . '</div>';
			break;

			case 'error':
				$str = '<div class="error">' . $what . '</div>';
			break;

			case 'content':
				$str = '<div class="content">' . $what . '</div>';
			break;

			default:
				$str = $what;

		}
		return $str;
	}
	
	function setGlobalParts(){
		// spit out the globals for TBS...
			$GLOBALS['body_content'] = $this->getHTMLcontents();
			$GLOBALS['header_append'] = $this->header_append;
			$GLOBALS['body_params'] = $this->body_params;
	}

	function forwardMessage($message='', $part='message', $page=NULL){
		$res=false;
		$_SESSION['PAGE'][$part] = $message;
		if($page!==NULL){
			//die('we have a page');

			$_SESSION['PAGE']['target'] = $page;
			$res = true;
		}else{
			$_SESSION['PAGE']['target'] = 'next';
		}
		return $res;

	}
}
?>
