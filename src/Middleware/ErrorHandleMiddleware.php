<?php
/**
 * 处理部分报错处理
 * @author: kebin
 * @date : 2017.06.06
 */
namespace Cake\Error\Middleware;


/**
 *
 * Error handling middleware.
 * Traps exceptions and converts them into HTML or content-type appropriate
 * error pages using the CakePHP ExceptionRenderer.
 */
class ErrorHandlMiddleware
{
    public function __invoke($request, $response, $next)
    {
        try {
            return $next($request, $response);
        } catch (\Cake\Routing\Exception\MissingControllerException $e) {

        }
    }
}
