<?php

namespace FleetCart\Console\Commands;

use Illuminate\Console\Command;

class UpdateTempAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tempattributes:update';

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
        $attributesArray = json_decode(file_get_contents('attributes.json'), true);
        $data_chunk = array_chunk($attributesArray, 500);

        foreach ($data_chunk as $data) {
            foreach ($data as $attributes) {

                $variant_code = trim($attributes['variant_code']);
                $sku = trim($attributes['sku']);



                foreach ($attributes as $attribute_name => $attribute_value) {
                    if(str_starts_with($attribute_name, 'variant_attribute_name_')){
                        $parts = explode("_", $attribute_name);
                        $index = end($parts);

                        $attr_name = array_key_exists("variant_attribute_name_$index", $attributes) ? $attributes["variant_attribute_name_$index"] : "";
                        $attr_value = array_key_exists("variant_attribute_value_$index", $attributes) ? $attributes["variant_attribute_value_$index"] : "";


                        $temp_attribute = TempAttribute::where("variant_code", $variant_code)
                                            ->where("sku", $sku)
                                            ->where("attribute_name", $attr_name)
                                            ->where("attribute_value", $attr_value)
                                            ->first();

                        if(!$temp_attribute){

                            $temp_attribute = new TempAttribute();
                            $temp_attribute->variant_code = $variant_code;
                            $temp_attribute->attribute_name = $attr_name;
                            $temp_attribute->attribute_value = $attr_value;
                            $temp_attribute->sku = $sku;
                            $temp_attribute->save();
                        }else{
                            // update temp attribute value
                        }
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
