<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App;

use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{

    /**
     * Setup the middleware your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middleware The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware.
     */
    public function middleware($middleware)
    {
        $middleware
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error.exceptionRenderer')))
            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware())
            // Apply routing
            ->add(new RoutingMiddleware());
        $middleware->push(new \ADmad\Glide\Middleware\GlideMiddleware([
            // Run this filter only for URLs matching specified value. If unset the
            // value of `server.base_url` config mentioned below will be used.
            // http://book.cakephp.org/3.0/en/development/dispatch-filters.html#conditionally-applying-filters
            'scope' => null,
            // Either an instance of League\Glide\Server or config array to be used to
            // create server instance.
            // http://glide.thephpleague.com/1.0/config/setup/
            'server' => [
                // Path or League\Flysystem adapter instance to read images from.
                // http://glide.thephpleague.com/1.0/config/source-and-cache/
                'source' => WWW_ROOT . 'upload',
                // Path or League\Flysystem adapter instance to write cached images to.
                'cache' => WWW_ROOT . 'cache',
                // URL part to be omitted from source path. Defaults to "/images/"
                // http://glide.thephpleague.com/1.0/config/source-and-cache/#set-a-base-url
                'base_url' => '/imgs/',
                // Response class for serving images. If unset (default) an instance of
                // \ADmad\Glide\Responses\PsrResponseFactory() will be used.
                // http://glide.thephpleague.com/1.0/config/responses/
                'response' => null,
//                'watermarks' => WWW_ROOT.'img',
                'watermarks' => new \League\Flysystem\Filesystem(new \League\Flysystem\Adapter\Local(WWW_ROOT . 'img')),
//                'watermarks_path_prefix' => 'img/watermarks', // optional
            ],
            // http://glide.thephpleague.com/1.0/config/security/
            'security' => [
                // Boolean indicating whether secure URLs should be used to prevent URL
                // parameter manipulation. Default false.
                'secureUrls' => false,
                // Signing key used to generate / validate URLs if `secureUrls` is `true`.
                // If unset value of Cake\Utility\Security::salt() will be used.
                'signKey' => null,
            ],
            // If set to `true` exception generated when trying to get image from Glide
            // will be ignored and request will be allowed to proceed. Default false.
            'ignoreException' => false,
            // Cache duration. Default '+1 days'.
            'cacheTime' => '+1 days',
            // Any response headers you may want to set. Default null.
            'headers' => [
                'X-Custom' => 'some-value',
            ]
        ]));

        return $middleware;
    }

}
