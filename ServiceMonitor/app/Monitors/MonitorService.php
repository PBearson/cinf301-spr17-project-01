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
	protected $attempt;
	protected $name;
	protected $link;
	protected $logger;
	
	/**
	 * Execute the service - to be implemented inPortMonitorService
	 * and WebMonitorService
	 */
	abstract public function execute();
	
	/**
	 * Construct a new web or port monitor service
	 * @param MonitorManager manager the referencing monitor manager
	 * @param name the name of the service
	 * @param link the web or port link
	 */
	function __construct(MonitorManager $manager, string $name, string $link)
	{
		$this->manager = $manager;
		$this->name = $name;
		$this->link = $link;
		$attempt = 1;
		$this->logger = new Logger('service_logger');
	}
	
	/**
	 * Handle the service behavior based on the latest result
	 * Log the execution results to the output file
	 * @param bool $success whether the result was successful
	 */
	protected function handleResult(bool $success)
	{
		print($success . "\n");
	}
}