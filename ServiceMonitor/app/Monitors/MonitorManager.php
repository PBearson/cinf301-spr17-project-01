<?php 

require_once __DIR__ .'/WebMonitorService.php';
require_once __DIR__ .'/PortMonitorService.php';
require_once __DIR__ .'/../GeneralUtilities/Utilities.php';

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
	
	private $parentPID;
	
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
		
		//Get parent pid
		$this->parentPID = getmypid();
		
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
		//Ensure this is the parent process
		if(getmypid() != $this->parentPID) return;
		
		//Sleep and increment the counter
		usleep(1000000 / $this->GLOBAL_SPEED);
		$this->counter++;
		
		//Get the priority services' frequency
		$name = $this->priorityService->parameters->name;
		$name = "$name";
		$frequency = (int)$this->frequencies[$name] * 60;
		
		//If it's time to spawn, then spawn, log the service,
		//And get the new priority service.
		if($this->counter >= $frequency)
		{
			$pid = pcntl_fork();
			if($pid == 0) $this->checkInterval($this->priorityService);
			array_push($this->activeServices, $name);
			$this->prioritize();
		}
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
		//Increment the priority service
		$name = $this->getPriorityService()->parameters->name;
		$name = "$name";
		$base = (int) $this->getPriorityService()->parameters->frequency;
		$current = (int)$this->frequencies[$name];
		$current += $base;
		$this->frequencies[$name] = "$current";
		
		//Get the new priority service
		$this->priorityService = $this->getPriorityService();
		
		//See if all services have spawned
		$spawnCount = 0;
		$serviceCount = 0;
		foreach($this->configFile->services->service as $service)
		{
			$serviceCount++;
			$name = $service->parameters->name;
			$name = "$name";
			$baseFrequency = $service->parameters->frequency;
			$currentFrequency = $this->frequencies[$name];
			if($baseFrequency < $currentFrequency || !class_exists($service->class)) $spawnCount++;
		}

		//If all services have spawned then reset frequencies
		if($spawnCount == $serviceCount)
		{
			$name = $this->getPriorityService()->parameters->name;
			$name = "$name";
			$current = (int)$this->frequencies[$name];
			$this->resetFreqencies();
			$new = (int)$this->frequencies[$name];
			$diff = 60 * ($current - $new);
			$this->counter -= $diff;
		}
	}
	
	/**
	 * Reset the original frequencies of each service
	 */
	private function resetFreqencies()
	{
		foreach($this->configFile->services->service as $service)
		{
			$name = $service->parameters->name;
			$name = "$name";
			$targetFrequency = $service->parameters->frequency;
			$this->frequencies[$name] = (int)$targetFrequency;
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
			$name = "$name";
			$frequency = $this->frequencies[$name];
			$spawnTime = $frequency - $this->counter;
			if($spawnTime < $minTime && class_exists($service->class))
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
					$this->CONFIG_PATH = __DIR__ ."/../data/input.xml";
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
			usleep(1000000 / $this->GLOBAL_SPEED);
			$this->counter++;
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
			
			if($shouldAlarm) $this->alarmProcess();
			if($shouldExit) exit($this->exitProcess());
		}
	}
	
	/**
	 * Handle a thrown process signal
	 * @param unknown $signo
	 */
	private function handle_signal($signo)
	{
		switch($signo)
		{
			case SIGCHLD:
				print ("Caught SIGCHILD\n");
				break;
				
			case SIGALRM:	
				print("Caught SIGALRM\n");
				break;
		}
	}
	
	private function alarmProcess()
	{
		pcntl_signal(SIGALRM, "MonitorManager::handle_signal");
		posix_kill(posix_getpid(), SIGALRM);
		pcntl_signal_dispatch();
	}
	
	/**
	 * Handle exit process
	 */
	private function exitProcess()
	{
		pcntl_signal(SIGCHLD, "MonitorManager::handle_signal");
		posix_kill(posix_getpid(), SIGCHLD);
		pcntl_signal_dispatch();
	}
}