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
	//The speed at which the program runs. Change this
	//by running --speed=value or -s value in the command line
	public $GLOBAL_SPEED = 1;
	
	//The path to the config file. Specify this by running
	//--config=path or -c path in the command line.
	public $CONFIG_PATH = "";
	
	//The path to the output file. Specify this by running
	//--output=path or -o path in the command line.
	public $OUTPUT_PATH = "";
	
	//The service to give priority to. This is the next
	//service that 
	private $priorityService;
	
	//The config file
	private $configFile;
	
	//The services that have recently executed. Clears once 
	//All services have executed at least once.
	private $activeServices = array();
	
	//The frequencies of each service. Will change as the priority
	//service changes
	private $frequencies = array();
	
	//Parent counter
	private $counter = 0;
	
	/**
	 * Construct the manager and run the infinite loop
	 */
	function __construct()
	{
		//Parse and interpret command line arguments
		$this->parseArgs();
		
		//Set config file
		$this->configFile = simplexml_load_file($this->CONFIG_PATH);
		
		//Set target frequencies for each service
		$this->resetFreqencies();
		
		//Get priority service
		$this->priorityService = $this->getPriorityService();
		
		//Now run the main loop
		while(true) $this->loop();
		
// 		while(true)
// 		{
// 			foreach ($this->configFile->services->service as $service) 
// 			{	
// 				//Ensure only appropriate services are parsed
// 				if(class_exists($service->class))
// 				{
// 					//If the service is running then check if it's time
// 					//To execute the service check
// 					$name = $service->parameters->name;
// 					$name = "$name";
					
// 					if(!in_array($name, $this->activeServices))
// 					{	
// 						$this->checkFrequency($service);
// 					}
// 				}	
// 			}
// 		}
	}
	
	/**
	 * The parent loop. Runs the counter until the priority
	 * service is ready to spawn.
	 */
	private function loop()
	{
		//Sleep and increment the counter
		sleep(1);
		$this->counter += $this->GLOBAL_SPEED;
		
		//Get the priority services' frequency
		$name = $this->priorityService->parameters->name;
		$name = "$name";
		$frequency = $this->frequencies[$name];
		
		if($counter >= $frequency)
		{
			array_push($this->activeServices, $name);
			$this->spawnService();
			$this->prioritize();
		}
	}
	
	/**
	 * Spawn the priority service and run its loop
	 */
	private function spawnService()
	{
		
	}
	
	/**
	 * Increment the frequency of the priority service
	 * by its base (original) frequency. Then re-evaluate
	 * which service takes priority. If all services have
	 * spawned at least once, then reset frequencies, and
	 * subtract from count the same as what was subtracted
	 * from the next priority service. 
	 * Example: Service A's freq = 15 (base 5). Counter = 13.
	 * If all services have spawned, Service A's freq = 15 - 10 = 5.
	 * Counter = 13 - 10 = 3. We assume Service A has priority.
	 */
	private function prioritize()
	{
		
	}
	
	/**
	 * Reset the original frequencies of each service
	 */
	private function resetFreqencies()
	{
		foreach($this->configFile->services->service as $service)
		{
			$name = $service->parameters->name;
			$name = "name";
			$targetFrequency = $service->parameters->frequency;
			$this->frequencies[$name] = $targetFrequency;
		}
	}
	
	/**
	 * Get the next service that should spawn. The priority
	 * service is the one that should logically be next to spawn,
	 * so it is picked based on the difference between its spawn
	 * time and the global counter.
	 */
	private function getPriorityService()
	{
		$minTime = INF;
		foreach($this->configFile->services->service as $service)
		{
			$name = $service->parameters->name;
			$name = "name";
			$frequency = $this->frequencies[$name];
			$spawnTime = $frequency - $this->counter;
			if($spawnTime < $minTime)
			{
				$minTime = $spawnTime;
				$priorityService = $service;
			}
		}
		
		return $priorityService;
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
					if($this->GLOBAL_SPEED == 1) $this->GLOBAL_SPEED = $value;
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
			$this->OUTPUT_PATH = "output.log";
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
		$execute = $reflect->getMethod("execute");
		$exitStatus = $reflect->getMethod("getExitStatus");
		$alarmStatus = $reflect->getMethod("getAlarmStatus");
		
		//Get service name and port/link
		$name = $service->parameters->name;
		if($class == "PortMonitorService")
		{
			$link = $service->parameters->port;
		}
		else $link = $service->parameters->link;
		
		//Get interval
		$interval = $service->parameters->interval;
		
		//Create new instance of the class
		$name = "$name";
		$link = "$link";
		$interval = "$interval";
		$data = array();
		$data['name'] = $name;
		$data['link'] = $link;
		$data['interval'] = $interval;
		$instance = new $class($this, $data);
		
		while(true)
		{
			sleep(1);
			$this->counter += $this->GLOBAL_SPEED;
			$execute->invoke($instance);
			$interval = (double)$service->parameters -> interval * 60.00;
			
			//Is it time to check the service?
			if($this->counter >= $interval)
			{
				$exitStatus->invoke($instance);
			}
			
			//Is it time to exit?
			$shouldExit = $exitStatus->invoke($instance);
			$shouldAlarm = $exitStatus->invoke($instance);
			if($shouldExit)
			{
				unset($this->activeServices[$name]);
				sleep($service->parameters->frequency * (60/$this->GLOBAL_SPEED));
				exit("exitProcess");
			}
		}
	}
	
	/**
	 * Handle exit process
	 */
	private function exitProcess()
	{
		
	}
}

//Test manager
$manager = new MonitorManager();