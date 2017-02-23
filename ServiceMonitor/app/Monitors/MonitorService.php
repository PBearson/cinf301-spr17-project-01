<?php 

/**
 * A monitoring service that alerts the user about
 * any specified services.  
 * @author bryan
 *
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

abstract class MonitorService
{
	protected $manager;
	protected $attempt;
	protected $name;
	protected $link;
	protected $logger;
	protected $interval;
	protected $shouldExit;
	protected $shouldAlarm;
	
	/**
	 * Execute the service - to be implemented inPortMonitorService
	 * and WebMonitorService
	 */
	abstract public function execute();
	
	/**
	 * Construct a new Web or Service Monitor
	 * @param MonitorManager $manager the parent monitor manager class
	 * @param array $data the service data. Should include name, link, interval
	 */
	function __construct(MonitorManager $manager, array $data)
	{
		$this->manager = $manager;
		$this->name = $data['name'];
		$this->link = $data['link'];
		$this->interval = $data['interval'];
		$attempt = 0;
		$this->shouldExit = false;
		$this->shouldAlarm = true;
		$this->logger = new Logger('service_logger');
	}
	
	/**
	 * Handle the service behavior based on the latest result
	 * Log the execution results to the output file
	 * @param bool $success whether the result was successful
	 */
	protected function handleResult(bool $success)
	{	
		$link = $this->manager->OUTPUT_PATH;
		
		//Success: Log as INFO, child exits
		if($success)
		{
			$handler = new StreamHandler($link, Logger::INFO);
			$this->logger->pushHandler($handler);
			$this->logger->info("This service is responding");
			$this->logger->popHandler();
			$this->shouldAlarm = false;
			$this->shouldExit = true;
		}
		
		//Failure: Increment attempt count, Log as WARNING/CRITICAL,
		//With a responding/notresponding status. If third attempt
		//Fails, child exits
		else
		{
			$this->shouldAlarm = true;
			$this->attempt++;
			if($this->attempt < 3)
			{
				$handler = new StreamHandler($link, Logger::WARNING);
				$this->logger->pushHandler($handler);
				$this->logger->warning("This service is not responding");
			}
			else
			{
				$handler = new StreamHandler($link, Logger::CRITICAL);
				$this->logger->pushHandler($handler);
				$this->logger->critical("This service has stopped responding. Maybe if you didn't suck, you could fix it.");
				$this->shouldExit = true;
				$this->shouldAlarm = false;
			}
			$this->logger->popHandler();
		}
	}
	
	/**
	 * See whether the child process is ready to exit or not
	 * @return unknown
	 */
	public function getExitStatus()
	{
		return $this->shouldExit;
	}
	
	public function getAlarmStatus()
	{
		return $this->shouldAlarm;	
	}
}