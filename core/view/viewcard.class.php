<?php

/**
 * This is the class that creates a display page for an array of data 
 * 
 * @author rick
 *
 */
class viewcard{
	/*
	 * 
	 * @var unknown_type
	 */
	
	var $codestring;
	var $errorstring;
	var $hiddenArray;			// keys with this in the name will not be displayed.
	var $blocaksArray;			// array of blocks to display - 'block'=>array('key1','key2'...)
	var $customContentArray;	// array of elements to include with their own HTML code - 'key' => 'code' is fed into elements array before processing layout...
	var $template;				// can be HTML code or a filename (to be hunted for under the MBR path), or a complete path...
	var $blockWrapCode;			// what do I wrap my lists in... '<div>###</div>...
	var $viewWrapCode;			// same thing, but it's for all output...
	var $elementArray;			// the starint array - from a SQL querry, for example...
	var $dataArray;				// keeps the original array...
	var $subSetArrays; 			// if the elements come from 2 or more querries or data sets, this tells the class what's there...
	var $elementOutputType;		// what HTML element do I genetrate? - default is <ul><li><span>key</span>value</li></ul>...
	var $trimUnsorted;			// bool - if true then all unsorted entries will be dropped
	var $inPanel;				// if we're in a panel
	var $panelHeadIsSet;		// if we put the spry code in the head
	var $panelFootCode;			// spry panel settings

	function viewcard(){
		// constructor...
		/*
		 * 
	 * @return void
		 */
		
		$this->codestring = '';
		$this->errorstring = '';
		$this->blockWrapCode = '<div id="displaycard">{@CODE@}</div>';
		$this->viewWrapCode = '';
		$this->elementOutputType = 'UL-KEY';
		$this->hiddenArray = array();
		$this->trimUnsorted = true;
		$this->inPanel = false;
		$this->panelHeadIsSet = false;
		$this->panelFootCode = '';
		
		
	}
	
	function buildView(){
		$this->dataArray = $this->elementArray;
		$this->filterhidden();
		$this->addcustomelements();
		$this->groupElements();
		$this->iterateblocks();
		
		
		if($this->inPanel){
				$tempStr .= $this->closePanel();
				$this->codestring .= $tempStr;
				$this->codestring .=$this->addPanelFoot();	
			}
		
		return $this->codestring;
		
		
		
	}
	
