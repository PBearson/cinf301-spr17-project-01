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
	//The services that are currently running. This holds 
	//The names of the services. Assumes no duplicate names.
	private $activeServices = array();
	
	//Parent counter
	private $counter = 0;
	
	//The speed at which the program runs. Change this
	//by running --speed=value or -s value in the command line
	public $GLOBAL_SPEED = 1;

	//The path to the config file. Specify this by running 
	//--config=path or -c path in the command line.
	public $CONFIG_PATH = "";
	
	/**
	 * Construct the manager and run the infinite loop
	 */
	function __construct()
	{
		//Parse and interpret command line arguments
		$this->parseArgs();
		
		$parsed = simplexml_load_file($this->CONFIG_PATH);
		
		while(true)
		{
			foreach ($parsed->services->service as $service) 
			{	
				//Get the class (web or port) of the service and its parameters
				$class = $service->class;
				$parameters = $service->parameters;
				
				//Ensure only appropriate services are parsed
				if(class_exists($class))
				{
					//If the service is running then check if it's time
					//To execute the service check
					$name = $parameters->name;
					$name = "$name";
					if(in_array($name, $this->activeServices))
					{	
						$this->checkInterval($service);
					}
					
					//If the service is inactive then check if it's time
					//To respawn it
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
					$this->GLOBAL_SPEED = $value;
					break;
					
				case "config":
				case "c":
					$this->CONFIG_PATH = $value;
					break;
			}
		}
		//Ensure valid arguments were entered
		if($this->GLOBAL_SPEED <= 0) $this->GLOBAL_SPEED = 1;
		if(!file_exists($this->CONFIG_PATH))
		{	
			exit("Error: Path " . $this->CONFIG_PATH . " is not a file.\n");
		}
	}
	
	/**
	 * Check if an inactive service should respawn
	 * @param unknown $service
	 */
	private function checkFrequency($service)
	{
		//Sleep and increment the counter
		sleep(1);
		$this->counter += $this->GLOBAL_SPEED;
		
		$frequency = $service->parameters->frequency * 60;
		
		//Time to spawn a new process?
		if($this->counter >= $frequency)
		{
			//Add service name to array and fork a new process
			$name = $service->parameters->name;
			$name = "$name";
			array_push($this->activeServices, $name);
 			$pid = pcntl_fork();
	
 			//Child
 			if($pid == 0) 
 			{
 				print("This is a child process\n");
 			}
 			else
 			{
 				print("This is a parent process\n");
 			}
		}
 		else
 		{
 			
 		}

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