# Show documentation

To show the documentation, you can use the following command:

```bash
daemonize run \
    "\\Some\\Name\\Space\\MyExistingClass::someExistingMethod" \
    --showdocs
```

## Example

```bash
$ daemonize run \
    "\\ByJG\\Daemon\\Sample\\TryMe::process" \
    --showdocs
```

This will return the following output:

```plaintext
This will return a pong message with the arguments passed and write to the file /tmp/tryme_test.txt
@param string $arg1
@param string|null $arg2

Usage: 
daemonize run "\\ByJG\\Daemon\\Sample\\TryMe::ping" --arg <arg1> --arg [arg2] 
```
