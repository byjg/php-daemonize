# Running a pre-installed demo

Open two terminals.

First do :

```bash
touch /etc/tryme.txt
tail -f /etc/tryme.txt
```

On the second do:

```php
sudo daemonize install --template=systemd tryme "\\ByJG\\Daemon\\Sample\\TryMe::process" "vendor/autoload.php" "./"

sudo service tryme start
```

If everything is OK, will see on the first terminal a lot of lines added. Do not forget to run `sudo service tryme stop`
