<?php

require_once('KVC.php');

$subjects = array();

for ($i=0; $i < 1000; $i++) { 
	$subjects[] = array('hoi', "name" => "Ward", 'doei', "age" => 22, "awesome" => true, "devices" => array("MBA", "iPhone", "iPod", "iPad"));
}

?>