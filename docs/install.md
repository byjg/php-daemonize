# Install a PHP class/method call as a daemon

This option allows you to create a daemon process from any PHP class. 

## Test the class/method call from command line
First you need to test how ro call the method from command line:

```bash
daemonize run \
    "\\Some\\Name\\Space\\MyExistingClass::someExistingMethod" \
    --rootdir "/path/to/root" \
    --arg "value1" \
    --arg "value2"
```

## Create the daemon process
If everything is ok, now you can "daemonize" this class (as root):

```php
daemonize install --template=systemd mydaemon \
    --class "\\Some\\Name\\Space\\MyExistingClass::someExistingMethod" \
    --rootdir "/path/to/root" \
    --arg "value1" \
    --arg "value2"
```

*note*: valid templates are:

- systemd (default)
- upstart
- initd
- crond


## Manage the daemon process

List all "daemonized" php classes:

```php
daemonize services --only-names
```

Start or stop the linux services:

```bash
sudo service mydaemon start  # or stop, status or restart
```



## Uninstalling

For uninstall just type:

```bash
daemonize uninstall mydamon
```
