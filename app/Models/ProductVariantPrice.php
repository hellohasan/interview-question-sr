<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model {
    /**
     * @var array
     */
    protected $guarded = [''];

    /**
     * @return mixed
     */
    public function variantOne() {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_one');
    }

    /**
     * @return mixed
     */
    public function variantTwo() {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_two');
    }

    /**
     * @return mixed
     */
    public function variantThree() {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_three');
    }
}
