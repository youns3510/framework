<?php

namespace My_MVC\Validation;

use My_MVC\Http\Request;
use My_MVC\Session\Session;
use My_MVC\Url\Url;
use Rakit\Validation\Validator;

class Validate
{

 private function __construct()
 {
 }

 /* 
validate 
array $rulles
bool $json 

*/
 public function validate(array $rules, $json)
 {
  $validator = new Validator;

  $validation = $validator->validate($_POST + $_FILES, $rules);



  if ($validation->fails()) {

   // handling errors
   $errors = $validation->errors();

   if ($json) {
    return ['errors' => $errors->firstOfAll()];
   } else {
    Session::set('errors', $errors);
    Session::set('old',Request::all());

    return Url::redirect(url::previous());
   }


  } else {


   // validation passes
   echo "Success!";

   
  }
 }
}
