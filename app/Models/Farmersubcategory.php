<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmersubcategory extends Model
{
    use HasFactory;

    public function farmercategory()
    {
    	return $this->belongsTo(Farmercategory::class);
    }
}
