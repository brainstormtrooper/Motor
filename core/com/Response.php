<?php

class Response{
	
	private static $_instance;
	protected $themePath = '';
	protected $themeFile = '';
	protected $template = '';
	protected $values = array();
	//protected $tags = array();
	//protected $markers = array();
	
	
	
    function __construct(){
    	$tfstr = $GLOBALS['config']['userThemeDir'] . (isset($_GET['where'])?$_GET['where'] . '/':'default/');
    	$mfstr = $GLOBALS['config']['userModDir'] . (isset($_GET['where'])?$_GET['where'] . '/view/':'default/view/');
    	$fstr = (isset($_GET['what'])?$_GET['what'] . 'Tpl.html':'defaultTpl.html');
    	
    	$m = $mfstr . $fstr;
    	$t = $tfstr . $fstr;
    	
    	
    	
    	if (file_exists($t)) {
    		$this->template = $t;
    		//print '<p>tpl using theme folder : ' . $t . '</p>';
    	}elseif (file_exists($m)){
    		$this->template = $m;
    		//print '<p>tpl using module folder : ' . $m . '</p>';
    	}else{
    		$this->template = $GLOBALS['config']['themePath'] . $GLOBALS['config']['themeFile'];
    		//print '<p>tpl using systme core folder : ' . $this->template . '</p>';
    	}   	
        
	}
	
	public static function init(){
		if(self::$_instance == null){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    public function setHeader($headerStr, $replace=true){
		header($headerStr, $replace);
	}

	public function getHeaders(){
		return headers_list();
	}
	
	public function delHeader($headerName){
		header_remove($headerName);
	}
	

    private function readTpl($filename) {
    	//$filename = $themePath . $themeFile;
    	return file_get_contents($filename);
    }
    
    private function getVal($i, $m){
    	$v = '';
    	
    	
	    if (stripos($m, '|')!==False) {
	    	$ka=explode('|', $m);
	    	$m = $ka[0];
	    	if ((array_key_exists($m, $this->values))&&($this->values[$m]!=Null)) {
	    		$es = $ka[1];
	    		// ":" delimits parrameters for a function call
	    		if (stripos($es, ':')!==False) {
	    			//print '>>> ' . $es . ' might be a function...<br>';
	    			$ea = explode(':', $es);
	    			if (function_exists(trim($ea[0]))) {
	    				//print '> Calling function ' . $ea[0] . '... ';
	    				$pa = (stripos($ea[1], ',')!==False?explode(',',$ea[1]):array($ea[1]));
	    						
	    				array_unshift($pa, $this->values[$m]);
	    				//print ' with params ' . print_r($pa,true) . '<br>';
	    				$v = call_user_func_array($ea[0],$pa);
	    			}else{
	    				// ok, don't use the backup
	    				$v=str_replace('%s', $this->values[$m], $ea[0]);
	    			}
	    		}else{
	    			// it wasn't a function, so it must be an optional wrapper
	    			
	    			//print 'optional value ' . $vals[$m] . ' found for ' . $k[1] . '<br>';
	    			
	    			$v=str_replace('%s', $this->values[$m], $ka[1]);
				 
	    		}
	    				
	    				
	    	}else{
	    		// key not in vals...
	    		$es = $ka[1];
	    		
	    		if (stripos($es, ':')!==False) {
	    			$ea = explode(':', $es);
	    			$v = $ea[1];
	    			
	    		}
	    	}
	    	// nope, just a string to replace
	    }elseif (array_key_exists($m, $this->values)) {
	    	//print('> Got value : "' . $vals[$m] . '" for key ' . $m . '<br>');	
	    	$v=$this->values[$m];
	
	    }else{
	    			
	    	// didn,t find a key/val pair...
	    	//print '>>> No value for ' . $m . '<br>';
	    	$v='';
	    }
	    
	    return $v;
    		
    }
    
    public function buildResponse($vals, $template='') {
    	if ($template=='') {
    		$template = $this->template;
    	}
    	//print '<p>module tpl path is : ' . $template . '</p>';
    	//$themeFile = 'defaultTpl.html';
    	$M = Motor::init();
    	$vals['mmessages'] = $M->Result->messages;
    	$this->values = $vals;
    	$result = $this->readTpl($template);
    	
    	#r"period_1_(.*)\.ssa", my_long_string
    	#m = re.findall ( '{$(.*?)}', self.responseString, re.DOTALL)
    	
    	$s = '/\{\@';
    	$e = '\@\}/';
    	#
    	# Gets all the fields in the template file...
    	# http://stackoverflow.com/questions/1445506/get-content-between-two-strings-php for php...
    	# and http://stackoverflow.com/questions/10827693/php-preg-replace-text-between-two-strings
    	# for any string use (.*?)
    	# for only letters, numbers, and _ use ([a-zA-Z0-9_]*)
    	#
    	$markers = array();
    	$regex = $s . '(.*?)' . $e;
    	preg_match_all($regex, $result, $markers);
    	
    	//print_r($markers);
    	
    	$tags = $markers[0];
    	//$this->markers = $markers[1];
    	$markers = $markers[1];
    	
    	foreach ($markers as $k => $m) {
    		$v = $this->getVal($k, $m);
    		
    		//print '>>> trying to replace "' . $tags[$k] . '" with "' . $v . '"';
     		$result = str_replace($tags[$k], $v, $result);
    		
    	}
    	
    	
    	return $result;
    	/*
    			print('>>> tryint to replace "' + str(m) + '" with "' + str(v) + '"...')
    			ms='{@' + str(m) + '@}'
    					self.tplString = self.tplString.replace(ms, str(v))
    	
    	
    					return self.tplString
    				*/	
    }
    
    
}
?>