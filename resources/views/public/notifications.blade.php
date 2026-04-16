@extends('layouts.app')

@section('title', 'Notifications — KOBLAN')

@section('content')
<style>
.notif-wrapper { max-width: 900px; margin: 0 auto; padding: 8rem 1rem 2rem 1rem; color: #fff; min-height: 80vh; }
.notif-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); }
.notif-header h1 { font-size: 2.2rem; font-family: var(--font-display); font-weight: 800; color: var(--gray-100); }
.btn-read-all { background: rgba(255, 215, 0, 0.1); color: #FFD700; border: 1px solid rgba(255, 215, 0, 0.3); padding: 0.6rem 1.2rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem; }
.btn-read-all:hover { background: #FFD700; color: #000; }
.notif-card { background: rgba(25, 25, 30, 0.4); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 1.5rem; margin-bottom: 1rem; display: flex; gap: 1.5rem; align-items: flex-start; transition: all 0.3s ease; position: relative; overflow: hidden; }
.notif-card.unread { background: rgba(30, 40, 60, 0.5); border-color: rgba(59, 130, 246, 0.3); border-left: 4px solid #3b82f6; }
.notif-card:hover { transform: translateY(-3px); background: rgba(40, 40, 50, 0.5); border-color: rgba(255, 215, 0, 0.2); }
.notif-icon-box { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
.notif-card[data-type="order_status"] .notif-icon-box, .notif-card[data-type="commande"] .notif-icon-box { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.notif-card[data-type="new_message"] .notif-icon-box, .notif-card[data-type="message"] .notif-icon-box { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.notif-card[data-type="review"] .notif-icon-box, .notif-card[data-type="avis"] .notif-icon-box { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.notif-card[data-type="payment"] .notif-icon-box { background: rgba(168, 85, 247, 0.1); color: #a855f7; }
.notif-card[data-type="success"] .notif-icon-box { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.notif-card[data-type="danger"] .notif-icon-box { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.notif-card[data-type="info"] .notif-icon-box { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.default-icon-box { background: rgba(156, 163, 175, 0.1); color: #9ca3af; }
.notif-content { flex: 1; }
.notif-title { font-size: 1.1rem; font-weight: 700; color: var(--gray-100); margin-bottom: 0.3rem; display: flex; justify-content: space-between; align-items: center; }
.notif-meta { font-size: 0.75rem; color: var(--gray-500); }
.notif-desc { color: var(--gray-300); font-size: 0.95rem; line-height: 1.5; margin-top: 0.5rem; }
.notif-actions { display: flex; align-items: center; gap: 1rem; margin-top: 1rem; }
.notif-link { font-size: 0.85rem; font-weight: 600; color: #FFD700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; }
.notif-link:hover { text-decoration: underline; }
.notif-mark-read { font-size: 0.8rem; color: var(--gray-400); text-decoration: none; transition: 0.2s; background: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.3rem; }
.notif-mark-read:hover { color: #fff; }
.notif-dot { position: absolute; top: 1.5rem; right: 1.5rem; width: 10px; height: 10px; border-radius: 50%; background: #3b82f6; box-shadow: 0 0 10px #3b82f6; }
.notif-empty { text-align: center; padding: 5rem 0; border: 1px dashed rgba(255,255,255,0.1); border-radius: 20px; background: rgba(10,10,15,0.5); }
</style>

<div class="notif-wrapper">
    <div class="notif-header gs-reveal">
        <h1>Notifications</h1>
        @if($notifications->where('is_read', false)->count() > 0)
        <form action="{{ route('notifications.readAll') }}" method="POST" style="margin:0;" class="ajax-form">
            @csrf
            <button type="submit" class="btn-read-all">
                <i class="fas fa-check-double"></i> Tout marquer lu
            </button>
        </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="notif-empty gs-reveal">
            <i class="fas fa-bell-slash fa-4x" style="color:var(--gray-600); margin-bottom:1rem;"></i>
            <h2 style="font-size:1.5rem; font-weight:700;">Aucune notification</h2>
            <p style="color:var(--gray-400); margin-top:0.5rem;">Vous êtes à jour. C'est le calme plat ici.</p>
        </div>
    @else
        <div class="notif-list">
            @foreach($notifications as $n)
                @php
                    $icon = 'fas fa-info-circle';
                    $iconClass = 'default-icon-box';
                    if(in_array($n->type, ['commande', 'order_status'])) { $icon = 'fas fa-shopping-bag'; $iconClass = ''; }
                    elseif(in_array($n->type, ['message', 'new_message'])) { $icon = 'fas fa-comment-dots'; $iconClass = ''; }
                    elseif(in_array($n->type, ['avis', 'review'])) { $icon = 'fas fa-star'; $iconClass = ''; }
                    elseif(in_array($n->type, ['payment'])) { $icon = 'fas fa-wallet'; $iconClass = ''; }
                    elseif($n->type === 'success') { $icon = 'fas fa-check-circle'; $iconClass = ''; }
                    elseif($n->type === 'danger') { $icon = 'fas fa-exclamation-triangle'; $iconClass = ''; }
                    elseif($n->type === 'info') { $icon = 'fas fa-info-circle'; $iconClass = ''; }
                @endphp

                <div class="notif-card gs-card {{ $n->is_read ? '' : 'unread' }}" data-type="{{ $n->type }}">
                    
                    <div class="notif-icon-box {{ $iconClass }}">
                        <i class="{{ $icon }}"></i>
                    </div>
                    
                    <div class="notif-content">
                        <div class="notif-title">
                            {{ $n->title }}
                            <span class="notif-meta">
                                <i class="far fa-clock"></i> 
                                {{ $n->created_at->format('d M Y à H:i') }}
                            </span>
                        </div>
                        
                        <div class="notif-desc">
                            {!! nl2br(e($n->message)) !!}
                        </div>
                        
                        <div class="notif-actions">
                            @if(!empty($n->link))
                                <a href="{{ url($n->link) }}" class="notif-link">Voir les détails <i class="fas fa-arrow-right"></i></a>
                            @endif
                            
                            @if(!$n->is_read)
                                <form action="{{ route('notifications.read', $n->id) }}" method="POST" style="margin:0;" class="ajax-form">
                                    @csrf
                                    <button type="submit" class="notif-mark-read">
                                        <i class="fas fa-check"></i> Marquer comme lu
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if(!$n->is_read)
                        <div class="notif-dot"></div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('turbo:load', () => {
    if(typeof gsap !== 'undefined') {
        gsap.fromTo('.gs-reveal', 
            { y: 30, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.8, stagger: 0.1, ease: 'power2.out' }
        );
        gsap.fromTo('.gs-card', 
            { x: -30, opacity: 0 },
            { x: 0, opacity: 1, duration: 0.5, stagger: 0.05, ease: 'power2.out', delay: 0.2 }
        );
    }
});
</script>
@endsection

