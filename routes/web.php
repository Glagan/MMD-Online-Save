<?php

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

/**
 * /
 */
/*$router->get('/', function () use ($router) {
    return $router->app->version();
});*/

/**
 * /install
 * Remove this line after installation
 */
//$router->get('/install', ['uses' => 'InstallController@install']);

/**
 * /user
 */
$router->group(['prefix' => 'user'], function ($router) {
    $router->post('/', ['uses' => 'UserController@register']);
    $router->get('/', ['uses' => 'UserController@login']);
});

/**
 * /user/self
 */
$router->group(['prefix' => 'user/self'], function ($router) {
    $router->get('token', ['uses' => 'UserController@showToken']);
    $router->get('token/refresh', ['uses' => 'UserController@refreshToken']);

    $router->get('/', ['uses' => 'UserController@show']);
    $router->post('/', ['uses' => 'UserController@update']);
    $router->delete('/', ['uses' => 'UserController@delete']);

    $router->get('options', ['uses' => 'UserController@showOptions']);
    $router->post('options', ['uses' => 'UserController@updateOptions']);
});

/**
 * /user/self/title
 */
$router->group(['prefix' => 'user/self/title'], function ($router) {
    $router->get('/', ['uses' => 'TitleController@showAll']);
    $router->post('/', ['uses' => 'TitleController@updateAll']);
    //$router->delete('/', ['uses' => 'TitleController@deleteAll']);
});

/**
 * /user/self/title/{mangaDexId}
 */
$router->group(['prefix' => 'user/self/title/{mangaDexId}'], function ($router) {
    $router->get('/', ['uses' => 'TitleController@showSingle']);
    $router->post('/', ['uses' => 'TitleController@updateSingle']);
    //$router->delete('/', ['uses' => 'TitleController@deleteSingle']);

    //$router->get('chapters', ['uses' => 'TitleController@showChapters']);
});