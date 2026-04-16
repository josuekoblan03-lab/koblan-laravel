<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'titre', 'contenu', 'url_media', 'type_media',
        'categorie', 'user_id', 'statut', 'vues', 'nb_likes'
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function comments() { return $this->hasMany(ArticleComment::class)->with('user')->orderBy('created_at', 'asc'); }
    public function likes()    { return $this->hasMany(ArticleLike::class); }
    public function likedByUser($userId) { return $this->likes()->where('user_id', $userId)->exists(); }

    public function scopePublie($q) { return $q->where('statut', 'publie'); }

    public function getExtraitsAttribute() {
        return \Str::limit(strip_tags($this->contenu), 120);
    }
}
