<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Product extends Model
{
   protected $connection = 'mysql';
   protected $table = 'product';
   protected $primaryKey = 'id_product';
   protected $fillable = [
      'id_user',
      'id_category',
      'name_product',
      'price',
      'url_logo',
      'status_active',
   ];
   public $timestamps = true;
   protected $guarded = [];

   
   public function item() {
      return $this->hasMany(ProductAddOn::class, 'id_product', 'id_product');
   }

}
