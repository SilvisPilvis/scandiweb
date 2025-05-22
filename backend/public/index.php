<?php

// Define the application root directory
define('APP_ROOT', dirname(__DIR__));

use App\Model\DataLoaderModel;

// Load the bootstrap file to initialize the application
// This require call executes bootstrap.php and gets its return value
// $bootstrap = require APP_ROOT . '/bootstrap.php';
$bootstrap = include APP_ROOT . '/bootstrap.php';

// Access initialized components (like the database connection)
$db = $bootstrap['db'];
$graphql = $bootstrap['graphql'];

// --- Routing/Request Handling (for GraphQL) ---
// This is where you'll integrate your GraphQL handler
$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $r) use ($graphql, $db) {
        // $r->post('/graphql', [GraphQL::class, 'handle']);
        $r->post('/graphql', [$graphql, 'handle']);

        $r->addGroup(
            '/api',
            function (FastRoute\RouteCollector $r) {
                $r->get(
                    '/',
                    function () {
                        return 'Hello World!';
                    }
                );
                $r->get(
                    '/products',
                    DataLoaderModel::class . '::getProduct'
                );
            }
        );
    }
);

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        break;
}
