<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'avatar',
        'bio', 'address', 'city_id', 'is_verified', 'is_active',
        'rating_avg', 'total_reviews',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_verified'       => 'boolean',
        'is_active'         => 'boolean',
        'rating_avg'        => 'decimal:2',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function city()               { return $this->belongsTo(City::class); }
    public function neighborhood()       { return $this->belongsTo(Neighborhood::class); }
    public function prestations()        { return $this->hasMany(Prestation::class); }
    public function ordersAsClient()     { return $this->hasMany(Order::class, 'client_id'); }
    public function ordersAsPrestataire(){ return $this->hasMany(Order::class, 'prestataire_id'); }
    public function reviewsReceived()    { return $this->hasMany(Review::class, 'prestataire_id'); }
    public function reviewsGiven()       { return $this->hasMany(Review::class, 'client_id'); }
    public function notifications()      { return $this->hasMany(Notification::class); }
    public function wallet()             { return $this->hasOne(Wallet::class); }
    public function messagesSent()       { return $this->hasMany(Message::class, 'sender_id'); }
    public function messagesReceived()   { return $this->hasMany(Message::class, 'receiver_id'); }
    public function favoris()            { return $this->belongsToMany(Prestation::class, 'favoris', 'user_id', 'prestation_id')->withTimestamps(); }
    public function favorites()          { return $this->favoris(); } // alias anglais
    public function loginLogs()          { return $this->hasMany(LoginLog::class); }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopePrestataires($q) { return $q->where('role', 'prestataire'); }
    public function scopeClients($q)      { return $q->where('role', 'client'); }
    public function scopeActive($q)       { return $q->where('is_active', true); }
    public function scopeVerified($q)     { return $q->where('is_verified', true); }

    // ── Helpers ────────────────────────────────────────────────
    public function isAdmin()        { return $this->role === 'admin'; }
    public function isClient()       { return $this->role === 'client'; }
    public function isPrestataire()  { return $this->role === 'prestataire'; }
    public function getAvatarUrl()   { return $this->avatar ? asset('storage/' . $this->avatar) : null; }
    public function getInitials()    { return strtoupper(mb_substr($this->name, 0, 2)); }
    public function unreadNotificationsCount() {
        return $this->notifications()->where('is_read', false)->count();
    }

    public function pushNotification($title, $message, $type = 'info', $link = '#')
    {
        return $this->notifications()->create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false
        ]);
    }
}
