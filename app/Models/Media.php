<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'medias';
    protected $fillable = ['prestation_id','url','type','is_main','order'];
    protected $casts    = ['is_main' => 'boolean'];

    public function prestation() { return $this->belongsTo(Prestation::class); }
    public function getFullUrl() { return asset('storage/' . $this->url); }
}
