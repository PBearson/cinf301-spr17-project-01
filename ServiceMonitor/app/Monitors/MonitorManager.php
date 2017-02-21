<?php 

require_once "../../vendor/autoload.php";
use App\Monitors as Monitors;
/**
 * Model class that continuously checks if another
 * child monitoring service should spawn.
 * @author bryan
 *
 */

class MonitorManager
{
	function __construct()
	{
		$parsed = simplexml_load_file("../data/input.xml");
		foreach ($parsed->services->service as $service) 
		{
			$class = $service->class;
			$parameters = $service->paremeters;
			
			switch ($class)
			{
				case "WebMonitorService":
					$reflect = new Monitors\WebMonitorService();
					break;
					
				case "PortMonitorService":
					$reflect = new Monitors\PortMonitorService();
					break;
			}
			print($reflect->isInstantiable());
			
			//$connect = $reflectModel->getMethod("connect");
			//$instance = new $connection();
			//$connect->invoke($instance);
		}
	}
}

$manager = new MonitorManager();