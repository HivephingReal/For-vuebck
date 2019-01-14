<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FirebaseModel extends Model
{
    //
    public $table='firebase';
    public $fillable=['user_id','token','created_at','updated_at'];
}
