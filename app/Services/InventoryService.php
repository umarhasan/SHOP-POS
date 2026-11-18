<?php
namespace App\Services;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function move(Product $product, ?int $variantId, float $quantity, string $direction, string $referenceType, ?int $referenceId, float $unitCost = 0, string $note = ''): void
    {
        if (!$product->track_stock) return;
        $balance = StockBalance::query()->firstOrCreate(['product_id'=>$product->id,'product_variant_id'=>$variantId], ['quantity'=>0,'avg_cost'=>0]);
        $balance = StockBalance::query()->whereKey($balance->id)->lockForUpdate()->first();
        $delta = $direction === 'in' ? $quantity : -$quantity;
        if ($direction === 'out' && (float)$balance->quantity < $quantity) {
            throw ValidationException::withMessages(['stock' => "Insufficient stock for {$product->name}. Available: {$balance->quantity}"]);
        }
        $oldQty = (float)$balance->quantity;
        if ($direction === 'in' && ($oldQty + $quantity) > 0) {
            $balance->avg_cost = (($oldQty * (float)$balance->avg_cost) + ($quantity * $unitCost)) / ($oldQty + $quantity);
        }
        $balance->quantity = $oldQty + $delta;
        $balance->save();
        DB::table('stock_movements')->insert([
            'product_id'=>$product->id,'product_variant_id'=>$variantId,'reference_type'=>$referenceType,'reference_id'=>$referenceId,
            'direction'=>$direction,'quantity'=>$quantity,'unit_cost'=>$unitCost,'unit_name'=>$product->baseUnit->short_name,
            'note'=>$note,'user_id'=>auth()->id() ?? 1,'created_at'=>now(),'updated_at'=>now(),
        ]);
    }
}
