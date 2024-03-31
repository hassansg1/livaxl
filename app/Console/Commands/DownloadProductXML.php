<?php

namespace FleetCart\Console\Commands;

use FleetCart\TempProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DownloadProductXML extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productxml:download';

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
        // $xml = file_get_contents(public_path('product-test.xml'));
        $xml = file_get_contents('http://transport.productsup.io/4f373569130aa7bb51c5/channel/188215/vidaXL_uk_dropshipping.xml');
        // file_put_contents(public_path('products.xml'), $xml);

        ini_set('memory_limit', '-1');

        $xmlObject = simplexml_load_string($xml);
        $json = json_encode($xmlObject);
        $phpArray = json_decode($json, true);

        file_put_contents(public_path("products.json"), json_encode($phpArray['product']));

        return Command::SUCCESS;
    }
}
