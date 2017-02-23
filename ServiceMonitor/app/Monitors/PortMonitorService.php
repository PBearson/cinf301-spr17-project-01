<?php

require_once __DIR__ ."/MonitorService.php";

class PortMonitorService extends MonitorService
{
	//Check if the port is open
	public function execute()
	{
		$fh = @fsockopen('localhost',$this->link, $errno, $errstr, 5);
		if (is_resource($fh))
		{
			$this->handleResult(true);
			fclose($fh);
		}
		else $this->handleResult(false);
	}
}