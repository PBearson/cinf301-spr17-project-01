<?php 

require_once __DIR__.'/../../vendor/autoload.php';
require_once './WebMonitorService.php';
require_once './PortMonitorService.php';
require_once './../GeneralUtilities/Utilities.php';

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
	
	//Parent counter
	private $counter = 0;
	
	//The speed at which the program runs. Change this
	//by running --speed=x or -s x in the command line
	public $GLOBAL_SPEED = 1; 
	
	/**
	 * Construct the manager and run the infinite loop
	 */
	function __construct()
	{
		//Parse and interpret command line arguments
		$this->parseArgs();
		
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
	 * Parse command line arguments
	 */
	private function parseArgs()
	{
		$utilities = new Utilities();
		$args = $utilities->argv;
		
		foreach($args as $key=>$value)
		{
			switch($key)
			{
				case "speed":
				case "s":
					if($value > 0) $this->GLOBAL_SPEED = $value;
					break;
			}
		}
	}
	
	/**
	 * Check if an inactive service should respawn
	 * @param unknown $service
	 */
	private function checkFrequency($service)
	{
		//Sleep and increment the counter
		sleep(1/$this->GLOBAL_SPEED);
		print($this->GLOBAL_SPEED);
		$this->counter ++;
		$frequency = $service->parameters->frequency * 60;
		
// 		//Time to spawn a new process?
// 		if($this->counter >= $frequency)
// 		{
// 			//Add service to array
// 			$serviceString = '$service';
// 			array_push($this->activeServices, $serviceString);
// 			$pid = pcntl_fork();
// 			if($pid == 0) 
// 			{
// 				print ("Child\n");
// 				exit();
// 			}
// 			else print("Parent\n");
// 		}
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