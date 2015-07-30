# PHP Daemonize

## Description

Transform any class in a *nix daemon process or cron job without changes or refactoring.

## Motivation

Some times we need to create a cron tab or a process for running in background. The most of times we need to
create a new class, probably in a different framework and have to set or even choose another language for create the
job/daemon.

"Daemonize" enables you to can create a linux daemon or a job for use in a cron tab without change you pre-existing class.

"Daemonize" is a script that create a "init.d" script and encapsulate or class enabling you to run it in the bash, for example.

## How to

Suppose you have a pre-existing class for read some info from database and if exists you run some action. For example:

```php
namespace Some\Name\Space;

class MyExistingClass
{
	// ...

    public function someExistingMethod()
    {
        // Your code
    }

	// ...
}
```

If you want transform this class and method in a linux daemon (or "daemonize" it) you have to first create a bootstrap php file.
This file will tell to the script all setup you need to run this class.

The most simple bootstrap.php is:

```php
require_once __DIR__ . "vendor/autoload.php";
```

Now, if you want to test it you can run the command:

```bash
daemonize run \\Some\\Name\\Space\\MyExistingClass::someExistingMethod /path/to/bootstrap.php
```

If everything is ok, now you can "daemonize" this class:

```php
daemonize install mydaemon \\Some\\Name\\Space\\MyExistingClass::someExistingMethod /path/to/bootstrap.php
```

Now for start or stop the service you need only

```bash
sudo service mydaemon start  # or stop, status or restart
```

For uninstall just type:

```bash
daemonize uninstall mydamon
```

and list all "daemonized" php classes

```php
daemonize list
```

## Install

Daemonize does not need to be associated to a project. Prefer install as a global package and as root user.

```bash
composer global require "byjg/php-daemonize=~1.0"
export PATH=/root/.composer/vendor/bin    # put this in the .bashrc or /etc/environment
```


## Running a pre-installed demo

Open two terminals.

First do :

```bash
touch /etc/tryme.txt
tail -f /etc/tryme.txt
```

On the second do:

```php
sudo daemonize install tryme \\ByJG\\Daemon\\Sample\\TryMe::process /root/.composer/vendor/byjg/php-daemonize/src/Sample/bootstrap.php

sudo service tryme start
```

If everything is OK, will see on the first terminal a lot of lines added. Do not forget to run `sudo service tryme stop`

