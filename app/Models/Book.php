<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable =[
        'book_tittle','book_detail','author_id','book_image' 
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

}
