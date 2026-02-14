<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmeritem extends Model
{
    use HasFactory;

    public function images()
    {
        return $this->hasMany(FarmerImage::class, 'farmeritem_id');
    }
}
