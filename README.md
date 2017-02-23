* Name: CINF301 Service Monitor
* Author: Bryan Pearson
* Description: This project utilizes PHP daemons to simulate a monitoring service. Unfortunately Docker was not implemented in time, but ideally, Docker would have been used to automatically configure and run everything.

INSTRUCTIONS:

To run the program, do the following:
* Navigate to ServiceMonitor directory
* Run 'composer update' or 'composer install' to install dependencies.
* Run 'php Monitor.php' - you MUST specify the path to the config file using -c

Command line arguments: 
* -s | --speed:
	* Set the program speed. 1 by default. 2 = twice as fast, 3 = thrice as fast, etc.
	* Also accepts fractions. 0.5 = half as fast, 0.25 = quarter as fast, etc.
	* 0 and negative values not accepted.

* -c | --config:
	* Set the path to the config file. Should be an XML file.

* -o | --output:
	* Set the path to the output file. All Logs will be dumped here.
	* If the file does not exist already, one will be created.

* -n: 
	* Clear the output file and start fresh.
	* Assumes the configured output path already exists, otherwise has no effect.

* -a:
	* Automatically set the path to the config file as "../data/input.xml"
	* Also automatically set the global speed to 200.
	* For dev use
