* Name: CINF301 Service Monitor
* Author: Bryan Pearson
* Description: This project utilizes PHP daemons to simulate a monitoring service.

INSTRUCTIONS: 

Command line arguments: 
* -s | --speed:
	* Set the program speed. 1 by default. 2 = twice as fast, 3 = thrice as fast, etc.
	* Integers only (e.g. fractions and decimals not allowed).
	* 0 and negative values not accepted.

* -c | --config:
	* Set the path to the config file. Should be an XML file.

* -o | --output:
	* Set the path to the output file. All Logs will be dumped here.
	* If the file does not exist already, one will be created.
