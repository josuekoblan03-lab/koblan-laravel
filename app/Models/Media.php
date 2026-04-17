<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'medias';
    protected $fillable = ['prestation_id','url','image_data','type','is_main','order'];
    protected $casts    = ['is_main' => 'boolean'];

    public function prestation() { return $this->belongsTo(Prestation::class); }

    /**
     * Retourne l'URL de l'image.
     * Si image_data est présent (stoché en base), retourne un data: URI.
     * Sinon retourne l'URL du fichier.
     */
    public function getDisplayUrl(): string
    {
        if ($this->image_data) {
            // Déjà un data URI complet
            if (str_starts_with($this->image_data, 'data:')) {
                return $this->image_data;
            }
            // Base64 brut — on reconstruit le data URI
            return 'data:image/jpeg;base64,' . $this->image_data;
        }

        if ($this->url) {
            if (str_starts_with($this->url, 'http')) return $this->url;
            return asset('storage/' . $this->url);
        }

        return asset('images/default-service.jpg');
    }

    public function getFullUrl() { return $this->getDisplayUrl(); }
}
