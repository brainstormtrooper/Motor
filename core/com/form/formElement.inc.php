<?php
/*===========================================
create form elements

functions:
1. p()  - returns a form element (based on SQL DESCRIBE)
2. ph() - returns a hidden field
3. fo() - opens a form
4. fc() - closes a form
5. sa() - returns a SELECT element with otions from an array
6. so() - return a SELECT OPTION




============================================*/


     # so... I need to determine what to do with form elements...
     class formelement {

     var $elementCodeStr;  	// what gets printed out...
     var $elementNameStr; 	// the contents of $row['Field']...
     var $elementTypeStr; 	// csv with type info (from $row['Type']...174.45
     var $elementValStr; 	//  what's in $row['Default']...
     var $elementTitleStr; 	// the element title or hint...
     var $elementCurStr; 	// what's there now...
     var $fieldTypeSplit; 	// the array that will hold type data...
     var $elementFlagsStr; 	// eg 'binary' or 'unsigned zerofill'.
	 var $elementLabelStr;	// the contents of a <label> tag...
     var $elementLengthStr;
     var $fieldOptionsAry; 	// will become options <option value="$val">$key</option>... or other... or just use force array below...
     var $tmp;
     var $multiselect; 		// is the element multiselect? - bool.
	 var $fieldForceArray; 	// holds fiels that must get values from a table or array... forces a field to have a SELECT field... :
/*
$this->fieldForceArray = array(
	'label1' => 'value1''
	'label2' => value2'

);



*/



function formelement(){
     // what type of element...
     // can the current user see it
     // is there a default value?big handsome men
     // what text to disply?...

	$this->tmp = true;
	$this->fieldForceArray = Array();
	$this->elementLabelStr = NULL;
	$this->multiselect = false;



}
function forceVal($label, $value){
	//array_push( $this->fieldForceArray,
	$this->fieldForceArray[$label] = $value;

}

function pr(){

	echo $this->elementCodeStr;


}
function sm($elementNameStr, $ary, $elementCurAry=NULL){
	$this->multiselect = true;
		$checked = '';
	if($this->selectedtest($elementNameStr, $elementCurAry)){
		$checked = '  checked="checked"';
	}
	if(is_array($ary)){
foreach($ary as $label => $value){

	//$this->fieldOptionsAry[$label] = $value;
	//$this->elementCodeStr .= $this->so($label,$value);

		$this->elementCodeStr .=  '<label>' . $label . '<input type="checkbox" id="' . $elementNameStr . '" name="' . $elementNameStr . '" value="' . $value . '"' .  $checked . '/></label>';


}



} elseif(!empty($this->fieldOptionsAry)){

	foreach($this->fieldOptionsAry as $label => $value){
		//$this->elementCodeStr .=  $this->so($label,$value);
	$this->elementCodeStr .=  '<label>' . $label . '<input type="checkbox" id="' . $elementNameStr . '" name="' . $elementNameStr . '" value="' . $value . '"' .  $checked . '/></label>';
	}

}
return $this->elementCodeStr;
}

function selectedtest($element, $ary){
	$result = false;
	foreach($ary as $k => $v){
		if($element = $v){
			$result = true;
			break;
		}
	}
}
function sa($elementNameStr, $ary, $elementCurStr=NULL){
$this->elementCodeStr = '';
if(is_array($ary)){
foreach($ary as $label => $value){
	//$this->fieldOptionsAry[$label] = $value;
	$this->elementCodeStr .= $this->so($label,$value);

}



} elseif(!empty($this->fieldOptionsAry)){

	foreach($this->fieldOptionsAry as $label => $value){
		$this->elementCodeStr .=  $this->so($label,$value);

	}

}
$this->elementCodeStr = '<select name="' . $elementNameStr . '" id="' . $elementNameStr . '">' . $this->elementCodeStr . '</select>';
return $this->elementCodeStr;
}


function so($label, $value){
//$this->tmp =
return '<option value="' . $value  . '">' . $label  . '</option>';
}


function p($elementNameStr, $elementTypeStr, $elementValStr, $elementCurStr=''){

	echo $this->r($elementNameStr, $elementTypeStr, $elementValStr, $elementCurStr);
}

function r($elementNameStr, $elementTypeStr, $elementValStr='', $elementCurStr=''){


	ereg('^([^ (]+)(\((.+)\))?([ ](.+))?$',$elementTypeStr,$this->fieldTypeSplit);
	//# split type up into array
	$this->elementNameStr = $elementNameStr;
	$this->elementTypeStr = $this->fieldTypeSplit[1];// eg 'int' for integer.
	$this->elementFlagsStr = $this->fieldTypeSplit[5]; // eg 'binary' or 'unsigned zerofill'.
	$this->elementLengthStr = $this->fieldTypeSplit[3]; // eg 11, or 'cheese','salmon' for enum.
	// something like <span class="formelement"><input type="" id="" name="" slected="" value="" /> </span>...
	$this->elementCodeStr = "<!-- strFormElement failed to run -->";
	// need the lelment to make...
	if(!isset($elementNameStr)){
		$this->elementCodeStr= "<!-- strFormElement didn't get an element -->";
	} else {
		// run the function...

		// 1. is it an ID field...
		if(strpos($this->elementNameStr, 'idsr')===0){
			// it's an id field.

			$this->elementCodeStr = '<input name="' .  $this->elementNameStr . '" id="' .  $this->elementNameStr  .'" type="hidden" value="' . $this->elementCurStr . '" readonly="true" />';

      		} elseif (((strtolower($this->elementTypeStr)=='enum') || (strtolower($this->elementTypeStr)=='set')))
	    		 //if (($fieldType=='enum') || ($fieldType=='set'))
	    	 {
	     		//if($ret_fieldName==$fieldName){
	    		 $this->fieldOptionsAry = split("','",substr($this->elementLengthStr,1,-1));
	     		//return $fieldOptions;
	   		 //}
	    		 // menu or checkboxes ? (if its a set then I need a multi-line menu or check boxes, if it's a enum then I need radios or a select field...
	    		 // if there are few items (say 4 or less) the I'll use check and radio, otherwise it's select and menu...
	     		if(strtolower($this->elementTypeStr)=='enum'){
					if($this->elementLabelStr != NULL){
						$this->elementCodeStr = "<label>" . $this->elementLabelStr . "<br />";
					} else {
						$this->elementCodeStr = '';
					}
				$this->elementCodeStr .=  "<SELECT NAME=\"" . $this->elementNameStr. "\" SIZE='1'>";

				foreach($this->fieldOptionsAry as $this->tmp)  {
		    			 $this->elementCodeStr .= "<OPTION value=\"$this->tmp\">$this->tmp</OPTION>";
		    		}
		    		 $this->elementCodeStr .= "</SELECT>";
					 if($this->elementLabelStr != NULL){
						$this->elementCodeStr .= "</label>";
					}
	     		} elseif (strtolower($this->elementTypeStr)=='set'){
									if($this->elementLabelStr != NULL){
						$this->elementCodeStr = "<label>" . $this->elementLabelStr . "<br />";
					} else {
						$this->elementCodeStr = '';
					}
	     			foreach($this->fieldOptionsAry as $this->tmp)
	     			// look here to see how to deal with updates to checkboxes: http://www.codingforums.com/archive/index.php?t-79887.html
	    			 {
	    				 $this->elementCodeStr .=  '<label>' . $this->tmp . '<input type="checkbox" id="' . $this->elementNameStr . '" name="' . $this->elementNameStr . '" value="' . $this->tmp . '" checked="checked" /></label>';

		  		}
				 if($this->elementLabelStr != NULL){
						$this->elementCodeStr .= "</label>";
					}
		  	}
		  } else {
			// it's something else...
			#
			#  we need to be able to get values from another db table...( force a select fiels for something...)...
			# we'll see if the force variable is filled...
			#
			#

			if(count($this->fieldForceArray) != 0){
				// we need to present a SELECT element...
	if($this->elementLabelStr != NULL){
						$this->elementCodeStr = "<label>" . $this->elementLabelStr . "<br />";
					} else {
						$this->elementCodeStr = '';
					}
				$this->elementCodeStr .=  "<SELECT NAME=\"" . $this->elementNameStr. "\" SIZE='1'>";
				// populate it with the k/v pairs...
				foreach($this->fieldForceArray as $k => $v){
					$this->elementCodeStr .= "<OPTION value=\"$k\">$v</OPTION>";
				}

				$this->elementCodeStr .= "</SELECT>";
				if($this->elementLabelStr != NULL){
						$this->elementCodeStr .= "</label>";
					}
							} else {

	if($this->elementLabelStr != NULL){
						$this->elementCodeStr = "<label>" . $this->elementLabelStr . "<br />";
					} else {
						$this->elementCodeStr = '';
					}
				$this->elementCodeStr .= '<input  name="' . $this->elementNameStr . '" id="' . $this->elementNameStr . '" value="' . $this->elementCurStr . '" />';
				if($this->elementLabelStr != NULL){
						$this->elementCodeStr .= "</label>";
					}
			}


     // echo('<br />');


    		 }

	}
     return $this->elementCodeStr;
}

function ph($elementNameStr, $elementValStr, $roBool=true){
	// simply return a hidden field
$element =  '<input name="' .  $elementNameStr . '" id="' . $elementNameStr . '" type="hidden" value="' . $elementValStr . '" readonly="readonly" />';
return $element;
}
     }
 ?>
