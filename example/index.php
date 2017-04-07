<?php

use Han\Router\Router;

require_once __DIR__ . '/../Han/Router/Route.php';
require_once __DIR__ . '/../Han/Router/Routepart.php';
require_once __DIR__ . '/../Han/Router/Router.php';

// router init
$router = new Router();

// adding routes
$router->addRoute('home', '/', ExampleController::class, 'index', 'GET');

//user in a REST way
$router->addRoute('user', '/user', UserController::class, 'user', 'GET');
$router->addRoute('newUserForm', '/user/new', UserController::class, 'newUserForm', 'GET');
$router->addRoute('newUserData', '/user/new', UserController::class, 'newUserData', 'POST');
$router->addRoute('showUser', '/user/{$username}', UserController::class, 'showUser', 'POST');
$router->addRoute('deleteUser', '/user/{$username}', UserController::class, 'deleteUser', 'DELETE');
$router->addRoute('editUserForm', '/user/{$username}/edit', UserController::class, 'editUserForm', 'GET');
$router->addRoute('editUserData', '/user/{$username}/edit', UserController::class, 'editUserData', 'PUT');

// actual routing
// param $_GET['path'] is set by apache in this example, look in the .htaccess file
$router->route(!empty($_GET['path']) ? $_GET['path'] : '/');

class ExampleController
{
    public function index()
    {
        echo 'index page';
        return true;
    }
}

class UserController
{
    public function user()
    {
        echo 'Show all users';
        return true;
    }

    public function newUserForm()
    {
        echo 'New user form';
        return true;
    }

    public function newUserData()
    {
        echo 'Post route for adding new user';
        return true;
    }

    public function showUser($username)
    {
        echo "Show user {$username}";
        return true;
    }

    public function deleteUser($username)
    {
        echo "Delete user {$username}";
        return true;
    }

    public function editUserForm($username)
    {
        echo "Edit form for user {$username}";
        return true;
    }

    public function editUserData($username)
    {
        echo "Edit POST route for user {$username}";
        return true;
    }
}