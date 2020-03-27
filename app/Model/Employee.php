<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function roster()
   {
       return $this->hasMany('App\Model\RosterShift','employee_id','employee_id');
   }

}
