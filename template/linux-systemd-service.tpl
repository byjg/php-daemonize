# DO NOT REMOVE OR CHANGE THIS LINE - PHP_DAEMONIZE #
[Unit]
Description=#DESCRIPTION#

[Service]
Type=simple
ExecStart=#PHPPATH# #DAEMONIZESERVICE#
Nice=5

[Install]
WantedBy=multi-user.target
