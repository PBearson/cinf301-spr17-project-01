<?php

require_once "../../vendor/autoload.php";
require_once "./MonitorService.php";

class WebMonitorService extends MonitorService
{	
	//Check if the web link is open
	public function execute()
	{
		$fh = @fopen($this->link, "r");
		if (is_resource($fh))
		{
			$add = "Web " . $this->name . " open\n";
			fclose($fh);
		}
		else $add = "Web " . $this->name . " closed\n";
		
		$path = $this->manager->OUTPUT_PATH;
		$contents = file_get_contents($path) . $add;
		file_put_contents($path, $contents);
	}
}