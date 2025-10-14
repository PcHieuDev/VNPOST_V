<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = ['area_id','code','name','is_active'];
    public function area() { return $this->belongsTo(Area::class); }
}
