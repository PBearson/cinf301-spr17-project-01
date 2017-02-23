<?php

require_once __DIR__ ."/MonitorService.php";

class WebMonitorService extends MonitorService
{	
	//Check if the web link is open
	public function execute()
	{
		$fh = @fopen($this->link, "r");
		if (is_resource($fh))
		{
			$this->handleResult(true);
			fclose($fh);
		}
		else $this->handleResult(false);
	}
}