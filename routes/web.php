<?php

$router->get('/', function () use ($router) {
    return response()->json(
        [
            'message' => "welcome to brave API",
        ]
    );
});

$router->post('/event/create', [
     'uses' => 'UserController@create_event',
]);

$router->post('/promocode/deactivate', [
    'uses' => 'UserController@deactivate_promo_code',
]);

$router->post('/promocode/update/radius', [
    'uses' => 'UserController@update_promo_code_radius',
]);


$router->get('/promocode/active', [
    'uses' => 'ApiController@active_promo_codes',
]);

$router->get('/promocode/all', [
    'uses' => 'ApiController@all_promo_codes',
]);

$router->post('/promocode/redeem', [
    'uses' => 'ApiController@redeem_promo_code',
]);



