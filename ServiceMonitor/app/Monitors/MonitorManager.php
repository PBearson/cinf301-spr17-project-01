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
	
	//The path to the output file. Specify this by running
	//--output=path or -o path in the command line.
	public $OUTPUT_PATH = "";
	
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
				//Ensure only appropriate services are parsed
				if(class_exists($service->class))
				{
					//If the service is running then check if it's time
					//To execute the service check
					$name = $service->parameters->name;
					$name = "$name";
					if(!in_array($name, $this->activeServices))
					{	
						$this->checkFrequency($service);
					}
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
		$deleteOutput = false;
		
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
					
				case "output":
				case "o":
					$this->OUTPUT_PATH = $value;
					
				case "n":
					$deleteOutput = true;
					break;
					
				case "a":
					$this->CONFIG_PATH = "../data/input.xml";
					$this->GLOBAL_SPEED = 200;
					break;
			}
		}
		//Ensure valid arguments were entered
		if($this->GLOBAL_SPEED <= 0) 
		{
			$this->GLOBAL_SPEED = 1;
		}
		if(!file_exists($this->CONFIG_PATH))
		{	
			exit("Error: Path " . $this->CONFIG_PATH . " is not a file.\n");
		}
		if($this->OUTPUT_PATH == "")
		{
			$this->OUTPUT_PATH = "output.txt";
		}
		
		//Clear output file if the user wishes to start anew
		if($deleteOutput && file_exists($this->OUTPUT_PATH))
		{
			file_put_contents($this->OUTPUT_PATH, "");
		}
		
		//Create output file if it does not exist
		if(!file_exists($this->OUTPUT_PATH))
		{
			exec("> $this->OUTPUT_PATH");
		}
	}
	
	/**
	 * Check if an inactive service should respawn
	 * (PARENT PROCESS ONLY)
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
 				$this->checkInterval($service);
 			}
		}
 		else
 		{
 			
 		}

	}
	
	/**
	 * Check if an active service should execute
	 * (CHILD PROCESS ONLY)
	 * @param unknown $service
	 */
	private function checkInterval($service)
	{
		//Reset the counter for the child
		$this->counter = 0;

		//Create a new class
		$class = $service->class;
		$class = "$class";
		$reflect = new ReflectionClass($class);
		$instance = new $class($this, array());
		$method = $reflect->getMethod("execute");
		
		while(true)
		{
			sleep(1);
			$this->counter += $this->GLOBAL_SPEED;
			$method->invoke($instance);
			$interval = (double)$service->parameters -> interval * 60.00;
			
			if($this->counter >= $interval)
			{
				$method->invoke($instance);
				print("Logged\n");
			}
		}
	}
}

//Test manager
$manager = new MonitorManager();