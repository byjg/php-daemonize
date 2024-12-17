# Call a GET RESt endpoint from command line

Calling an endpoint from command line without actualy doing a HTTP Request is very simple.

Your endpoint must be implemented using any PHP framework that handles the HTTP request and response.

e.g.:
 - byjg/restserver
 - slimphp/slim
 - zendframework/zend-expressive
 - symfony/http-foundation
 - etc.

Let's take a look at an example using the `byjg/restserver` framework, and with the controller file `app.php`:

```php
<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$routeDefinition = new RouteList();
$routeDefinition->addRoute(Route::get("/testclosure")
    ->withOutputProcessor(JsonOutputProcessor::class)
    ->withClosure(function ($response, $request) {
        $response->write([
            "result" => "OK",
            "arg" => $request->get("arg")
        ]);
    })
);

$restServer = new HttpRequestHandler();
$restServer->handle($routeDefinition);
```

The example above creates a simple REST endpoint `/testclosure` that returns a JSON with the argument `arg`.

To call this endpoint from the command line, you can use the following command:

```bash
daemonize call \
    /testclosure \
    --controller "app.php" \
    --http-get "arg=value1"
```
