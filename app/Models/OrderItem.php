<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'prestation_id', 'unit_price', 'quantity'];
    protected $casts    = ['unit_price' => 'decimal:2'];

    public function order()      { return $this->belongsTo(Order::class); }
    public function prestation() { return $this->belongsTo(Prestation::class); }
}
