<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class OrderProduct extends Model
{
   protected $connection = 'mysql';
   protected $table = 'order_product';
   protected $primaryKey = 'id_order_product';
   protected $fillable = [
         'id_order',
        'id_product',
        'id_product_add_on',
        'qty',
        'notes',
        'amount',
        'total_amount',
        'status_active',
   ];
   public $timestamps = true;
   protected $guarded = [];
}
