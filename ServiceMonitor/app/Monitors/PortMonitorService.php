<?php

require_once __DIR__ . '/MonitorService.php';

class PortMonitorService extends MonitorService
{
	public function execute()
	{
		print("Executing Port\n");
	}
}
