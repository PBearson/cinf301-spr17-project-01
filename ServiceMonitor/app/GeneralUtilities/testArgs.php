<?php
require_once "./Utilities.php";

$parsed = new ParseArg();
$arguments = $parsed->argv;

foreach($arguments as $key=>$val)
{
	print($key . '=>' . $val . "\n");
};