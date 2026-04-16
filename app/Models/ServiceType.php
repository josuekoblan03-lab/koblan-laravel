<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['name','category_id'];

    public function category()    { return $this->belongsTo(Category::class); }
    public function prestations() { return $this->hasMany(Prestation::class); }
}
