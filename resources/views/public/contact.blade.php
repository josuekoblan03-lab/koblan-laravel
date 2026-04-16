@extends('layouts.app')

@section('content')

<!-- 1. HERO CONTACT 3D -->
<section class="page-header" id="contact-hero" style="position:relative;z-index:2;">
  <div class="section-container">
    <div class="section-tag reveal">📩 Assistance 24/7</div>
    <h1 class="page-header-title reveal" data-delay="0.1">
      Parlons <span class="text-gold">Ensemble</span>
    </h1>
    <p class="page-header-sub reveal" data-delay="0.2">Notre équipe dédiée est à votre disposition pour répondre à toutes vos questions et vous accompagner.</p>
  </div>
</section>

<!-- 2. PANNEAUX D'ASSISTANCE RAPIDE -->
<section style="padding:4rem 0;background:var(--dark-200);border-bottom:1px solid var(--glass-border);position:relative;z-index:2;">
  <div class="section-container">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:2rem;">
      <div class="glass-card reveal" style="padding:2rem;text-align:center;">
        <i class="fas fa-question-circle text-gold-plain" style="font-size:2.5rem;margin-bottom:1rem;display:block;"></i>
        <h3 style="font-weight:700;font-size:1.1rem;margin-bottom:0.5rem;">Centre d'Aide</h3>
        <p style="font-size:0.875rem;color:var(--gray-400);margin-bottom:1rem;">Trouvez des réponses immédiates dans notre FAQ complète.</p>
        <a href="{{ route('faq') }}" class="btn btn-outline-gold btn-sm">Consulter la FAQ</a>
      </div>
      <div class="glass-card reveal" data-delay="0.1" style="padding:2rem;text-align:center;">
        <i class="fas fa-headset text-gold-plain" style="font-size:2.5rem;margin-bottom:1rem;display:block;"></i>
        <h3 style="font-weight:700;font-size:1.1rem;margin-bottom:0.5rem;">Support Client</h3>
        <p style="font-size:0.875rem;color:var(--gray-400);margin-bottom:1rem;">Un problème avec une commande ? Nous intervenons.</p>
        <a href="#contact-form" class="btn btn-gold btn-sm">Ouvrir un ticket</a>
      </div>
      <div class="glass-card reveal" data-delay="0.2" style="padding:2rem;text-align:center;">
        <i class="fas fa-handshake text-gold-plain" style="font-size:2.5rem;margin-bottom:1rem;display:block;"></i>
        <h3 style="font-weight:700;font-size:1.1rem;margin-bottom:0.5rem;">Devenir Prestataire</h3>
        <p style="font-size:0.875rem;color:var(--gray-400);margin-bottom:1rem;">Rejoignez-nous et développez votre activité.</p>
        <a href="{{ route('register.prestataire') }}" class="btn btn-outline-gold btn-sm">S'inscrire</a>
      </div>
    </div>
  </div>
</section>

