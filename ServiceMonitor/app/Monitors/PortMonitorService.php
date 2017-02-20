<?php

require_once './MonitorService.php';

class PortMonitorService extends MonitorService
{
	public function execute()
	{
		print("Executing Port\n");
		print("Server name: " . $this->service . "\n");
	}
}
