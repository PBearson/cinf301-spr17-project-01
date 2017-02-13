Name: CINF301 Service Monitoring System
Author: Bryan Pearson
Version: 0.0 (02/13/2017)


"This service monitoring project is due by midnight (on Github) February 22.  The repository MUST be named EXACTLY cinf301-spr17-project-01
In class, we discussed the basic idea of this monitoring system that you will implement.  You will only be implementing the most basic system (for example, you will not need to restart a failed service), but there are many details to the project.  In general, the idea is that your system will monitor some number of services that will be listed in a configuration file.  Each service (either PortMonitorService or WebMonitorService, both which extend the abstract class MonitorService) will at least have:
name - service name
port or web link
present status - RUNNING or NOT_RESPONDING
attempt number - 1, 2, 3
attempt state (INFO, WARNING, CRITICAL)
frequency - time between checks (e.g. 10 would mean 10 minutes after the end of the last check)
interval - time between checks of a NOT_RESPONDING service (e.g. 0.5 would mean half a minute between successive checks once a service is found to not be responding.
The MonitorManager will be the model class that runs an infinite loop that continuously checks if it is time for another child monitoring process is to be spawned.  If it is time for another service to be monitored, a child is spawned that checks if the service is RUNNING.  If it is, then the child is done and exits (using SIGCHLD using an anonymous class) with a status logged as INFO with a comment that the service was running properly, and that service only gets checked again after the frequency stated above.  If the status is NOT_RESPONDING, then the status is logged as WARNING (with a descriptive comment) and the child sleeps for interval time (gets caught using SIGALRM) and tries again, incrementing the attempt number.  If now it is RUNNING, again an INFO comment is logged and the child exits with the service only getting checked again after the frequency stated above.  If it is again NOT_RESPONDING, the previous process is repeated one more time.  If on the third attemp, the status is NOT_RESPONDING, the status is logged as CRITICAL and the child exits.  The service is checked again after the frequency stated above.
More details are as follows:
Use Composer to install the monolog/monolog logging system.  You will notice that the Log Levels of this package include a number that include INFO, WARNING and CRITICAL.  These are the only three levels we will use to indicate if a service is running fine (INFO), not responding for two attempts (WARNING), and not responding after the third attempt (CRITICAL).  This is the output of your program.
Your project should have a directory structure that includes app/Monitors and app/GeneralUtilities, and you must conform to PSR-4 namespaces using Composer to include the autoload files
In your app/GeneralUtilities folder, you need the class Utilities.  This class will have your parsing arguments code from your homework (or you will need to complete it for this project) to parse the command line arguments to be passed in to the program.  
In your app/Monitors folder, you need the classes: MonitorManager, MonitorService, PortMonitorService, WebMonitorService.  The PortMonitorService checks a service by trying to open a socket to the port, and the WebMonitorService tries to open the link. (Sample programs for doing both are in the DaemonAndSignalsExample project in our class examples.)
Your MonitorService class is an abstract class with one abstract method called execute() which the derived classes PortMonitorService and WebMonitorService implement directly.  The MonitorService class should implement everything that needs to be done to monitor a service (detailed above) except for the actual step of opening the port or web page, which the implementing class does.
Your command line arguments include -c configfile -o outfile to specify the configuration file (which is an XML file) and the output file (which is the monolog produced output file).  A sample XML file is included with this project.
Each monitoring object for PortMonitorService and WebMonitorService must be instantiated using reflection (see testLoggingReflection.php in ClashesNamespaceComposer for example code) based on the services being checked as loaded from the configuration file.
I will ultimately provide you with a Dockerfile that you will use to test your code and for me to grade using.  I still need to work out the service daemon issues as some things have changed with how Docker is doing things.  (I will explain in class on Monday)."
~Daniel Plante
