# PHP Daemonize

[![Build Status](https://github.com/byjg/php-daemonize/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/byjg/php-daemonize/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](http://opensource.byjg.com)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-daemonize/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-daemonize.svg)](https://opensource.byjg.com/opensource/licensing.html)
[![GitHub release](https://img.shields.io/github/release/byjg/php-daemonize.svg)](https://github.com/byjg/php-daemonize/releases/)

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
<?php
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

The most simple bootstrap is `vendor/autoload.php` but you can create a more complex bootstrap file if you need.

Below is an example of a bootstrap file:

```php
require_once __DIR__ . "/vendor/autoload.php";

// Your code here
```

Now, if you want to test it you can run the command:

```bash
daemonize run \
    "\\Some\\Name\\Space\\MyExistingClass::someExistingMethod" \
    --bootstrap "relative/path/to/bootstrap.php" \
    --rootdir "/path/to/root" \
    --http-get "param1=value1&param2=value2"
```

You can test with:

```bash
daemonize run \
    "\\ByJG\\Daemon\\Sample\\TryMe::ping"
```

If everything is ok, now you can "daemonize" this class (as root):

```php
daemonize install --template=systemd mydaemon \
    --class "\\Some\\Name\\Space\\MyExistingClass::someExistingMethod" \
    --bootstrap "relative/path/to/bootstrap.php" \
    --rootdir "/path/to/root"
```

*note*: valid templates are:

- systemd (default)
- upstart
- initd
- crond

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
daemonize services --only-names
```

# Install

Daemonize does not need to be associated to your PHP project. Prefer install as a global package and as root user.

```bash
composer global require "byjg/php-daemonize=1.3.*"
sudo ln -s /root/.composer/vendor/bin/daemonize /usr/local/bin/daemonize
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
sudo daemonize install --template=upstart tryme "\\ByJG\\Daemon\\Sample\\TryMe::process" "vendor/autoload.php" "./"

sudo service tryme start
```

If everything is OK, will see on the first terminal a lot of lines added. Do not forget to run `sudo service tryme stop`