<!-- 3. FORMULAIRE DE CONTACT ET INFOS -->
<section id="contact-form" style="padding:6rem 0;position:relative;z-index:2;">
  <div class="section-container" style="display:grid;grid-template-columns:1fr 1.5fr;gap:4rem;">

    <!-- 4. INFORMATIONS DE CONTACT (Sidebar) -->
    <div class="reveal-left">
      <div class="section-tag">📍 Siège KOBLAN</div>
      <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:800;margin:1rem 0 2.5rem;">Nos <span class="text-gold">Coordonnées</span></h2>

      @php
      $contacts = [
        ['fas fa-map-marker-alt', 'Adresse Principale', 'Cocody Riviera, Abidjan', "Côte d'Ivoire 🇨🇮"],
        ['fas fa-phone-alt', 'Ligne Directe', '+225 07 00 00 00 00', 'Lun-Sam, 8h-20h GMT'],
        ['fas fa-envelope-open-text', 'Email Support', 'support@koblan.ci', 'Réponse sous 24h ouvrées'],
        ['fab fa-whatsapp', 'WhatsApp Pro', '+225 07 00 00 00 00', 'Assistance rapide textuelle'],
      ];
      @endphp
      @foreach ($contacts as [$icon, $label, $val, $sub])
      <div class="glass-card" style="display:flex;align-items:flex-start;gap:1.25rem;padding:1.5rem;margin-bottom:1.5rem;transition:all var(--transition-base);">
        <div style="width:50px;height:50px;border-radius:12px;background:linear-gradient(135deg,rgba(255,215,0,0.1),rgba(247,127,0,0.1));display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--gold-300);flex-shrink:0;">
          <i class="{{ $icon }}"></i>
        </div>
        <div>
          <div style="font-size:0.75rem;color:var(--gold-400);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.25rem;font-weight:700;">{{ $label }}</div>
          <div style="font-weight:600;font-size:1.05rem;color:var(--gray-100);">{{ $val }}</div>
          <div style="font-size:0.8rem;color:var(--gray-400);margin-top:0.25rem;">{{ $sub }}</div>
        </div>
      </div>
      @endforeach

      <!-- 5. RÉSEAUX SOCIAUX -->
      <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--glass-border);">
        <h3 style="font-family:var(--font-alt);font-weight:700;font-size:1rem;margin-bottom:1.5rem;color:var(--gray-300);">Rejoignez notre communauté</h3>
        <div class="social-links" style="gap:1rem;">
          <a href="#" class="social-link" style="width:45px;height:45px;font-size:1.1rem;"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-link" style="width:45px;height:45px;font-size:1.1rem;"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-link" style="width:45px;height:45px;font-size:1.1rem;"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-link" style="width:45px;height:45px;font-size:1.1rem;"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>

    <!-- 6. FORMULAIRE PRINCIPAL -->
    <div class="glass-card reveal" data-delay="0.2" style="padding:3rem;">
      <h2 style="font-family:var(--font-display);font-size:1.8rem;font-weight:800;margin-bottom:2rem;">Envoyer un <span class="text-gold">Message</span></h2>

      @if(session('success'))
      <div class="alert alert-success" style="margin-bottom:2rem;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
      @endif

      <form method="POST" action="{{ route('contact') }}">
        @csrf
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
          <div class="form-group" style="margin:0;">
            <label class="form-label">Nom complet</label>
            <input type="text" name="nom" class="form-control" placeholder="Ex: Jean Kouassi" required style="padding:1rem;">
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label">Adresse Email</label>
            <input type="email" name="email" class="form-control" placeholder="votre@email.com" required style="padding:1rem;">
          </div>
        </div>

        <div class="form-group" style="margin-top:1.5rem;">
          <label class="form-label">Type de demande</label>
          <div class="dropdown" style="width:100%;position:relative;">
            <select name="sujet" class="form-control" style="padding:1rem;appearance:none;" required>
              <option value="" disabled selected>Sélectionnez un sujet...</option>
              <option value="Question générale">Question générale</option>
              <option value="Support client">Support client (Commande)</option>
              <option value="Support prestataire">Support technique (Prestataire)</option>
              <option value="Signalement">Signaler un abus ou litige</option>
              <option value="Partenariat">Proposition de partenariat</option>
              <option value="Média">Presse & Médias</option>
            </select>
            <i class="fas fa-chevron-down" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);color:var(--gray-500);pointer-events:none;"></i>
          </div>
        </div>

        <div class="form-group" style="margin-top:1.5rem;">
          <label class="form-label">Numéro de Commande (Optionnel)</label>
          <input type="text" name="order_ref" class="form-control" placeholder="Ex: CMD-2026-XXXX">
        </div>

        <div class="form-group" style="margin-top:1.5rem;">
          <label class="form-label">Message détaillé</label>
          <textarea name="message" class="form-control" rows="6" placeholder="Expliquez-nous votre besoin en détail..." required style="padding:1rem;"></textarea>
        </div>

        <div style="margin-top:2rem;">
          <button type="submit" class="btn btn-gold btn-xl" style="width:100%;justify-content:center;">
            <i class="fas fa-paper-plane"></i> Envoyer le message sécurisé
          </button>
          <p style="text-align:center;font-size:0.75rem;color:var(--gray-500);margin-top:1rem;">
            Vos données sont protégées. Consultez notre <a href="#" style="color:var(--gold-400);text-decoration:underline;">politique de confidentialité</a>.
          </p>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- 7. SECTION CARTE 3D (Lieux) -->
<section class="section-with-canvas" style="padding:6rem 0;background:var(--dark-200);border-top:1px solid var(--glass-border);">
  <div class="section-canvas" id="canvas-contact-map" data-three="octahedron" data-color="0xF77F00"></div>
  <div class="section-container section-content-z" style="text-align:center;">
    <div class="section-header">
      <div class="section-tag reveal">🌍 Présence Nationale</div>
      <h2 class="section-title reveal" data-delay="0.1">KOBLAN en <span class="text-gold">Côte d'Ivoire</span></h2>
      <p class="section-desc reveal" data-delay="0.2">Notre siège est à Abidjan, mais nos prestataires couvrent l'ensemble du territoire.</p>
    </div>
    
    <div class="glass-card reveal holographic" style="max-width:900px;margin:0 auto;height:450px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;border:1px solid var(--gold-600);">
      <i class="fas fa-map-marked-alt" style="font-size:12rem;color:rgba(255,215,0,0.1);position:absolute;"></i>
      <div style="position:relative;z-index:2;background:var(--dark-50);padding:2rem;border-radius:var(--radius-md);border:1px solid var(--glass-border);box-shadow:var(--shadow-card);">
        <h4 style="font-family:var(--font-display);font-weight:800;font-size:1.2rem;margin-bottom:0.5rem;color:var(--gold-300);">Cocody, Abidjan</h4>
        <p style="font-size:0.9rem;color:var(--gray-300);margin-bottom:1.5rem;">Centre Opérationnel KOBLAN CI</p>
        <a href="https://maps.google.com" target="_blank" class="btn btn-outline-gold"><i class="fas fa-external-link-alt"></i> Ouvrir dans Google Maps</a>
      </div>
    </div>
  </div>
