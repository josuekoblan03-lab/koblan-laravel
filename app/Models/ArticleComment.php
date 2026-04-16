<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    protected $table = 'article_comments';
    protected $fillable = ['article_id', 'user_id', 'contenu'];

    public function user()    { return $this->belongsTo(User::class); }
    public function article() { return $this->belongsTo(Article::class); }
}
