<?php

namespace My_MVC\Router;

use My_MVC\Http\Request;

class Route
{

 private static $routes = [];
 private static $middleware;
 private static $prefix;

 private function __construct()
 {
 }


 public  static function invoke($route, $params)
 {

  // check middleware
  static::executeMiddleware($route);
  $callback = $route['callback'];
  if (is_callable($callback)) {
   call_user_func($callback, $params);
  } elseif (strpos($callback, '@') !== false) {
   list($controller, $method) = explode('@', $callback);
   $controller = 'App\Controllers\\' . $controller;
   if (class_exists($controller)) {

    $object = new $controller;

    if (method_exists($object, $method)) {

     return call_user_func_array([$object, $method], $params=[]);
    } else {
     throw new \BadFunctionCallException("The Method " . $method . " is not exist at " . $controller);
    }
   } else {
    throw new \ReflectionException("Class " . $controller . " is  not found ");
   }
  } else {
   throw new \BadFunctionCallException("please provide valid callback function ");
  }
 }


 public static function executeMiddleware($route)
 {
  foreach (explode('|', $route['middleware']) as $middleware) {
   if (!empty($middleware)) {
    $middleware = 'App\Middleware\\' . $middleware;
    if (class_exists($middleware)) {
     $object = new  $middleware;
      call_user_func_array([$object, 'handle'], []);
    } else {
     throw new \ReflectionException("Class " . $middleware . " is  not found ");
    }
   }
  }
 }


 public  static function add($methods, $uri, $callback)
 {
  $uri = rtrim(static::$prefix . '/' . trim($uri, '/'), '/');
  $uri  = $uri ?: '/';

  foreach (explode('|', $methods) as $method) {
   static::$routes[] = [
    'uri' => $uri,
    'method' => $method,
    'callback' => $callback,
    'middleware' => static::$middleware
   ];
  }
 }



 public static function get($uri, $callback)
 {
  static::add('GET', $uri, $callback);
 }



 public static function post($uri, $callback)
 {
  static::add('POST', $uri, $callback);
 }


 public static function any($uri, $callback)
 {
  static::add('GET|POST', $uri, $callback);
 }

 // public static function allRoutes()
 // {
 //  return static::$routes;
 // }


 public static function prefix($prefix, $callback)
 {
  $parent_prefix = static::$prefix;
  static::$prefix .= '/' . trim($prefix, '/');
  if (is_callable($callback)) {
   call_user_func($callback);
  } else {
   throw new \BadFunctionCallException("please provide valid callback function ");
  }

  static::$prefix = $parent_prefix;
 }


 public static function middleware($middleware, $callback)
 {
  $parent_middleware = static::$middleware;
  static::$middleware .=  '|' . trim($middleware, '|');
  if (is_callable($callback)) {
   call_user_func($callback);
  } else {
   throw new \BadFunctionCallException("please provide valid callback function ");
  }

  static::$middleware = $parent_middleware;
 }






 public  static function handle()
 {
  $matched = true;
  $uri = Request::url();
  foreach (static::$routes as $route) {
   $route['uri'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['uri']);
   $route['uri'] = '#^' . $route['uri'] . '$#';

   if (preg_match($route['uri'], $uri, $matches)) {
    array_shift($matches);
    $params = array_values($matches);


    foreach ($params as $param) {
     if (strpos($param, '/')) {
      $matched = false;
     }
    }

    if ($route['method'] !== Request::method()) {
     $matched = false;
    }
    if ($matched == true) {
     // die('matched');
     return static::invoke($route, $param);
    } else {
     die('Not Found Page.');
    }
   }
  }


  die('Not Found Page.');
 }
}
