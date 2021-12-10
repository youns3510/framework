<?php

namespace My_MVC\View;

use My_MVC\File\File;
use Jenssegers\Blade\Blade;
use My_MVC\Session\Session;

class View
{


 private function __construct()
 {
 }

 public static function render($path,$data = [])
 {
  $errors = Session::flash('errors');
  $old = Session::flash('old');
  $data = array_merge($data,['errors'=>$errors,'old'=>$old]);
  return static::baldeRender($path,$data);
 }
 /*
 render view file .php
string $path
array $data */
 public static function viewRender($path, $data = [])
 {
  $path = 'views' . File::ds() . str_replace(['/', '\\', '.'], File::ds(), $path) . '.php';
  if (!File::exist($path)) {
   throw new \Exception('the view file ' . $path . '  is not exist');
  }

  ob_start(); //['name'=>"Younes","age"=>"25"]
  extract($data); //$name="younes";
  include File::path($path);
  $content = ob_get_contents();
  ob_end_clean();
  return $content;
 }

 /* render the view file.blade.php  using blade engine
 @param string $path
 @param array $data
 @return string 
 */

 public static function baldeRender($path, $data = [])
 {
  $blade = new Blade(File::path('views'), File::path('storage/cache'));
  return $blade->make($path, $data)->render();
 }
}
