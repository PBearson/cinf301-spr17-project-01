<?php 

/**
 * A monitoring service that alerts the user about
 * any specified services.  
 * @author bryan
 *
 */
abstract class MonitorService
{
	//The service name
	protected $service;
	
	//The port or web link
	protected $link;
	
	//Present status - RUNNING or NOT_RESPONDING
	protected $status;
	
	//Attempt number - 1, 2, 3
	protected $attempt;
	
	//Attempt state - INFO, WARNING, CRITICAL
	protected $state;
	
	//Time (in minutes) between checks
	protected $frequency;
	
	//Time between checks of NOT_RESPONDING services
	
	protected $interval;
	
	/**
	 * Execute the service - to be implemented inPortMonitorService
	 * and WebMonitorService
	 */
	abstract public function execute();
	
	/**
	 * Construct a new web or port monitor service
	 * @param array $data can include information about
	 * the service, link, frequency, and interval
	 * (Will only work if declared as  an array)
	 */
	public function __construct(array $data = NULL)
	{
		if(!is_array($data)) return;
		if(isset($data['service'])) $this->service = $data['service'];
		if(isset($data['link'])) $this->link = $data['link'];
		if (isset($data['frequency'])) $this->frequency = $data['frequency'];
		if (isset($data['interval'])) $this->interval = $data['interval'];
		$this->status = 'RUNNING';
		$this->attempt = 1;
		$this->state = 'INFO';
	}
}