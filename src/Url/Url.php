<?php
namespace My_MVC\Url;

use My_MVC\Http\Request;
use My_MVC\Http\Server;

class Url{
 private function __construct(){ }

 public  static function path($path)
 {

  return Request::baseUrl().'/'.trim($path,'/'); 
 }


 public  static function previous()
 {
  return Server::get('HTTP_REFERER'); 
 }

 public  static function redirect($path)
 {
  header('Location: '.$path); 
  exit(); 
 }
}
