# Call a PHP method from command line

Assuming you have a class and a method like this:

```php
<?php
namespace Some\Name\Space;

class MyExistingClass
{
	// ...

    /**
     * This is sample method 
     * @param string $param1
     * @param string $param2
     */
    public function someExistingMethod(string $param1, ?string $param2 = null)
    {
        // Your code
    }

	// ...
}
```

You can run this method from command line with the following command:

```bash
daemonize run \
    "\\Some\\Name\\Space\\MyExistingClass::someExistingMethod" \
    --arg value1 \
    --arg value2
```

If is necessary to execute this method in a specific environment you can use the `--bootstrap` and `--rootdir` parameters:

```bash
daemonize run \
    "\\Some\\Name\\Space\\MyExistingClass::someExistingMethod" \
    --bootstrap "relative/path/to/bootstrap.php" \
    --rootdir "/path/to/root" \
    --arg value1 \
    --arg value2
```
