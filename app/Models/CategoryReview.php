<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryReview extends Model
{
    use HasFactory;
  
    protected $fillable = [
        'customer_name',
        'city',
        'article',
        'category',
    ];
}