	function panelHead(){
		global $PAGE;
		
		$PAGE->header_append .= '<script src="lib/spry/widgets/collapsiblepanel/SpryCollapsiblePanel.js" type="text/javascript"></script>
<link href="lib/spry/widgets/collapsiblepanel/SpryCollapsiblePanel.css" rel="stylesheet" type="text/css" />
<link href="lib/spry/css/samples.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.CollapsiblePanel {
	width: 850px;
}
.CollapsiblePanelTab {
	font-size: 1em;
}
-->
</style>';
		$this->panelHeadIsSet = true;
	}
	
	function addPanelFoot($id=false){
		$tempcode = '';
		global $PAGE;
		/*
		 * <script type="text/javascript">
<!--
var CollapsiblePanel1 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel1");
var CollapsiblePanel2 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel2", {contentIsOpen:false, enableAnimation:false});
var CollapsiblePanel3 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel3");
var CollapsiblePanel4 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel4");
var CollapsiblePanel5 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel5");
//-->
</script>
		 * 
		 */
		if($id){
			$this->panelFootCode .= 'var ' . $id . ' = new Spry.Widget.CollapsiblePanel("' . $id . '");';
		} else {
			$tempcode = '<script type="text/javascript">' . $this->panelFootCode . '</script>';
			//$PAGE->body_content .= $tempcode;
			return $tempcode;
		}
		
	}
	
	
	function openPanel($id='panel', $label=''){
		if($label==''){
			$label=$id;
		}
		//print'opening panel "' . $id . '" , ';
		$tempStr = '';
		$tempStr = '<div id="' . $id . '" class="CollapsiblePanel">
  <div class="CollapsiblePanelTab">' . $label . '</div>
  <div class="CollapsiblePanelContent">';
		$this->inPanel = true;
		$this->addPanelFoot($id);
		return $tempStr;
	}
	
	function closePanel(){
		$tempStr = '';
		$tempStr = ' </div>
</div>';
		$this->inPanel = false;
		return $tempStr;
	}
	
	function iterateblocks(){
		global $TRADS;
		$tempStr = '';
		if(is_array($this->blocaksArray)){
		//	print_r($this->blocaksArray);
			foreach($this->blocaksArray as $block => $keys){
				$label = $TRADS->popTrad($block);
				//print_r($ary);
				if(stripos($block, 'panel')===0){
					
					//print ' - panel found at block "' . $block . '" where "panel is at ' . stripos($block, 'panel') . '. ';
					if(!$this->panelHeadIsSet){
						$this->panelHead();
					}
					//die("we have a panel");
					if($this->inPanel){
						//$tempStr .= $this->openPanel($block);
						$tempStr .= $this->closePanel();
						$tempStr .= $this->openPanel($block, $label);
						
					}else{
						//$tempStr .= $this->closePanel();
						$tempStr .= $this->openPanel($block, $label);
					}
					
				} else {
				$name = $block . '_list';
				$tempStr .= '<div class="dataListBlock" id="' . $block . '">' . $this->iterateList($keys, $name, 'keys', $block) . '</div>';
				}
				
			}
		
		} else {
			$tempStr = $this->iterateList($this->elementArray);
		}
	
		//return $tempStr;
		$this->codestring = $tempStr;
	}
	function iterateList($ary, $name='dataList', $type='vals', $block='NULL'){
	//print_r($ary);
		global $TRADS;
		$tempStr='<dl class="listData" id="' . $name . '">';
		
		switch ($type){
			case 'vals':
				foreach($ary as $k => $v){
					if(!array_key_exists($k, $this->customContentArray)){
						//$tempStr .= '<li class="viewcarDataItem" id="' . $k . '"><span class="dataItemLabel" id="' . $k . '_label">' .  $TRADS->popTrad($k) . ': </span>' . $v . '</li>';
						$tempStr .= '<dt id="' . $k . '_label">' .  $TRADS->popTrad($k) . '</dt><dd id="' . $k . '">' . $v . '</dd>';
					} else {
						//$tempStr .= '<li class="customDataItem" id="' . $k . '">' . $v . '</li>';
						$tempStr .= '<dt class="customDataItemLabel" id="' . $k . '_label">' .  $TRADS->popTrad($k) . '</dt><dd id="' . $k . '">' . $v . '</dd>';
					}
				}
			break;
			
			case 'keys':
						foreach($ary as $k => $ek){
					if(!array_key_exists($ek, $this->customContentArray)){
						//$tempStr .= '<li class="viewcarDataItem" id="' . $ek . '"><span class="dataItemLabel" id="' . $ek . '_label">' .  $TRADS->popTrad($ek) . ': </span>' . $this->elementArray[$block][$ek] . '</li>';
						$tempStr .= '<dt id="' . $ek . '_label">' .  $TRADS->popTrad($ek) . '</dt><dd id="' . $ek . '">' . $this->elementArray[$block][$ek] . '</dd>';
					} else {
						//$tempStr .= '<li class="customDataItem" id="' . $ek . '">' . $this->customContentArray[$ek] . '</li>';
						$tempStr .= '<dt class="customDataItemLabel" id="' . $ek . '_label">' .  $TRADS->popTrad($ek) . '</dt><dd id="' . $ek  . '">' . $this->customContentArray[$ek] . '</dd>';
					}
				}				
			break;	
		}
		

		$tempStr .= '</dl>';
		return $tempStr;
	}
	
	function addcustomelements(){
		if(is_array($this->customContentArray)){
			foreach($this->customContentArray as $e => $c){
				$this->elementArray[$e] = $c;
			}
			//print_r($this->customContentArray);
		}
		
	}
	function filterhidden(){
		$tempArray = array();
		$hiddenKeys = array_flip($this->hiddenArray);
		$tempArray = array_diff_key($this->elementArray, $hiddenKeys);
		/*foreach($this->hiddenArray as $k => $v){
			if(array_key_exists($v, $this->elementArray)){
				unset($this->elementArray[$v]);
			}
		}*/
		//print_r($tempArray);
		$this->elementArray = $tempArray;
		//print_r($this->elementArray);
	}
	
	function groupElements(){
		$tempAry = array();
		$thisBlock = array();
		$sortedKeys = array();
		$remaining = array();
		$currntBlock='';
		
		
		if((is_array($this->elementArray))&&(is_array($this->blocaksArray))){
			// sort the elements into the blocks...
			foreach($this->blocaksArray as $block => $elements){
				$currntBlock = $block;
				// peck out the data elements...
				foreach($elements as $k => $e){
					if(array_key_exists($e, $this->elementArray)){
						// it goes here...
						$thisBlock[$e] = $this->elementArray[$e];
						$sortedKeys[$e]=$e;
					}
				}
				$tempAry[$currntBlock] = $thisBlock;
			}
			$remaining = array_diff_key($this->elementArray, $sortedKeys);
			$r=count($remaining);
			//print $r;
			if(!$this->trimUnsorted){
				
			
			if($r!=0){
				//print $r;
				$currntBlock = 'default';
				//$this->blocaksArray[$currentBlock] = array();
				foreach($remaining as $k => $v){
					
					$tempAry[$currntBlock][$k] = $v;
					//$this->blocaksArray['default'][] = $k;
					$this->blocaksArray[$currntBlock][] = $k;
					
				}
				//print_r($tempAry);
			}
			//--
			}
		}
		//return $tempAry;
		$this->elementArray = $tempAry;
		//print_r($tempAry['picBlock']);
	}
	function boolInBlock(){
		
	}
	
}
?>
