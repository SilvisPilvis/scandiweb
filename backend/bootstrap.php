<?php

/**
 * A bootstrap file for the application
 * bootstrap.php
 * php version  8.2
 *
 * @category    Bootstrap
 * @description Bootstrap file for the application
 * @package     App
 * @author      Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license     https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @version     GIT: main
 * @link        None
 */

// Ensure vendor autoloader is loaded
require __DIR__ . '/vendor/autoload.php';

// --- Configuration Loading ---
use Dotenv\Dotenv;
use App\Database\Database;
use App\Controller\GraphQL;

// Assumes your .env file is in the project root
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// --- Database Connection ---
$db = new Database();

if ($db->connect_error) {
    // In a real application, log this error and show a generic message
    // For a test, a die is acceptable to show the error
    echo "Connection failed: " . $db->connect_error;
    die("Connection failed: " . $db->connect_error);
}

$graphql = new GraphQL($db);

// Return necessary initialized objects or values
return [
    'db' => $db,
    'graphql' => $graphql,
    // You could return other initialized things here, like config
];
