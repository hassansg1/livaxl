<?php

namespace FleetCart\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Product\Entities\Product;
use DB;
use FleetCart\TempAttribute;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeValue;
use Modules\Attribute\Entities\AttributeValueTranslation;
use Modules\Attribute\Entities\ProductAttribute;
use Modules\Attribute\Entities\ProductAttributeValue;

class DownloadProductAttributeXML extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productattributesxml:download';

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
        // $xml = file_get_contents(public_path('attributes.xml'));
        $xml = file_get_contents('https://transport.productsup.io/80e1fa0cd9a6c8a76fd6/channel/527672/en_b2b_variant_attribute.xml');
        // file_put_contents(public_path('attributes.xml'), $xml);

        ini_set('memory_limit', '-1');

        $xmlObject = simplexml_load_string($xml);
        $json = json_encode($xmlObject);
        $phpArray = json_decode($json, true);

        file_put_contents(public_path("attributes.json"), json_encode($phpArray['product']));
        
        return Command::SUCCESS;
    }
}
