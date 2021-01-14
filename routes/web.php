<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('register',   'UserController@register');
$router->post('login',      'UserController@login');
$router->post('logout',     'UserController@logout');

$router->group(['prefix' => 'users'], function () use ($router) {
    $router->get    ('/{id}',       'UserController@show');
    $router->post   ('/image',      'FileController@storeUserImage');
});

$router->group(['prefix' => 'tasks'], function () use ($router) {
    $router->post   ('/',                   'TaskController@store'); 
    $router->get    ('/',                   'TaskController@index'); 
    $router->get    ('/completed',          'TaskController@indexCompleted');
    $router->get    ('/uncompleted',        'TaskController@indexUncompleted');
    $router->get    ('/deleted',            'TaskController@indexDeleted');
    $router->get    ('/{id}',               'TaskController@show');
    $router->put    ('/{id}',               'TaskController@update');
    $router->put    ('/{id}/completion',    'TaskController@updateCompletion');
    $router->delete ('/{id}',               'TaskController@softDelete');
    $router->patch  ('/{id}/restore',       'TaskController@restore');
    $router->delete ('/{id}/force-delete',  'TaskController@forceDelete');
    $router->post   ('/image',              'FileController@storeTaskImage');
});