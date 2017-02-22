<?php 

require_once __DIR__.'/../../vendor/autoload.php';
require_once './WebMonitorService.php';
require_once './PortMonitorService.php';

/**
 * Model class that continuously checks if another
 * child monitoring service should spawn.
 * @author bryan
 *
 */

class MonitorManager
{	
	//The services that are currently running
	private $activeServices = array();
	
	private $counter = 0;
	
	/**
	 * Construct the manager and run the infinite loop
	 */
	function __construct()
	{
		$parsed = simplexml_load_file("../data/input.xml");
		
		while(true)
		{
			foreach ($parsed->services->service as $service) 
			{	
				//Get the class (web or port) of the service and its parameters
				$class = $service->class;
				$parameters = $service->paremeters;
				
				//Ensure only appropriate services are parsed
				if(class_exists($class))
				{
					//If the service is running then check if it's time
					//To execute the service check
					$serviceString = '$service';
					if(array_key_exists($serviceString, $this->activeServices))
					{	
						$this->checkInterval($service);
					}
					
					else
					{
						$this->checkFrequency($service);
					}
					//REFLECTION
					//$execute = $reflect->getMethod("execute");
					//$instance = new $class;
					//$execute->invoke($instance);
				}	
			}
		}
	}
	
	/**
	 * Check if an inactive service should respawn
	 * @param unknown $service
	 */
	private function checkFrequency($service)
	{
		sleep(1);
		$this->counter++;
		
	}
	
	/**
	 * Check if an active service should execute
	 * @param unknown $service
	 */
	private function checkInterval($service)
	{
		
	}
}

$manager = new MonitorManager();