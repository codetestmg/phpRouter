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
$router->addRoute('showUser', '/user/{username}', UserController::class, 'showUser', 'GET');
$router->addRoute('deleteUser', '/user/{username}', UserController::class, 'deleteUser', 'DELETE');
$router->addRoute('editUserForm', '/user/{username}/edit', UserController::class, 'editUserForm', 'GET');
$router->addRoute('editUserData', '/user/{username}/edit', UserController::class, 'editUserData', 'PUT');

// actual routing
// param $_GET['path'] is set by apache in this example, look in the .htaccess file
$router->route(!empty($_GET['path']) ? $_GET['path'] : '/');

class ExampleController
{
    public function index()
    {
        return 'index page';
    }
}

class UserController
{
    public function user()
    {
        return 'Show all users';
    }

    public function newUserForm()
    {
        return 'New user form';
    }

    public function newUserData()
    {
        return 'Post route for adding new user';
    }

    public function showUser($username)
    {
        return "Show user {$username}";
    }

    public function deleteUser($username)
    {
        return "Delete user {$username}";
    }

    public function editUserForm($username)
    {
        return "Edit form for user {$username}";
    }

    public function editUserData($username)
    {
        return "Edit POST route for user {$username}";
    }
}