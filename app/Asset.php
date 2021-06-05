<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use \DateTimeInterface;

class Asset extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'assets';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
        'description',
        'danger_level',
        'price_buy',
        'price_sell',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'asset_id')->latest()->take(10)->orderby("created_at","desc");
    }

    public function getStock(){
        $stock   = DB::table('stocks')->where("team_id",1)
                   ->where("asset_id",$this->id)
                   ->value("current_stock");
                
        return $stock;

    }
    public function updateStock($stock){
        $_stock   = DB::table('stocks')->where("team_id",1)
                   ->where("asset_id",$this->id)
                   ->update(["current_stock"=>$stock]);
        Transaction::create([
            'asset_id'=>$this->id,
            'stock'=>$stock,
            'team_id'=>1,
            'user_id'=>\Auth::id()
        ]);
        return $_stock;

    }

}
