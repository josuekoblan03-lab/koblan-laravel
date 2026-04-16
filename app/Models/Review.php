<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['order_id','client_id','prestataire_id','prestation_id','rating','comment','is_visible'];
    protected $casts    = ['is_visible' => 'boolean'];

    public function order()      { return $this->belongsTo(Order::class); }
    public function client()     { return $this->belongsTo(User::class, 'client_id'); }
    public function prestataire(){ return $this->belongsTo(User::class, 'prestataire_id'); }
    public function prestation() { return $this->belongsTo(Prestation::class); }

    public function scopeVisible($q) { return $q->where('is_visible', true); }
}
