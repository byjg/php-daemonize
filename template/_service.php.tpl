<?php
// #DESCRIPTION#

chdir("#ROOTPATH#");
require_once "#BOOTSTRAP#";
require_once "#DAEMONBOOTSTRAP#";
$runner = new \ByJG\Daemon\Runner("#CLASS#", "#SVCNAME#", #CONSOLEARGS#);
$runner->execute();
