<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class ActivityUser extends Model
{
   protected $connection = 'mysql';
   protected $table = 'log_user';
   protected $primaryKey = 'id_log_user';
   protected $fillable = [
        'id_user',
        'activity',
   ];
   public $timestamps = true;
   protected $guarded = [];

}
