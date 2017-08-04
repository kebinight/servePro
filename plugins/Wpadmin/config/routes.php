<?php
use Cake\Routing\Router;

Router::plugin(
    'Wpadmin',
    ['path' => '/wpadmin'],
    function ($routes) {
        $routes->fallbacks('DashedRoute');
    }
);
