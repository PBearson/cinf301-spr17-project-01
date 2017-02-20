<?php
require_once "./Utilities.php";

$parsed = new Utilities();
$arguments = $parsed->argv;

foreach($arguments as $key=>$val)
{
	if(is_array($val))
	{
		print($key . '=> array(');
		foreach($val as $key=>$subval)
		{
			if($key == sizeof($val) - 1)
			{
				print($subval . ")\n");
			}
			else
			{
				print($subval . ",");
			}
		}
	}
	else
	{
 		print($key . '=>' . $val . "\n");
	}
};