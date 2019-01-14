<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    //
    protected $table='for_repair';
    protected $fillable=['phone','name','confirm','quotation_type','close','description','fr_type','address','user_id','quotation','city','state','project_define_point'];
}
