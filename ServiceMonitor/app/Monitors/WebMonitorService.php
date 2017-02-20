<?php

require_once __DIR__ . '/MonitorService.php';

class WebMonitorService extends MonitorService
{	
	public function execute()
	{
		print("Executing Web\n");
		print("Server name: " . $this->service . "\n");
	}
}
