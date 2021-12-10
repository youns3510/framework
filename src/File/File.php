<?php

namespace My_MVC\File;

class File
{
 private function __construct()
 {
 }

 // root path
 public  static function root()
 {
  return ROOT;
 }
 // directory separator
 public  static function ds()
 {
  return DS;
 }

 public  static function path($path)
 {
  $path  = static::root() . static::ds() . trim($path, '/');
  $path = str_replace(['/', '\\'], static::ds(), $path);

  return $path;
 }

 public static function exist($path)
 {
  return file_exists(static::path($path));
 }


 public static function require_file($path)
 {
  if (static::exist($path)) {
  
   return require_once(static::path($path)) ;
   
  }
 }



 public static function include_file($path)
 {
  if (static::exist($path)) {
   return include(static::path($path));
  
  }
 }





 public static function require_directory($path)
 {
  if (static::exist($path)) {

   $files = array_diff(scandir(static::path($path)), ['.', '..']);
   foreach ($files as $file) {
    $file_path = $path . static::ds() . $file;
    if (static::exist($file_path)) {
     static::require_file($file_path);
    }
   }
  }
 }
}
