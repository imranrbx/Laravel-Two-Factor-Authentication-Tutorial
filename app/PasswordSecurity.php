<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class PasswordSecurity extends Model
{
   protected $guarded = [];

   public function user(){
   	return $this->belongsTo('App\User');
   }

}
