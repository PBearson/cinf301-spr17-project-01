<?php 

/**
 * A monitoring service that alerts the user about
 * any specified services.  
 * @author bryan
 *
 */
abstract class MonitorService
{
	protected $data = array();
	
	/**
	 * Execute the service - to be implemented inPortMonitorService
	 * and WebMonitorService
	 */
	abstract public function execute();
	
	/**
	 * Construct a new web or port monitor service
	 * @param array $data can include information about
	 * the service, link, frequency, and interval
	 */
	public function __construct(array $data)
	{
		if(isset($data['service'])) $this->data['service'] = $data['service'];
		if(isset($data['link'])) $this->data['link'] = $data['link'];
		if (isset($data['frequency'])) $this->data['frequency'] = $data['frequency'];
		if (isset($data['interval'])) $this->data['interval'] = $data['interval'];
		if (isset($data['interval'])) $this->data['interval'] = $data['interval'];
		if (isset($data['interval'])) $this->data['interval'] = $data['interval'];
		$this->data['status'] = 'RUNNING';
		$this->data['attempt'] = 1;
		$this->data['state'] = 'INFO';
		$this->data['ready'] = 'YES';
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	
}