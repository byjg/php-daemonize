# PHP Daemonize

## Description

Transform any class in a *nix daemon process or cron job without changes or refactoring.

## Examples


daemonize run \\ByJG\\Daemon\\Sample\\TryMe::someMethod src/Sample/bootstrap.php 

daemonize install tryme \\ByJG\\Daemon\\Sample\\TryMe::someMethod src/Sample/bootstrap.php 

daemonize uninstall tryme


## Install

Just type: `composer global require "byjg/php-daemonize=~1.0"`

## Running Tests

