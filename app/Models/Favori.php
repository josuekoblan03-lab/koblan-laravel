<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Favori extends Model
{
    protected $fillable = ['user_id', 'prestation_id'];

    public function user()       { return $this->belongsTo(User::class); }
    public function prestation() { return $this->belongsTo(Prestation::class); }
}
