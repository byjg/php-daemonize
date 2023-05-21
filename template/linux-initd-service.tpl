#!/bin/bash
#
#	/etc/init.d/#SVCNAME#
#
# Starts the php daemon for PHP class #CLASS#
# PHP_DAEMONIZE
#
# chkconfig: 345 95 5
# description: #DESCRIPTION#
# processname: #SVCNAME#

#startup values
mkdir -p /var/log/daemonize
log=/var/log/daemonize/general.log

#
#	Set NAME, PIDFILE and bin variables.
#
NAME="#SVCNAME#"
PIDFILE=/var/run/$NAME.pid

start() {
    echo -n $"Starting $NAME: "
    source #ENVIROMENT#
    start-stop-daemon --start --quiet --background --make-pidfile --pidfile $PIDFILE --exec -- #PHPPATH# #DAEMONIZESERVICE# run "#CLASS#" --bootstrap "#BOOTSTRAP#" --rootdir "#ROOTPATH#" #CONSOLEARGS# --daemonize --pidfile $PIDFILE --log $log --daemon
    case "$?" in
        0) echo "OK!" ;;
        1) echo "already started" ;;
        *) echo "Fail!" ;;
    esac
}

stop() {
    echo -n $"Stopping $NAME: "
    start-stop-daemon --stop --pidfile $PIDFILE
    case "$?" in
        0) echo "OK!" && rm -f $PIDFILE;;
        1) echo "already stopped" ;;
        *) echo "Fail!" ;;
    esac
}

restart() {
    stop
    start
}

reload() {
    restart
}

status_at() {
    echo -n $"Status $NAME: "
    if [ -f $PIDFILE ]; then
        echo "running"
    else
        echo "not running"
    fi
    echo
}

case "$1" in
start)
    start
    ;;
stop)
    stop
    ;;
reload|restart)
    restart
    ;;
condrestart)
    if [ -f $PIDFILE ]; then
        restart
    fi
    ;;
status)
	status_at
	;;
*)

echo $"Usage: $0 {start|stop|restart|condrestart|status}"
	exit 1
esac

exit $?
exit $RETVAL