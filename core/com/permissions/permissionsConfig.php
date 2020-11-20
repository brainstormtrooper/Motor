<?php
/**


$component_rule = array('param1=val1,param2=val2_user@@group_daecr);
		// $where && $what
		'*&&*' => array ('**_*@@*_'), // [r]ead, [c]omment, [e]dit, [a]pprove, [d]elete
		'index&&*' => array ('**_*@@users_r'),
		'index&&foo' => array ('**_*@@users_ecr'),
		'content&&edit' => array ('author=me_me@@*_decr'),
		'content&&*' => array ('**_*@@editors_aecr')

		);

		'**_*@@*_00100000'
		rcead,
		*/

//$_SESSION['motor']['currentUser'] = 'bob';
//$_SESSION['motor']['currentGroup'] = 'users,bobgrp';



$GLOBALS['config']['permissions'] = array();

$GLOBALS['config']['permissions']['codes']=array(
		'd'=>'00000010',
		'c'=>'00010000',
		'e'=>'00001000',
		'r'=>'00100000',
		'a'=>'00000100'
);


$GLOBALS['config']['permissions']['*&&*'] = array ('**_*@@*_');


$GLOBALS['config']['permissions']['index&&*'] = array ('**_*@@*_r');
$GLOBALS['config']['permissions']['index&&bob'] = array ('**_*@@*_',
		'**_bob@@*_r',
		'**_*@@bobgrp_00010000',
		'**_bob@@bobgrp_decr',
		'param=val_*@@*_');

?>
