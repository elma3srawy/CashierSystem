<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
