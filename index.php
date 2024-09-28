<?php

// Allow from any origin
header("Access-Control-Allow-Origin: *");
// Allow the following HTTP methods
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Allow the following headers
header("Access-Control-Allow-Headers: Content-Type");
include 'framework/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (class_exists('Router')) {
    $router = new Router();

    // Map routes to controller actions
    $router->map('', 'Index@index');
    $router->map('/', 'Index@index');
    $router->map('/Dashboard/add_bet', 'Dashboard@add_bet'); // Make sure this is 'add_bet' if that's the intended method.
    $router->map('/Dashboard/get_bet_data', 'Dashboard@get_bet_data');
    $router->map('/Dashboard/display_bets', 'Dashboard@display_bets');
    $router->map('/Dashboard/update_bet', 'Dashboard@update_bet'); // Add this line for update_bet
    $router->map('/Dashboard/delete_bet', 'Dashboard@delete_bet');

    // Add other existing routes
    $router->map('/Child', 'Child@index');
    $router->map('/Index/calculation', 'Index@calculation');
    $router->map('/Index/get_data', 'Index@get_data');
    $router->map('/Index/add_race', 'Index@add_race');
    $router->map('/api/Index/get_data', 'Index@get_data');
    $router->map('/api/api_get_data', 'Index@get_data');
    $router->map('/api/api_add_race', 'Index@api_add_race');

    // Dispatch the request
    $url = $_SERVER['REQUEST_URI'];
    $url = str_ireplace("/index.php", "", $url);
    $router->dispatch($url);    

} else {
    die("Router class not found.");
}
?>
