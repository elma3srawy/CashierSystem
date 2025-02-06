<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;
    protected $fillable = [
        'section_id' , 'name' , 'title' , 'status'
    ];


    public function subSection()
    {
        return $this->hasMany(Section::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeRentSections($query)
    {
        return $query->where('status', 1);
    }
    public function scopeSaleSections($query)
    {
        return $query->where('status', 0);
    }

}
