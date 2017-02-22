<?php 

/**
 * A monitoring service that alerts the user about
 * any specified services.  
 * @author bryan
 *
 */

require_once '../../vendor/autoload.php';
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
		$shouldExit = false;
		$this->logger = new Logger('service_logger');
	}
	
	/**
	 * Handle the service behavior based on the latest result
	 * Log the execution results to the output file
	 * @param bool $success whether the result was successful
	 */
	protected function handleResult(bool $success)
	{
		//Set up signal handlers
		pcntl_signal(SIGALRM, "handle_signal");
		
		$link = $this->manager->OUTPUT_PATH;
		
		//Success: Log as INFO, child exits
		if($success)
		{
			$handler = new StreamHandler($link, Logger::INFO);
			$this->logger->pushHandler($handler);
			$this->logger->info("This service is responding");
			$this->logger->popHandler();
			$this->shouldExit = true;
		}
		
		//Failure: Increment attempt count, Log as WARNING/CRITICAL,
		//With a responding/notresponding status. If third attempt
		//Fails, child exits
		else
		{
			pcntl_alarm((double)$this->interval * 60);
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
			}
			$this->logger->popHandler();
		}
	}
	
	/**
	 * Dispatch the signals
	 * @param unknown $signo
	 */
	protected function handle_signal($signo)
	{
		switch($signo)
		{
			case "SIGALRM":
				echo "Caught SIGALRM with signo " . $signo . "\n";
				break;
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
}