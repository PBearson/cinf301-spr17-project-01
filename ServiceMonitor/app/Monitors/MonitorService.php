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
	private $service;
	
	//The port or web link
	private $link;
	
	//Present status - RUNNING or NOT_RESPONDING
	private $status;
	
	//Attempt number - 1, 2, 3
	private $attempt;
	
	//Attempt state - INFO, WARNING, CRITICAL
	private $state;
	
	//Time (in minutes) between checks
	private $frequency;
	
	//Time between checks of NOT_RESPONDING services
	
	private $interval;
	
	/**
	 * Execute the service - to be implemented inPortMonitorService
	 * and WebMonitorService
	 */
	abstract protected function execute();
}