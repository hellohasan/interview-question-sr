<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    /**
     * @var array
     */
    protected $fillable = [
        'title', 'sku', 'description',
    ];

    /**
     * @return mixed
     */
    public function variants() {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    /**
     * @return mixed
     */
    public function prices() {
        return $this->hasMany(ProductVariantPrice::class, 'product_id');
    }
}
