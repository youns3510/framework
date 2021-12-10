<?php

namespace My_MVC\Bootstrap;

use My_MVC\Exceptions\Whoops;
use My_MVC\File\File;
use My_MVC\Http\Request;
use My_MVC\Http\Response;
use My_MVC\Session\Session;
use My_MVC\Http\Server;
use My_MVC\Router\Route;

class  App
{
   /**
    * App constructor
    * @return void
    */

   private function __construct()
   {
   }

   /**
    * Run the Application
    */
   public static function run()
   {
      // Register Whoops
      Whoops::handle();
      // Start Session
      Session::start();
      // throw new \Exception("Error Processing Request", 1);
      // echo Session::set('Name','Younes');
     
      // print_r(Server::all()); 
      Request::handle();
      File::require_directory('routes');
  
     $data = Route::handle();
    Response::output($data); 
   }
}
