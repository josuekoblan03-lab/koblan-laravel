<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Prestation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title','slug','description','price','price_type','image','gallery',
        'user_id','service_type_id','city_id','status','views',
        'rating_avg','total_reviews','total_orders',
    ];

    protected $casts = ['gallery' => 'array', 'rating_avg' => 'decimal:2'];

    protected static function booted(): void {
        static::creating(function ($p) {
            $p->slug = $p->slug ?: Str::slug($p->title) . '-' . uniqid();
        });
    }

    // ── Relations ──────────────────────────────────────────────
    public function user()        { return $this->belongsTo(User::class); }
    public function serviceType() { return $this->belongsTo(ServiceType::class); }
    public function city()        { return $this->belongsTo(City::class); }
    public function orders()      { return $this->hasMany(Order::class); }
    public function reviews()     { return $this->hasMany(Review::class); }
    public function medias()      { return $this->hasMany(Media::class)->orderBy('order'); }
    public function mainMedia()   { return $this->hasOne(Media::class)->where('is_main', true); }
    public function favoritedBy() { return $this->belongsToMany(User::class, 'favoris', 'prestation_id', 'user_id')->withTimestamps(); }
    public function orderItems()  { return $this->hasMany(OrderItem::class); }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeActive($q)                   { return $q->where('status', 'active'); }
    public function scopeByCategory($q, $categoryId)  { return $q->whereHas('serviceType', fn($st) => $st->where('category_id', $categoryId)); }
    public function scopeSearch($q, $term)            { return $q->where('title','like',"%$term%")->orWhere('description','like',"%$term%"); }

    // ── Helpers ────────────────────────────────────────────────

    /**
     * Normalise une chaîne pour la comparer : minuscules, sans accents, 
     * espaces et caractères spéciaux remplacés par des tirets.
     */
    private static function normalizeForMatch(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        // Remplacer les caractères accentués
        $from = ['à','â','ä','é','è','ê','ë','î','ï','ô','ö','ù','û','ü','ç','ñ','æ','œ'];
        $to   = ['a','a','a','e','e','e','e','i','i','o','o','u','u','u','c','n','ae','oe'];
        $str = str_replace($from, $to, $str);
        // Remplacer parenthèses, apostrophes et ponctuations par des espaces
        $str = preg_replace('/[()\'",!?;:\/\\\\]/', ' ', $str);
        // Remplacer les espaces multiples par un tiret
        $str = preg_replace('/\s+/', '-', trim($str));
        // Supprimer les caractères non alphanumériques restants (sauf tirets)
        $str = preg_replace('/[^a-z0-9\-]/', '', $str);
        return $str;
    }

    /**
     * Retourne l'URL de l'image de la prestation.
     * Ordre de priorité :
     *  1. Champ `image` en BDD (upload direct)
     *  2. Media principal associé
     *  3. Image locale dans public/images/services/ dont le nom contient
     *     les mots-clés principaux du titre de la prestation
     *  4. Image Unsplash thématique par catégorie/titre (fallback externe)
     *  5. Image par défaut locale
     */
    public function getImageUrl(): string
    {
        // 1. Image uploadée directement
        if ($this->image) {
            // URL externe (Cloudinary etc.)
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            // Fichier local — vérifie qu'il existe encore
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
                return asset('storage/' . $this->image);
            }
        }

        // 2. Media principal
        $main = $this->mainMedia;
        if ($main && $main->url) {
            // URL externe (Cloudinary etc.)
            if (str_starts_with($main->url, 'http')) {
                return $main->url;
            }
            // Fichier local — vérifie qu'il existe encore
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($main->url)) {
                return asset('storage/' . $main->url);
            }
        }

        // 3. Recherche dans public/images/services/
        $servicesDir = public_path('images/services');
        if (is_dir($servicesDir)) {
            $normalizedTitle = self::normalizeForMatch($this->title);
            // Extraire les mots significatifs du titre (longueur >= 4 lettres)
            $titleWords = array_filter(
                explode('-', $normalizedTitle),
                fn($w) => strlen($w) >= 4
            );

            $files = scandir($servicesDir);
            $bestFile = null;
            $bestScore = 0;

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) continue;

                $normalizedFile = self::normalizeForMatch(pathinfo($file, PATHINFO_FILENAME));
                $score = 0;
                foreach ($titleWords as $word) {
                    if (str_contains($normalizedFile, $word)) {
                        $score++;
                    }
                }
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestFile = $file;
                }
            }

            // Accepter un fichier si au moins 1 mot-clé correspond
            if ($bestFile && $bestScore >= 1) {
                return asset('images/services/' . rawurlencode($bestFile));
            }
        }

        // 4. Fallback Unsplash par mots-clés du titre / catégorie
        $title = mb_strtolower($this->title, 'UTF-8');
        $cat   = mb_strtolower($this->serviceType?->category?->name ?? '', 'UTF-8');

        // Supprimer les accents pour les comparaisons
        $titleN = self::normalizeForMatch($title);
        $catN   = self::normalizeForMatch($cat);

        if (str_contains($titleN, 'coiff') || str_contains($catN, 'beaut')) {
            return 'https://images.unsplash.com/photo-1562322140-8baeececf3df?q=80&w=800&auto=format&fit=crop';
        }
        if (str_contains($titleN, 'plomb') || str_contains($catN, 'bricolage')) {
            return 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?q=80&w=800&auto=format&fit=crop';
        }
        if (str_contains($titleN, 'elec') || str_contains($catN, 'bricolage')) {
            return 'https://images.unsplash.com/photo-1621905252507-b35492d90cb0?q=80&w=800&auto=format&fit=crop';
        }
        if (str_contains($titleN, 'menage') || str_contains($catN, 'nettoyage')) {
            return 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?q=80&w=800&auto=format&fit=crop';
        }
        if (str_contains($titleN, 'jardin') || str_contains($catN, 'vert')) {
            return 'https://images.unsplash.com/photo-1416879598555-22b311740d7c?q=80&w=800&auto=format&fit=crop';
        }
        if (str_contains($titleN, 'enfant') || str_contains($titleN, 'nounou') || str_contains($catN, 'enfant')) {
            return 'https://images.unsplash.com/photo-1537655780520-1e392ead81f2?q=80&w=800&auto=format&fit=crop';
        }
        if (str_contains($titleN, 'cuis') || str_contains($titleN, 'traiteur') || str_contains($catN, 'cuisine')) {
            return 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?q=80&w=800&auto=format&fit=crop';
        }
        if (str_contains($titleN, 'info') || str_contains($titleN, 'reseau') || str_contains($catN, 'tech')) {
            return 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=800&auto=format&fit=crop';
        }
        if (str_contains($titleN, 'demenag') || str_contains($catN, 'transport')) {
            return 'https://images.unsplash.com/photo-1600518464441-9154a4dea21b?q=80&w=800&auto=format&fit=crop';
        }

        // 5. Image par défaut locale
        return asset('images/default-service.jpg');
    }

    public function getFormattedPrice(): string {
        return number_format($this->price, 0, ',', ' ') . ' FCFA';
    }
}
