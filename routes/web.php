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

$router->group(['prefix' => 'v1'], function () use ($router) {

    // Customer
    $router->get('/customer', 'CustomerController@index');
    $router->get('/customer/{id}', 'CustomerController@show');
    $router->post('/customer', 'CustomerController@store');
    $router->put('/customer/{id}', 'CustomerController@update');
    $router->delete('/customer/{id}', 'CustomerController@destroy');

    // Product
    $router->get('/product', 'ProductController@index');
    $router->get('/product/{id}', 'ProductController@show');
    $router->post('/product', 'ProductController@store');
    $router->put('/product/{id}', 'ProductController@update');
    $router->delete('/product/{id}', 'ProductController@destroy');

    // Order
    $router->get('/order', 'OrderController@index');
    $router->get('/order/{id}', 'OrderController@show');
    $router->post('/order', 'OrderController@store');
    $router->put('/order/{id}', 'OrderController@update');
    $router->delete('/order/{id}', 'OrderController@destroy');

    // Order Item
    $router->get('/order-item', 'OrderItemController@index');
    $router->get('/order-item-detail', 'OrderItemController@indexJoin');
    $router->get('/order-item/{id}', 'OrderItemController@show');
    $router->get('/order-item-detail/{id}', 'OrderItemController@showJoin');
    $router->post('/order-item', 'OrderItemController@store');
    $router->put('/order-item/{id}', 'OrderItemController@update');
    $router->delete('/order-item/{id}', 'OrderItemController@destroy');

    // Payment
    $router->get('/payment', 'PaymentController@index');
    $router->get('/payment-detail', 'PaymentController@indexJoin');
    $router->get('/payment/{id}', 'PaymentController@show');
    $router->get('/payment-detail/{id}', 'PaymentController@showJoin');
    $router->post('/payment', 'PaymentController@store');
    $router->put('/payment/{id}', 'PaymentController@update');
    $router->delete('/payment/{id}', 'PaymentController@destroy');
    $router->post('/payment/midtrans/push', 'PaymentController@midtransPush');
});
