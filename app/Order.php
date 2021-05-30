<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\OrderDetail;

class Order extends Model
{
    use  HasFactory;

    public $table = 'orders';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'branch_id',
        'total_price',
        'order_date',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function getOrderDetails()
    {
        $details   = DB::table('order_details')->where("order_id", $this->id)->where("deleted_at",NULL)->get();
        foreach($details as $detail){
            $asset=Asset::withTrashed()->find($detail->asset_id);  
            if(isset($asset->deleted_at)){
                $asset->name.="<i class='color-red'>(deleted)</i>";
            }    
            if ($asset->getStock()<$detail->quantity && $this->status=="Open") {
                $asset->name.="<i class='color-red'>(insufficient stock)</i>";
            }
            $detail->_asset=$asset;
        }
     
        return $details;
    }
    public function addOrderDetail($assetId, $qty)
    { 
        $asset=Asset::withTrashed()->find($assetId);
       // $totalPrice=$asset->price_sell*$qty;
        $orderDetail=OrderDetail::updateOrCreate([
            'asset_id'=>$assetId,
            'order_id'=>$this->id
        ], [
            'quantity'=>$qty,
            'price_sell'=>$asset->price_sell,
            'price_total'=>0
        ]);
    }
    public function removeOrderDetail($assetId){
        $id   = DB::table('order_details')->where("asset_id",$assetId)
                   ->where("order_id",$this->id)
                   ->value("id");
        $orderDetail= OrderDetail::find($id);
        if(isset($orderDetail)){
            $orderDetail->delete();
            return true;
        }else{
            return false;
        }
        
    }
    public function getTotalPrice(){
        $details   = DB::table('order_details')->where("order_id", $this->id)->where("deleted_at",NULL)->get();
        $totalPrice=0;
        foreach ($details as $detail) {
            $totalPrice+=$detail->price_sell*$detail->quantity;
        }
        return $totalPrice;
    }
}
