<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    protected $fillable = ['entry_date','area_id','office_id','service_id','quantity','amount','note'];
    public function area()   { return $this->belongsTo(Area::class); }
    public function office() { return $this->belongsTo(Office::class); }
    public function service(){ return $this->belongsTo(Service::class); }
}
