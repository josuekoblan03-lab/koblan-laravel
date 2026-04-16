<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ArticleLike extends Model
{
    protected $table = 'article_likes';
    protected $fillable = ['article_id', 'user_id'];
}
