<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class ProductAddOn extends Model
{
   protected $connection = 'mysql';
   protected $table = 'product_add_on';
   protected $primaryKey = 'id_product_add_on';
   protected $fillable = [
        'id_product',
        'name_product_add_on',
        'url_logo',
        'status_active',
   ];
   public $timestamps = true;
   protected $guarded = [];
}
