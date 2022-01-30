<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;

class ProductController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index() {
        $data['products'] = Product::with(['prices'])->orderBy('id', 'desc')->paginate(5);
        return view('products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create() {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        //dd($request->all());

        $request->validate([
            "title"                    => "required",
            "sku"                      => 'required|unique:products,sku',
            'description'              => 'required',
            'product_variant'          => 'required|array',
            'product_variant.*.option' => 'required',
            'product_variant.*.tags'   => 'required|array',
            'product_variant.*.tags.*' => 'required',
            'product_variant_prices'   => 'required|array',
        ]);

        $product = Product::create([
            "title"       => $request->input("title"),
            "sku"         => $request->input("sku"),
            'description' => $request->input("description"),
        ]);

        foreach (request('product_variant') as $variant) {

            $vv['product_id'] = $product->id;
            $vv['variant_id'] = $variant['option'];
            foreach ($variant['tags'] as $tag) {
                $vv['variant'] = $tag;
                ProductVariant::create($vv);
            }
        }

        foreach (request('product_variant_prices') as $price) {
            $titles = explode('/', $price['title']);

            $variantPrice['product_id'] = $product->id;
            $variantPrice['price'] = $price['price'];
            $variantPrice['stock'] = $price['stock'];

            foreach ($titles as $key => $t) {
                if (!empty($t)) {
                    if ($key == 0) {
                        $variantPrice['product_variant_one'] = ProductVariant::whereVariant($t)->whereVariantId(1)->whereProductId($product->id)->first()->id;
                    } elseif ($key == 1) {
                        $variantPrice['product_variant_two'] = ProductVariant::whereVariant($t)->whereVariantId(2)->whereProductId($product->id)->first()->id;
                    } elseif ($key == 2) {
                        $variantPrice['product_variant_three'] = ProductVariant::whereVariant($t)->whereVariantId(6)->whereProductId($product->id)->first()->id;
                    }
                }
            }

            ProductVariantPrice::create($variantPrice);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product) {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $variants = Variant::all();
        $product = Product::with(['variants', 'prices'])->find($id);
        return view('products.edit', compact('variants', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $product = Product::findOrFail($id);

        $request->validate([
            "title"                    => "required",
            "sku"                      => 'required|unique:products,sku,' . $id,
            'description'              => 'required',
            'product_variant'          => 'required|array',
            'product_variant.*.option' => 'required',
            'product_variant.*.tags'   => 'required|array',
            'product_variant_prices'   => 'required|array',
        ]);

        $product->title = $request->input("title");
        $product->sku = $request->input("sku");
        $product->description = $request->input("description");
        $product->save();

        $product->variants()->delete();
        $product->prices()->delete();

        foreach (request('product_variant') as $variant) {
            $vv['product_id'] = $product->id;
            $vv['variant_id'] = $variant['option'];
            foreach ($variant['tags'] as $tag) {
                $vv['variant'] = $tag;
                ProductVariant::create($vv);
            }
        }

        foreach (request('product_variant_prices') as $price) {
            $titles = explode('/', $price['title']);

            $variantPrice['product_id'] = $product->id;
            $variantPrice['price'] = $price['price'];
            $variantPrice['stock'] = $price['stock'];

            foreach ($titles as $key => $t) {
                if (!empty($t)) {
                    if ($key == 0) {
                        $variantPrice['product_variant_one'] = ProductVariant::whereVariant($t)->whereVariantId(1)->whereProductId($product->id)->first()->id;
                    } elseif ($key == 1) {
                        $variantPrice['product_variant_two'] = ProductVariant::whereVariant($t)->whereVariantId(2)->whereProductId($product->id)->first()->id;
                    } elseif ($key == 2) {
                        $variantPrice['product_variant_three'] = ProductVariant::whereVariant($t)->whereVariantId(6)->whereProductId($product->id)->first()->id;
                    }
                }
            }

            ProductVariantPrice::create($variantPrice);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product) {
        //
    }
}
