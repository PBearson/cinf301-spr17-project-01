<?php

require_once "../../vendor/autoload.php";

class PortMonitorService extends MonitorService
{
	//Check if the port is open
	public function execute()
	{
		$fh = @fsockopen('localhost',$this->link, $errno, $errstr, 5);
		if (is_resource($fh))
		{
			$add = "Port " . $this->name . " open\n";
			fclose($fh);
		}
		else $add = "Port " . $this->name . " closed\n";
		
		$path = $this->manager->OUTPUT_PATH;
		$contents = file_get_contents($path) . $add;
		file_put_contents($path, $contents);
	}
}