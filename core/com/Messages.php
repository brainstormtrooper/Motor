<?php
class Messages {

    
    private static $_instance;
	
	private function __construct($args){
		// constructor code here it will be called once only
		$this->Result = (object)array();
		$this->Result->status = 1;
		$this->Result->messages = array();
		
		
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
}
?>