# DO NOT REMOVE OR CHANGE THIS LINE - PHP_DAEMONIZE #
description "#DESCRIPTION#"
author      "PHP Daemonize"

# used to be: start on startup
# until we found some mounts weren't ready yet while booting:
start on runlevel [2345]
stop on shutdown

# Automatically Respawn:
respawn
respawn limit 99 5

script
    echo -n $"Starting $NAME: "
    #PHPPATH# #DAEMONIZESERVICE#
end script
