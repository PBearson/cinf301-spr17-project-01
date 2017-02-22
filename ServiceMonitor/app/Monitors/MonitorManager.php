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
				//Sleep for 1 second
				sleep(1);
				
				//Get the class (web or port) of the service and its parameters
				$class = $service->class;
				$parameters = $service->paremeters;
				
				//Ensure only appropriate services are parsed
				if(class_exists($class))
				{
					//If the service is running then check if it's time
					//To execute the service check
					if(array_key_exists($service->class, $this->activeServices))
					{
						$this->checkFrequency($service);
					}
					
					//Otherwise check if it's time to respawn the child
					else
					{
						$this->checkInterval($service);
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