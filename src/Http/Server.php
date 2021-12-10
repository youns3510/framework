<?php

namespace My_MVC\Http;

use My_MVC\Exceptions\Whoops;
use My_MVC\Session\Session;

class Server
{


 private function __construct()
 {
 }

 /* 
 check if server has key
 @return bool
  */
 public static function has($key)
 {
  return isset($_SERVER[$key]);
 }
 /* if server has key rutun value */
 public static function get($key)
 {
  return Server::has($key) ? $_SERVER[$key] : null;
 }


 /* Get path info */

 public static function path_info($path)
 {
  return pathinfo($path);
 }
 /*
 get all Server data
 
  */
 public static function all()
 {
  return $_SERVER;
 }
}
