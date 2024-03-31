<?php

namespace FleetCart\Console\Commands;

use FleetCart\TempProduct;
use Illuminate\Console\Command;
use Modules\Product\Entities\Product;
use Illuminate\Support\Str;
use Validator;
use DB;
use FleetCart\TempAttribute;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Modules\Category\Entities\Category;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Entities\File;
use Modules\Product\Entities\ProductVariant;
use Modules\Variation\Entities\Variation;
use Modules\Variation\Entities\VariationValue;

class SyncProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        \Log::info("Product Sync Started...");
        $temp_products = TempProduct::where('synced', false)->get()->take(1000);

        foreach ($temp_products as $temp_product) {

            $existing_product = Product::where("sku", $temp_product->sku)->first();
            if(!$existing_product){
                // if not already in db then check if it is a variation.
                $temp_attribute = TempAttribute::where('sku', $temp_product->sku)->first();
                $all_variation_sku = TempAttribute::select('sku')
                    ->where('variant_code', $temp_attribute ? $temp_attribute->variant_code : null)
                    ->distinct()
                    ->pluck('sku')
                    ->toArray();
                $exists = Product::whereIn('sku', $all_variation_sku)->first();
                if(!$exists){
                    $this->createProduct($temp_product);
                }
            }else{
                // update product

                $this->updateProduct($existing_product, $temp_product);
            }

        }
        \Log::info("Product Sync Completed");
        return Command::SUCCESS;
    }

    public function createProduct($temp_product){

        // Create Categories if not exists
        $product_categories = [];
        $categories = explode(">", $temp_product->categories);
        foreach ($categories as $i => $cat) {
            $category = trim($cat);
            $slug = Str::slug($category);

            $parent = null;
            if($i > 0){
                $prev_category_slug = Str::slug($categories[$i-1]);
                $parent = Category::where("slug", $prev_category_slug)->first();
            }

            $current = Category::where("slug", $slug)->first();

            if(!$current){
                $current = Category::create([
                    'parent_id' => $parent ? $parent->id : null,
                    'name' => $category,
                    'slug' => $slug,
                    'on_navigation' => false,
                    'is_active' => true,
                    'is_searchable' => false
                ]);
            }
            array_push($product_categories, $current->id);
        }

        try{

            $slug = Str::slug($temp_product->name);
            $imageUrls = json_decode($temp_product->images);

            $additional_images = [];
            $base_image = null;
            foreach ($imageUrls as $i => $imageUrl) {
                $url = $imageUrl;
                $contents = null;
                try{
                    $contents = file_get_contents($url);
                }catch(\Exception $e){

                }

                if($contents){
                    $filename = substr($url, strrpos($url, '/') + 1);
                    $parts = explode(".", $filename);
                    $extension = end($parts);
                    $path = "media/$filename";
                    Storage::put("media/$filename", $contents);

                    $file = File::create([
                        'user_id' => 1,
                        'disk' => config('filesystems.default'),
                        'filename' => $filename,
                        'path' => $path,
                        'extension' => $extension ?? '',
                        'mime' => "image/$extension",
                        'size' => strlen($contents),
                    ]);

                    if($i == 0){
                        $base_image = $file->id;
                    }else{
                        array_push($additional_images, $file->id);
                    }
                }
            }

            if($base_image == null && count($additional_images) > 0){
                $base_image = $additional_images[0];
            }

            $product_name = str_ireplace("vidaXL", "LivaXL", $temp_product->name);
            $price = $temp_product->b2b_price;

            $in_stock = $temp_product->stock > 0 ? 1 : 0;

            $product = Product::create([
                'name' => $product_name,
                'sku' => $temp_product->sku,
                'description' => $temp_product->html_description ?: $temp_product->description,
                // 'short_description' => $data['short_description'],
                'is_active' => $in_stock > 0 ? 1 : 0,
                // 'brand_id' => $data['brand'],
                'categories' => $product_categories,
                // 'tax_class_id' => $data['tax_class'],
                // 'tags' => $this->explode($data['tags']),
                'price' => $price,
                // 'special_price' => $data['special_price'],
                // 'special_price_type' => $data['special_price_type'],
                // 'special_price_start' => $data['special_price_start'],
                // 'special_price_end' => $data['special_price_end'],
                'manage_stock' => 1,
                'qty' => $temp_product->stock,
                'in_stock' => $in_stock, //bug fix
                'is_virtual' => false,
                // 'new_from' => $data['new_from'],
                // 'new_to' => $data['new_to'],
                // 'up_sells' => $this->explode($data['up_sells']),
                // 'cross_sells' => $this->explode($data['cross_sells']),
                // 'related_products' => $this->explode($data['related_products']),
                'files' => [
                    'base_image' => $base_image,
                    'additional_images' => $additional_images,
                ],
                // 'meta' => $this->normalizeMetaData($data),
                // 'attributes' => $this->normalizeAttributes($data),
                // 'options' => $this->normalizeOptions($data),
            ]);

            $product->categories()->sync($product_categories);

            if($base_image){
                DB::table("entity_files")->insert([
                    'file_id' => $base_image,
                    'entity_type' => Product::class,
                    'entity_id' => $product->id,
                    'zone' => 'base_image',
                ]);
            }

            foreach ($additional_images as $ad_image) {
                DB::table("entity_files")->insert([
                    'file_id' => $ad_image,
                    'entity_type' => Product::class,
                    'entity_id' => $product->id,
                    'zone' => 'additional_images',
                ]);
            }

            $temp_product->synced = true;
            $temp_product->save();

        }catch(\Exception $e){
            \Log::info("Create Error in Temp Product: $temp_product->id");
            throw $e;
        }

    }


    public function updateProduct($product, $temp_product){


        try{

            $should_update = false;
            if(floatval($product->price->amount()) != floatval($temp_product->b2b_price)){
                $should_update = true;
            }else if(intval($product->qty) != intval($temp_product->stock)){
                $should_update = true;
            }

            if($should_update == false){
                return false;
            }

            $images = DB::table("entity_files")->where('entity_type', 'Modules\Product\Entities\Product')
                                ->where('entity_id', $product->id)->get();

            $base_image = null;
            $additional_images = [];
            foreach ($images as $image) {
                if($image->zone == "base_image"){
                    $base_image = $image->file_id;
                }else{
                    array_push($additional_images, $image->file_id);
                }
            }

            if($base_image == null && count($additional_images) > 0){
                $base_image = $additional_images[0];
            }


            $product_name = str_ireplace("vidaXL", "LivaXL", $temp_product->name);
            $price = $temp_product->b2b_price;

            $in_stock = $temp_product->stock > 0 ? 1 : 0;

            $product->update([
                'name' => $product_name,
                'sku' => $temp_product->sku,
                'description' => $temp_product->html_description ?: $temp_product->description,
                'is_active' => $in_stock ? 1 : 0,
                // 'tax_class_id' => $data['tax_class'],
                'price' => $price,
                'manage_stock' => 1,
                'qty' => $temp_product->stock,
                'in_stock' => $in_stock, //bug fix
                'is_virtual' => false,
                //'media' => $additional_images
                //'files' => [
                //    'base_image' => $base_image,
                //    'additional_images' => $additional_images,
                //],
            ]);

            if($base_image){
                DB::table("entity_files")->insert([
                    'file_id' => $base_image,
                    'entity_type' => Product::class,
                    'entity_id' => $product->id,
                    'zone' => 'base_image',
                ]);
            }

            foreach ($additional_images as $ad_image) {
                DB::table("entity_files")->insert([
                    'file_id' => $ad_image,
                    'entity_type' => Product::class,
                    'entity_id' => $product->id,
                    'zone' => 'additional_images',
                ]);
            }

        }catch(\Exception $e){
            \Log::info("Update Error in Temp Product: $temp_product->id");
            throw $e;
        }

    }

}
