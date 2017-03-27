# DO NOT REMOVE OR CHANGE THIS LINE - PHP_DAEMONIZE #
'[Unit]
Description=#DESCRIPTION#
#AssertPathExists=/srv/webserver

[Service]
Type=notify
ExecStart=#PHPPATH# #DAEMONIZESERVICE#
Nice=5

[Install]
WantedBy=multi-user.target
