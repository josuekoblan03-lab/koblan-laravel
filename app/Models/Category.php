<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name','slug','icon','image','color','description','parent_id','is_active','order'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name) . '-' . uniqid();
            }
        });
    }

    public function serviceTypes() { return $this->hasMany(ServiceType::class); }
    public function parent()       { return $this->belongsTo(Category::class, 'parent_id'); }
    public function children()     { return $this->hasMany(Category::class, 'parent_id'); }
    public function prestations()  { return $this->hasManyThrough(Prestation::class, ServiceType::class); }
    public function scopeActive($q){ return $q->where('is_active', true)->orderBy('order'); }
}
