<?php
require_once "./Utilities.php";

$parsed = new Utilities();
$arguments = $parsed->argv;

foreach($arguments as $key=>$val)
{
	print($key . '=>' . $val . "\n");
};