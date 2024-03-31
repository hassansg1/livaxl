<?php

namespace FleetCart\Console\Commands;

use FleetCart\TempProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateTempProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tempproducts:update';

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
        
        $productsArray = json_decode(file_get_contents(public_path('products.json')), true);
        $data_chunk = array_chunk($productsArray, 500);

        \Log::info("Temp Products Update Started");
        foreach ($data_chunk as $data) {
            foreach ($data as $prod) {

                $images = [];
                foreach ($prod as $key => $val) {
                    if(str_starts_with($key, "Image_")){
                        array_push($images, $val);
                    }
                }

                $slug = Str::slug($prod['Title']);
                $product = TempProduct::where('sku', $prod['SKU'])->first();
                if(!$product){
                    $product = TempProduct::create([
                        'name' => $prod['Title'],
                        'sku' => $prod['SKU'],
                        'description' => $prod['Description'],
                        'categories' => $prod['Category'],
                        "b2b_price" => $prod['B2B_price'],
                        "webshop_price" => array_key_exists('Webshop_price', $prod) ? $prod['Webshop_price'] : 0,
                        "stock" => $prod['Stock'],
                        "images" => json_encode($images),
                        "slug" => $slug,
                        "product_url" => $prod["Link"]
                    ]);
                }else{
                    $product->update([
                        'name' => $prod['Title'],
                        // 'sku' => $prod['SKU'],
                        'description' => $prod['Description'],
                        'html_description' => $prod['HTML_description'],
                        'categories' => $prod['Category'],
                        "b2b_price" => $prod['B2B_price'],
                        "webshop_price" => array_key_exists('Webshop_price', $prod) ? $prod['Webshop_price'] : 0,
                        "stock" => $prod['Stock'],
                        "images" => json_encode($images),
                        "slug" => $slug,
                        "product_url" => $prod["Link"]
                    ]);
                }

            }
        }
    
        \Log::info("Temp Product UPdated");
        return Command::SUCCESS;
    }
}
