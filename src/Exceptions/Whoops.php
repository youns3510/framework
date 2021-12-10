<?php

namespace My_MVC\Exceptions;

class  Whoops
{
 /**
  * Whoops constructor
  * @return void
  */

 private function __construct()
 {
 }

 /**
  * Handle the Whoops error
  */
 public static function handle()
 {
  $whoops = new \Whoops\Run;
  $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
  $whoops->register();
 }
}
