* Name: CINF301 Service Monitor
* Author: Bryan Pearson
* Description: This project utilizes PHP daemons to simulate a monitoring service.

INSTRUCTIONS: 

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
