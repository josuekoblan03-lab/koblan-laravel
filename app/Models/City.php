<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'department_id'];
    public function department()    { return $this->belongsTo(Department::class); }
    public function neighborhoods() { return $this->hasMany(Neighborhood::class); }
    public function users()         { return $this->hasMany(User::class); }
    public function prestations()   { return $this->hasMany(Prestation::class); }
}
