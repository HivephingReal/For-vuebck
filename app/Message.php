<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    protected $table='message';
    public $fillable=['com_id','message','post_id','user_id','from_user'];

}
