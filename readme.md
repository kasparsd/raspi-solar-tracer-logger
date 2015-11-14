# EP Solar MPPT Tracer Logger and Stats with Raspberry Pi

Log the power output of the solar panels and the charge controller, and build some stats.

Uses [TracerComms software](https://github.com/StereotypicalSquirrel/TracerComms) to read the serial output of the MPPT Tracer and store it in CSV log files. Use the following Cron tasks:

	* * * * * php /path/to/log.php
	* * * * * php /path/to/stats.php

	# optional backup
	* * * * * rsync -avz /path/to/logs/ username@example.com:~/logs


## Author

[Kaspars Dambis](http://kaspars.net)