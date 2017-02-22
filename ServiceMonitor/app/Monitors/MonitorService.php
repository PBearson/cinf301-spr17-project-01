<?php 

/**
 * A monitoring service that alerts the user about
 * any specified services.  
 * @author bryan
 *
 */

require_once './MonitorManager.php';

abstract class MonitorService
{
	protected $data = array();
	protected $manager;
	
	/**
	 * Execute the service - to be implemented inPortMonitorService
	 * and WebMonitorService
	 */
	abstract public function execute();
	
	/**
	 * Construct a new web or port monitor service
	 * @param MonitorManager manager the referencing monitor manager
	 * @param array $data can include information about
	 * the service, link, frequency, and interval
	 */
	function __construct(MonitorManager $manager, array $data)
	{
		$this->manager = $manager;
		if(isset($data['service'])) $this->data['service'] = $data['service'];
		if(isset($data['link'])) $this->data['link'] = $data['link'];
		if (isset($data['frequency'])) $this->data['frequency'] = $data['frequency'];
		if (isset($data['interval'])) $this->data['interval'] = $data['interval'];
		if (isset($data['interval'])) $this->data['interval'] = $data['interval'];
		if (isset($data['interval'])) $this->data['interval'] = $data['interval'];
		$this->data['status'] = 'RUNNING';
		$this->data['attempt'] = 1;
		$this->data['state'] = 'INFO';
	}
	
	/**
	 * Retrieve a certain key from the Monitor Service
	 * @param string $key the key
	 * @return string|NULL returns either the value of the key
	 * querried or null, if it does not exist
	 */
	public function __get(string $key)
	{
		if(isset($data[$value])) return $value[$value];
		else return null;
	}
	
	/**
	 * Set a certain key from the Monitor Service
	 * @param string $key the key
	 * @param string $value the value to set the key to
	 */
	public function __set(string $key, string $value)
	{
		if (isset($data[$key])) $data[$key] = $value;   
	}
	
	
}