</section>

<!-- 8. ENGAGEMENT SUPPORT (SLA) -->
<section style="padding:6rem 0;">
  <div class="section-container">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:3rem;text-align:center;">
      <div class="reveal">
        <div style="width:70px;height:70px;border-radius:50%;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#22c55e;font-size:2rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
          <i class="fas fa-stopwatch"></i>
        </div>
        <h3 style="font-family:var(--font-alt);font-size:1.2rem;font-weight:700;margin-bottom:0.75rem;">Réponse en 24h</h3>
        <p style="color:var(--gray-400);font-size:0.9rem;">Nous nous engageons à traiter votre demande dans les plus brefs délais.</p>
      </div>
      <div class="reveal" data-delay="0.1">
        <div style="width:70px;height:70px;border-radius:50%;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);color:#3b82f6;font-size:2rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
          <i class="fas fa-shield-alt"></i>
        </div>
        <h3 style="font-family:var(--font-alt);font-size:1.2rem;font-weight:700;margin-bottom:0.75rem;">Support Sécurisé</h3>
        <p style="color:var(--gray-400);font-size:0.9rem;">Toutes vos communications sont chiffrées et confidentielles.</p>
      </div>
      <div class="reveal" data-delay="0.2">
        <div style="width:70px;height:70px;border-radius:50%;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);color:#f59e0b;font-size:2rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
          <i class="fas fa-users"></i>
        </div>
        <h3 style="font-family:var(--font-alt);font-size:1.2rem;font-weight:700;margin-bottom:0.75rem;">Équipe Locale</h3>
        <p style="color:var(--gray-400);font-size:0.9rem;">Notre support est basé à Abidjan pour mieux comprendre vos réalités.</p>
      </div>
    </div>
  </div>
</section>

<!-- 9. PREVIEW FAQ -->
<section style="padding:6rem 0;background:var(--dark-200);border-top:1px solid var(--glass-border);">
  <div class="section-container" style="display:flex;flex-wrap:wrap;align-items:center;gap:4rem;">
    <div style="flex:1;min-width:300px;" class="reveal-left">
      <h2 style="font-size:2.5rem;font-family:var(--font-display);font-weight:800;margin-bottom:1.5rem;">Questions <br><span class="text-gold">Fréquentes</span></h2>
      <p style="color:var(--gray-300);margin-bottom:2rem;font-size:1.1rem;line-height:1.7;">Avant de nous contacter, vérifiez si votre question n'a pas déjà été traitée dans notre FAQ complète.</p>
      <a href="{{ route('faq') }}" class="btn btn-outline-gold btn-lg">Accéder à la FAQ <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div style="flex:1;min-width:300px;" class="reveal">
      <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="glass-card" style="padding:1.5rem;border-left:4px solid var(--gold-300);">
          <h4 style="font-weight:700;margin-bottom:0.5rem;color:var(--gray-100);">Comment devenir prestataire ?</h4>
          <p style="font-size:0.875rem;color:var(--gray-400);">Inscrivez-vous en choisissant le rôle prestataire. Votre compte sera validé sous 48h par l'équipe admin.</p>
        </div>
        <div class="glass-card" style="padding:1.5rem;border-left:4px solid #F77F00;">
          <h4 style="font-weight:700;margin-bottom:0.5rem;color:var(--gray-100);">Les paiements sont-ils sécurisés ?</h4>
          <p style="font-size:0.875rem;color:var(--gray-400);">Oui, nous utilisons un système de tiers de confiance pour garantir que le service est rendu avant reversement.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 10. NEWSLETTER BOTTOM -->
<section style="padding:5rem 0;background:linear-gradient(45deg, var(--dark-500), var(--dark-300));">
  <div class="section-container" style="text-align:center;">
    <h3 style="font-family:var(--font-display);font-size:1.8rem;font-weight:800;margin-bottom:1rem;">Ne manquez aucune <span class="text-gold">Nouveauté</span></h3>
    <p style="color:var(--gray-400);margin-bottom:2rem;max-width:500px;margin-left:auto;margin-right:auto;">Inscrivez-vous pour recevoir nos conseils, actualités et offres exclusives par email.</p>
    <form style="display:inline-flex;width:100%;max-width:450px;gap:0.5rem;" onsubmit="event.preventDefault();">
      <input type="email" class="form-control" placeholder="Entrez votre email..." required style="flex:1;">
      <button type="submit" class="btn btn-gold">Je m'abonne</button>
    </form>
  </div>
</section>

<style>
select option { background: var(--dark-200); color: var(--gray-100); }
</style>

@endsection
