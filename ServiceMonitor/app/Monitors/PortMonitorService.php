<?php

require_once "../../vendor/autoload.php";

class PortMonitorService extends MonitorService
{
	public function execute()
	{
		$path = $this->manager->OUTPUT_PATH;
		$contents = file_get_contents($path) . "Port\n";
		file_put_contents($path, $contents);
	}
}