<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{BusinessUnit,Category,Unit,Product};
class ShopSeeder extends Seeder
{
 public function run(): void
 {
  foreach([['name'=>'Shopper Business','code'=>'SHOPPER','owner_name'=>'Cousin'],['name'=>'Stationery & Gifts','code'=>'STATIONERY','owner_name'=>'Shop Owner']] as $row) BusinessUnit::firstOrCreate(['code'=>$row['code']],$row);
  foreach([['name'=>'Shopper & Plastic Bags','slug'=>'shopper-plastic'],['name'=>'Packaging Material','slug'=>'packaging'],['name'=>'Stationery','slug'=>'stationery'],['name'=>'School Items','slug'=>'school-items'],['name'=>'Gifts & Toys','slug'=>'gifts-toys'],['name'=>'General Products','slug'=>'general-products']] as $row) Category::firstOrCreate(['slug'=>$row['slug']],$row);
  foreach([['name'=>'Kilogram','short_name'=>'KG','dimension'=>'weight','base_factor'=>1000],['name'=>'Gram','short_name'=>'G','dimension'=>'weight','base_factor'=>1],['name'=>'Piece','short_name'=>'PCS','dimension'=>'quantity','base_factor'=>1],['name'=>'Packet','short_name'=>'PKT','dimension'=>'quantity','base_factor'=>1],['name'=>'Dozen','short_name'=>'DOZ','dimension'=>'quantity','base_factor'=>12],['name'=>'Roll','short_name'=>'ROLL','dimension'=>'quantity','base_factor'=>1],['name'=>'Box','short_name'=>'BOX','dimension'=>'quantity','base_factor'=>1],['name'=>'Meter','short_name'=>'M','dimension'=>'length','base_factor'=>1]] as $row) Unit::firstOrCreate(['short_name'=>$row['short_name']],$row);
 }
}
