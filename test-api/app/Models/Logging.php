<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Logging extends Model
{
   protected $connection = 'mysql';
   protected $table = 'logging';
   protected $primaryKey = 'id_logging';
   protected $fillable = [
        'url',
        'method',
        'request_body',
        'response',
        'user_agent',
   ];
   public $timestamps = true;
   protected $guarded = [];

}
