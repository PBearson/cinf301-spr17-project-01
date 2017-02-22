<?php 

/**
 * A monitoring service that alerts the user about
 * any specified services.  
 * @author bryan
 *
 */

require_once '../../vendor/autoload.php';

abstract class MonitorService
{
	protected $attempt;
	protected $name;
	protected $link;
	
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
		$this->link = $link;
		$attempt = 1;
	}
}