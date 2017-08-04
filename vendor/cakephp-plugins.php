<?php
$baseDir = dirname(dirname(__FILE__));
return [
    'plugins' => [
        'ADmad/Glide' => $baseDir . '/vendor/admad/cakephp-glide/',
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'Migrations' => $baseDir . '/vendor/cakephp/migrations/',
        'Wpadmin' => $baseDir . '/plugins/Wpadmin/'
    ]
];