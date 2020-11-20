<?php
   // phpinfo();
   class formChecker{
   	
	var $formVals;		// the GET or POST array contents to check	
	var $elementAry;
	var $errorArray;	// errors to send back ('element'=>'error')
	var $errorCount;
	//var $formRules;		// 'element'=>array ('type'=>,'length'=>,'minlength'=>,required'=>,')
	
	
	function formChecker(){
		$this->formVals = array();
		$this->errorCount = 0;
		//$this->errorArray = array();
	}
	
	/*================================
	 * 
	 * 	Main Functions
	 * 
	 * ===============================*/
	
	function verifyForm($ary){
		//
	
		$this->elementAry = $ary;
		// check the form values...
		foreach($ary as $e => $args){
			// test it against the rule sets.
			//if(array_key_exists($e, $this->fromRules)){
				// what test do we do?
				if($args['etype']=='e-mail'){
					$this->emailtest($e,$_POST[$e]);
				}
				
				if($args['Type']=='int'){
					$this->inttest($e,$_POST[$e]);
				}
				
				if($args['etype']=='creditcard'){
					$this->cardtest($e,$_POST[$e]);
				}
				
				if($args['Type']=='date'){
					$this->datetest($e,$_POST[$e]);
				}
				
				/*
				 * check lengths and values
				 */
				if(is_int($args['length'])){
					$this->lengthtest($e,$_POST[$e]);
				}
				
				if($args['required']){
					$this->requiredtest($e);
				}
				if($args['unique']){
					$this->uniquetest($args);
				}
				if($args['Field']=='code'){
					$this->captchatest();
				}
			//}
			
			
			
		}
	return $this->errorCount;
	}
	
	
	
	/*---------------------------
	 * 
	 * 	Tests
	 * 
	 * ---------------------------*/
	
	function emailtest($k,$v){
		// test to see if $v is an e-mail address...
		if(!$this->checkEmail($v)){
			$result = 'error_not_valid_email';
			$this->seterror($k, $result);
		}
	}
	
	function inttest($k,$v){
		// test to see if $v is an integer...
		$result = NULL;
		if(!is_integer($v)){
			$result = 'error_not_integer';
			// it's not
			$this->seterror($k, $result);
			
		}
	}
	
	function cardtest($k,$v){
		// test to see if we have a valid card number.
		
		// need to verify that the number sequence matches the card type... need to have a card type in the post ary.
	}
	
	function datetest($k,$v){
		// test to see if we have a valid date
		
		// test to see if it matches a requested format...
	}
	
	function valuetest($k, $av){
		if(!is_array($av)){
			if($_POST[$k] !== $av){
				$result = 'error_bad_answer';			
			}
		}
	}
	function rangetest($k, $av){
		// see if the post value is in the value array given to the select element.
		$result='';
		if(is_array($av['value'])){
			if(!in_array($_POST[$k], $av['value'])){
				$result = 'error_answer_out_of_range';
				$this->seterror($k, $result);
			}
			
		}
	}
	function requiredtest($k){
		if((!isset($_POST[$k]))||($_POST[$k]=='')){
				$result = 'error_answer_required';
				$this->seterror($k, $result);			
		}
	}
	function captchatest(){
		require'app/pnp/securimage/securimage.php';
		
		$image = & new Securimage();
		
	    if ($image->check($_POST['code']) == false) {
	    	//die('bad captcha code');
				$result = 'error_captcha_error';
				$this->seterror('code', $result);	
    }
	}
	
	
	function uniquetest($args){
		/*
		 * function checks that a given value is unique in the table
		 * 
		 * we need to do a SELECT on $args['unique'] to count how many have the value of $_POST[$args['Field']]...
		 * 
	 * @param $args
	 * @return void
		 */
		include_once 'app/core/storage.class.php'; // no... storage class will be included, constructed, and globalized in index.php...
		$storage = new storage();
		$ary = array(
		'tbl' => $args['unique'],
		'col' => $args['Field'],
		'val' => $_POST[$args['Field']]
		);
		//die(print_r($ary));
		
		//global $STORAGE;
		
		if($storage->recordIsUnique($ary)<>0){
				$result = 'error_answer_exists';
				$this->seterror($args['Field'], $result);
		}
		
		
		
	}

	/*---------------------------
	 * 
	 * 	utilities
	 * 
	 * --------------------------*/
	function seterror($k,$error){
		// find the key for $v
		$this->errorCount++;
		$this->errorArray[$k]=$error;
		$this->elementAry[$k]['error'] = $error;
	}	
	
	function checkEmail($email) 
	{
   		if(eregi("^[a-zA-Z0-9_]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$]", $email)) 
   		{
      			return FALSE;
   		}

   		list($Username, $Domain) = split("@",$email);

   		if(getmxrr($Domain, $MXHost)) 
   		{
      			return TRUE;
   		}
   		else 
   		{
      			if(fsockopen($Domain, 25, $errno, $errstr, 30)) 
      			{
         			return TRUE; 
      			}
      			else 
      			{
         			return FALSE; 
      			}
   		}
	}
	
	
   }
?>
