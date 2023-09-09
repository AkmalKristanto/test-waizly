<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Order extends Model
{
   protected $connection = 'mysql';
   protected $table = 'order';
   protected $primaryKey = 'id_order';
   protected $fillable = [
        'id_user',
        'no_transaction',
        'name_order',
        'amount',
        'tax',
        'service',
        'payment_method',
        'payment_status',
        'type_order',
        'total_amount',
        'status_active',
   ];
   public $timestamps = true;
   protected $guarded = [];

   public function item() {
      return $this->hasMany(OrderProduct::class, 'id_order', 'id_order');
   }
}
