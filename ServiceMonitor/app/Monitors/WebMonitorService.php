<?php

require_once "../../vendor/autoload.php";
require_once "./MonitorService.php";

class WebMonitorService extends MonitorService
{	
	public function execute()
	{
		$path = $this->manager->OUTPUT_PATH;
		$contents = file_get_contents($path) . "Web\n";
		file_put_contents($path, $contents);
	}
}