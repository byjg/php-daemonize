# PHP Daemonize
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/37e7a7a0-402a-4add-a3bd-91b0c5cdc0ce/mini.png)](https://insight.sensiolabs.com/projects/37e7a7a0-402a-4add-a3bd-91b0c5cdc0ce)
[![Code Climate](https://codeclimate.com/github/byjg/php-daemonize/badges/gpa.svg)](https://codeclimate.com/github/byjg/php-daemonize)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/byjg/php-daemonize/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/byjg/php-daemonize/?branch=master)

## Description

Transform any class in a *nix daemon process or cron job without changes or refactoring.

## Motivation

Some times we need to create a cron tab or a process for running in background. The most of times we need to
create a new class, probably in a different framework and have to set or even choose another language for create the
job/daemon.

"Daemonize" enables you to can create a linux daemon or a job for use in a cron tab without change you pre-existing class.

"Daemonize" is a script that create a "init.d" script and encapsulate or class enabling you to run it in the bash, for example.

## How to

Suppose you have a pre-existing class for read some info from database and run some action with these data. For example:

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
require_once __DIR__ . "/vendor/autoload.php";
```

Now, if you want to test it you can run the command:

```bash
daemonize run "\\Some\\Name\\Space\\MyExistingClass::someExistingMethod" "relative/path/to/bootstrap.php" "/path/to/root"
```

If everything is ok, now you can "daemonize" this class (as root):

```php
daemonize install mydaemon "\\Some\\Name\\Space\\MyExistingClass::someExistingMethod" "relative/path/to/bootstrap.php" "/path/to/root"
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
daemonize services
```

## Install

Daemonize does not need to be associated to your PHP project. Prefer install as a global package and as root user.

```bash
composer global require "byjg/php-daemonize=1.2.*"
export PATH=/root/.composer/vendor/bin:$PATH    # put this in the .bashrc or /etc/environment
```

If you want to share this installation with another users consider use the command `chmod a+x /root`. The root
directory will remain unreadable for them, but you'll can execute the script "daemonize".


## Running a pre-installed demo

Open two terminals.

First do :

```bash
touch /etc/tryme.txt
tail -f /etc/tryme.txt
```

On the second do:

```php
sudo daemonize install tryme "\\ByJG\\Daemon\\Sample\\TryMe::process" "vendor/autoload.php" "."

sudo service tryme start
```

If everything is OK, will see on the first terminal a lot of lines added. Do not forget to run `sudo service tryme stop`

