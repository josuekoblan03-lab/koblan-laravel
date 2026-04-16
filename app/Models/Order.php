<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Neighborhood;

class Order extends Model
{
    protected $fillable = [
        'reference','client_id','prestataire_id','prestation_id',
        'description','amount','status',
        'scheduled_at','client_notes','prestataire_notes','address',
    ];

    protected $casts = ['scheduled_at' => 'datetime', 'amount' => 'decimal:2'];

    protected static function booted(): void {
        static::creating(function ($order) {
            if (!$order->reference) {
                $order->reference = 'KBL-' . strtoupper(uniqid());
            }
        });
    }

    public function client()       { return $this->belongsTo(User::class, 'client_id'); }
    public function prestataire()  { return $this->belongsTo(User::class, 'prestataire_id'); }
    public function prestation()   { return $this->belongsTo(Prestation::class); }
    public function items()        { return $this->hasMany(OrderItem::class); }
    public function review()       { return $this->hasOne(Review::class); }

    public function getFormattedAmount(): string {
        $amt = $this->amount ?? 0;
        return number_format($amt, 0, ',', ' ') . ' FCFA';
    }

    public function getStatusLabel(): string {
        return match($this->status) {
            'pending'     => 'En attente',
            'accepted'    => 'Acceptée',
            'confirmed'   => 'Acceptée',
            'in_progress' => 'En cours',
            'completed'   => 'Terminée',
            'cancelled'   => 'Annulée',
            default       => $this->status,
        };
    }

    public function getStatusColor(): string {
        return match($this->status) {
            'pending'     => 'warning',
            'accepted'    => 'info',
            'confirmed'   => 'info',
            'in_progress' => 'primary',
            'completed'   => 'success',
            'cancelled'   => 'danger',
            default       => 'secondary',
        };
    }
}
