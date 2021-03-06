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
     class formElement {

        var $tmp;
        var $eLen; // text input length
        var $eRow;
        var $eClass; // element class
        var $elementCodeStr;





function formElement(){
     // what type of element...
     // can the current user see it
     // is there a default value?big handsome men
     // what text to disply?...

        $this->tmp = '';
        $this->eLen = 55;
        $this->eRow = 5;
        $this->elementCodeStr = '';




}


function makeElement($args){


//function makeFromAry($args){
       // $this->elementCodeStr .= '<';

        // what are we making...
	switch ($args['etype']){
		case 'textarea':
			$this->elementCodeStr = '<textarea ';
			$this->setparams($args);
			$this->setIDs($args);
			$this->elementCodeStr .= '>';
			$this->elementCodeStr .= $this->valuetest($args);
			$this->elementCodeStr .= '</textarea>';
			$this->setLabel($args);

			break;
		case 'select':

			$this->elementCodeStr = '<select ';

			$this->setIDs($args);

			$this->setparams($args);

			$this->elementCodeStr .= '>';

	            $sel=false;
                foreach($args['value'] as $v => $l){
                	if($this->selectedtest($v, $args)){
                		$sel=true;
                	}
                     $this->elementCodeStr .= '<option value="' . $v . '"';
                     if($sel){
                     	$this->elementCodeStr .= ' selected ';
                     }
                     $this->elementCodeStr .= '>' . $l . '</option>';
                     $sel=false;
                }

            $this->elementCodeStr .= '</select>';
            $this->setLabel($args);

			break;

		case 'captcha':

			        //$args['Field'] = 'code'; // this is to compy with the default Secureimage' package for captcha...
                	$this->elementCodeStr = '<img src="app/pnp/securimage/securimage_show.php?sid=' . md5(uniqid(time())) . '" id="captcha-image" align="absmiddle" /><br />';
					$this->elementCodeStr .= '<a href="app/pnp/securimage/securimage_play.php" style="font-size: 13px">(Audio)</a><br /><br />';

					$this->elementCodeStr .= '<a href="#" onclick="document.getElementById(\'captcha-image\').src = \'app/pnp/securimage/securimage_show.php?sid=\' + Math.random(); return false">Reload Image</a><br />';
					$this->elementCodeStr .= $args['label'] . '<br/>';
					$this->elementCodeStr .= '<input ';
			$this->setparams($args);
			$this->setIDs($args);
			$this->elementCodeStr .= ' />';

			$this->setLabel($args);

			break;

		case 'bool':
			// make a boolean element...

			/*
			 *
			 * if the ['Type'] is 'tiynint', then we can have '0', or '1'... otherwise either 't' or 'f'...
			 *
			 */
			$str_t = 'TRUE'; // TODO move these to $this->str_t and str_f and have translated strings brought in...
			$str_f = 'FALSE';
			//die('found ' . stripos($args['Type'],'tinyint'));
			if(stripos($args['Type'],'tinyint') !== -1){ // TODO - try to replace stripos with srtcmp...
				$t = 1;
				$f = 0;
			} else {
				$t = $str_t;
				$f = $str_f;
			}
			$this->elementCodeStr .= '<input type="checkbox" ';
			$this->setparams($args);
			$this->setIDs($args);
			$this->elementCodeStr .= 'value="' . $t . '" ';

			if($this->selectedtest($t, $args)){
				$this->elementCodeStr .= 'checked="checked" ';
			}

			$this->elementCodeStr .= '/>';
			$this->setLabel($args, $args['label']);
/*			$this->elementCodeStr .= '<input type="radio" ';
			$this->setparams($args);
			$this->setIDs($args);
			$this->elementCodeStr .= $this->valuetest($args, $t);

			$this->elementCodeStr .= '/>';

			$this->setLabel($args, $str_t);

			$this->elementCodeStr .= '<br />';

			$this->elementCodeStr .= '<input type="radio" ';
			$this->setparams($args);
			$this->setIDs($args);
			$this->elementCodeStr .= $this->valuetest($args, $f);

			$this->elementCodeStr .= '/>';

			$this->setLabel($args, $str_f);*/


			break;

		case 'hidden':
			$this->elementCodeStr .= '<input ';

			$this->elementCodeStr .= 'type="hidden" readonly="readonly" ';
			$this->setparams($args);
			$this->setIDs($args);
			$this->elementCodeStr .= $this->valuetest($args);
			$this->elementCodeStr .= '/>';


			break;

		default:

			$this->elementCodeStr .= '<input ';

			$this->elementCodeStr .= 'type="' . $args['etype'] . '" ';
			$this->setparams($args);
			$this->setIDs($args);
			$this->elementCodeStr .= $this->valuetest($args);
			$this->elementCodeStr .= '/>';

			$this->setLabel($args);
	}
       // $this->elementCodeStr .= '<br />';
return $this->elementCodeStr;
}



function setIDs($args){
	$this->elementCodeStr .= ' id="' . $args['Field'] . '" name="' . $args['Field'] . '" ';
}

function setLabel($args, $force=''){
	$ts = '<label>';

	if($force !== ''){
		$ts .= $this->elementCodeStr . ' : ' . $force . '</label>';
 	} else {
 		$ts .= '<span class="bst_fe2_label">' . $args['label'] . ' : </span> ' . $this->elementCodeStr . '</label>';
 	}
 	//$this->elementCodeStr .= '/>';
	$this->elementCodeStr = $ts;
	unset($ts);
}

function valuetest($args, $val=''){
	if($val == ''){
		if(isset($args['Default'])){
			$val = $args['Default'];
		}
	}
	if(isset($args['current'])){
		$val = $args['current'];
	}

	$val = $this->setvalue($args, $val);

	return $val;
}

function selectedtest($o, $args){
	/*
	 * we need to deal with two types of data:
	 * 1. the type of select element (set, or enum), and
	 * 2. the available values (post, Default, or current)
	 *
	 * If there's a SET, then values in the DB will be CSVs, otherwise, just words or whatever...
	 *
	 * current values override Default values...
	 *
	 * @var unknown_type
	 */
	$sel = false;
	if(isset($args['Default'])){
		if(stripos($args['Default'],',')){
			if(in_array($o,explode(',',$args['Default']))){
				$sel = true;
			}
		} else {
			if($o == $args['Default']){
				$sel = true;
			}
		}
	}

	if(isset($args['current'])){
		if(stripos($args['current'],',')){
			if(in_array($o,explode(',',$args['current']))){
				$sel = true;
			}
		} else {
			if($o == $args['current']){
				$sel = true;
			}
		}
	}


	return $sel;
}

function setvalue($args, $val){
	$valstr ='';
	if((isset($args['etype']))&&(stripos('  radio, checkbox, bool', $args['etype']))){
		if($this->selectedtest($val, $args)){
		//if($this->valuetest($args)==)
			$valstr = 'checked="checked" value="' . $val . '" ';
		}
	}elseif((isset($args['etype']))&&($args['etype']=='select')){
		//if()
		if($this->selectedtest($val, $args)){
			$valstr = ' selected ';
		}
	}elseif((isset($args['etype']))&&($args['etype']=='textarea')){
		$valstr = $val;
	}else{
		$valstr = 'value="' . $val . '" ';
	}
	return $valstr;
}

function setparams($args){
	foreach($args['attributes'] as $p => $v){
		$this->elementCodeStr .= $p . ' = "' . $v . '" ';
	}



}

//////////// old functions  /////////////////


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

/*function selectedtest($element, $ary){
        $result = false;
        foreach($ary as $k => $v){
                if($element = $v){
                        $result = true;
                        break;
                }
        }
}*/
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




function ph($elementNameStr, $elementValStr, $roBool=true){
        // simply return a hidden field

$element =  '<input name="' .  $elementNameStr . '" id="' . $elementNameStr . '" type="hidden" value="' . $elementValStr . '" readonly="readonly" />';
return $element;
}
     }
 ?>
