# DO NOT REMOVE OR CHANGE THIS LINE - PHP_DAEMONIZE #
[Unit]
Description=#DESCRIPTION#

[Service]
Type=simple
EnvironmentFile=#ENVIRONMENT#
ExecStart=#PHPPATH# #DAEMONIZESERVICE# run "#CLASS#" --bootstrap "#BOOTSTRAP#" --rootdir "#ROOTPATH#" #CONSOLEARGS# --daemon
Nice=5

[Install]
WantedBy=multi-user.target
