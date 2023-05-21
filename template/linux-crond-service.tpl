# /etc/cron.d/#SVCNAME#: DO NOT REMOVE OR CHANGE THIS LINE - PHP_DAEMONIZE

SHELL=/bin/bash

* * * * *     root     #ENVCMDLINE# #PHPPATH# #DAEMONIZESERVICE# run "#CLASS#" --bootstrap "#BOOTSTRAP#" --rootdir "#ROOTPATH#" #CONSOLEARGS# 

