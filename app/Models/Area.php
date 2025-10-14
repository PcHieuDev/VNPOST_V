<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['code','name','is_active'];
    public function offices() { return $this->hasMany(Office::class); }
}
