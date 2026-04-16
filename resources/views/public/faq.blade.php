@extends('layouts.app')

@section('content')

@php
$faqs = [
    ['Comment créer un compte ?', "Cliquez sur \"S'inscrire\" en haut de la page. Choisissez votre rôle (client, prestataire ou les deux), remplissez vos informations et validez. C'est simple, rapide et entièrement gratuit !"],
    ['Comment fonctionne KOBLAN ?', "KOBLAN met en relation des clients avec des prestataires qualifiés en Côte d'Ivoire. Recherchez un service, consultez les profils, commandez et évaluez votre expérience en toute sécurité."],
    ["Combien coûte l'inscription ?", "L'inscription est totalement gratuite pour tous. Aucun frais caché ni abonnement. KOBLAN prend une commission de 10% uniquement sur les missions validées et réalisées."],
    ['Comment puis-je devenir prestataire ?', "Lors de l'inscription, choisissez le rôle \"Prestataire\" (ou \"Les deux\"). Remplissez votre profil et ajoutez au moins une prestation. Notre équipe validera votre compte sous 24h."],
    ['Quels sont les moyens de paiement acceptés ?', 'Nous centralisons les paiements pour garantir la sécurité. Vous pouvez payer via Mobile Money (Wave, MTN, Orange), carte bancaire classique, ou en espèces selon les cas particuliers.'],
    ["Comment fonctionne le système d'évaluation ?", "Après qu'une prestation est marquée \"Terminée\", le client est invité à laisser une note sur 5 étoiles et un commentaire. Ces avis sont publics pour aider la communauté."],
    ["Que faire en cas d'insatisfaction ?", "Si un service s'est mal déroulé, vous pouvez ouvrir un litige dans votre espace client. Le paiement du prestataire est alors suspendu le temps que notre équipe de médiation intervienne."],
    ['Puis-je modifier ma demande de service ?', "Une commande peut être modifiée tant qu'elle est \"En attente\". Une fois acceptée par le prestataire, contactez-le directement via le chat intégré ou le support KOBLAN."],
    ['Mes données sont-elles sécurisées ?', 'Absolument. Nous utilisons des protocoles cryptographiques avancés (HTTPS, mots de passe chiffrés) et ne revendons aucune donnée. Notre architecture Laravel est hautement sécurisée.'],
    ['Où puis-je trouver un service particulier ?', "Utilisez la barre de recherche avec des mots-clefs ou parcourez notre index de catégories. Si vous ne trouvez pas votre bonheur, contactez-nous pour suggérer son ajout."],
];
@endphp

<!-- 1. HERO FAQ -->
<section class="page-header" id="faq-hero" style="padding-bottom:2rem;">
  <div class="section-container" style="position:relative;z-index:2;">
    <div class="section-tag reveal">📚 Support & Aide</div>
    <h1 class="page-header-title reveal" data-delay="0.1">
      Tout Ce Que Vous <span class="text-gold">Devez Savoir</span>
    </h1>
    <p class="page-header-sub reveal" data-delay="0.2">Les réponses claires à vos questions fréquentes concernant l'utilisation de KOBLAN.</p>
    
    <!-- 2. RECHERCHE RAPIDE FAQ -->
    <div class="search-hero reveal" data-delay="0.3" style="max-width:600px;margin:2rem auto 0;background:var(--dark-100);">
      <i class="fas fa-search" style="color:var(--gold-300);"></i>
      <input type="text" id="faqSearchInput" placeholder="Tapez un mot-clé (ex: paiement, compte...)" onkeyup="filterFaqs()">
    </div>
  </div>
</section>

<!-- 3. CATÉGORIES DE FAQ -->
<section style="padding:2rem 0;background:var(--dark-200);border-bottom:1px solid var(--glass-border);">
  <div class="section-container" style="display:flex;justify-content:center;">
    <div class="filter-bar reveal">
      <button class="filter-chip active">Général</button>
      <button class="filter-chip">Clients</button>
      <button class="filter-chip">Prestataires</button>
      <button class="filter-chip">Paiements</button>
      <button class="filter-chip">Sécurité</button>
    </div>
  </div>
</section>

<!-- 4. LISTE DES FAQ PRINCIPALE -->
<section style="padding:4rem 0;">
  <div class="section-container" style="max-width:900px;">
    <div id="faqList">
      @foreach ($faqs as $i => [$q, $a])
      <div class="faq-item reveal faq-searchable" data-delay="{{ ($i%5) * 0.05 }}" style="margin-bottom:1.5rem;">
        <button onclick="toggleFaq(this)" class="faq-btn">
          <span class="faq-q-text">{{ $q }}</span>
          <div class="faq-icon-wrapper"><i class="fas fa-plus"></i></div>
        </button>
        <div class="faq-answer">
          <div style="padding:1.5rem;color:var(--gray-300);font-size:0.9rem;line-height:1.7;">
            {{ $a }}
          </div>
        </div>
      </div>
      @endforeach
    </div>
    
    <div id="noFaqResult" style="display:none;text-align:center;padding:3rem 0;">
      <i class="fas fa-search" style="font-size:3rem;color:var(--gray-600);margin-bottom:1rem;display:block;"></i>
      <p style="color:var(--gray-300);">Aucune question ne correspond à votre recherche.</p>
    </div>
  </div>
</section>

<!-- 5. 3D VISUAL DIVIDER -->
<section class="section-with-canvas" style="height:300px;background:var(--dark-300);">
  <div class="section-canvas" id="canvas-faq-divider" data-three="torus" data-color="0xF77F00"></div>
  <div style="position:relative;z-index:2;height:100%;display:flex;align-items:center;justify-content:center;">
    <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:900;color:var(--gold-300);text-transform:uppercase;letter-spacing:0.1em;opacity:0.2;">KOBLAN Support</h2>
  </div>
</section>

<!-- 6. BOX CONTACT SUPPORT DIRECT -->
<section style="padding:5rem 0;background:var(--dark-200);">
  <div class="section-container">
    <div class="glass-card holographic reveal" style="padding:3rem 2rem;text-align:center;max-width:800px;margin:0 auto;">
      <div style="width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,var(--gold-300),#F77F00);margin:0 auto 1.5rem;display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--dark-500);">
        <i class="fas fa-headset"></i>
      </div>
      <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:800;margin-bottom:1rem;">Vous n'avez pas trouvé votre réponse ?</h2>
      <p style="color:var(--gray-300);margin-bottom:2rem;max-width:500px;margin-left:auto;margin-right:auto;">
        Notre équipe d'assistance est disponible 7j/7 pour vous accompagner.
        N'hésitez pas à nous envoyer un message détaillé.
      </p>
      <div style="display:flex;gap:1.5rem;justify-content:center;flex-wrap:wrap;">
        <a href="{{ route('contact') }}" class="btn btn-gold btn-lg"><i class="fas fa-envelope"></i> Nous écrire</a>
        <a href="https://wa.me/2250700000000" target="_blank" class="btn btn-outline-gold btn-lg" style="color:#25D366;border-color:#25D366;"><i class="fab fa-whatsapp"></i> Chat WhatsApp</a>
      </div>
    </div>
  </div>
</section>

<style>
.faq-btn {
  width: 100%; text-align: left; padding: 1.5rem;
  background: var(--glass-bg); border: 1px solid var(--glass-border);
  border-radius: var(--radius-lg); color: var(--gray-100);
  display: flex; align-items: center; justify-content: space-between; gap: 1rem;
  font-family: var(--font-alt); font-weight: 700; font-size: 1rem;
  transition: all var(--transition-base); cursor: pointer;
}
.faq-btn:hover { background: var(--glass-bg-hover); border-color: var(--gold-300); }
.faq-icon-wrapper {
  width: 30px; height: 30px; border-radius: 50%;
  background: rgba(255,215,0,0.1); color: var(--gold-300);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; transition: transform 0.4s ease;
}
.faq-answer {
  display: none; background: rgba(255,215,0,0.02);
  border: 1px solid var(--gold-300); border-top: none;
  border-radius: 0 0 var(--radius-lg) var(--radius-lg);
  margin-top: -10px; padding-top: 10px;
}
</style>

@endsection

@section('scripts')
<script>
function toggleFaq(btn) {
  const answer = btn.nextElementSibling;
  const icon = btn.querySelector('i');
  const wrapper = btn.querySelector('.faq-icon-wrapper');
  const isOpen = answer.style.display === 'block';

  document.querySelectorAll('.faq-answer').forEach(a => a.style.display = 'none');
  document.querySelectorAll('.faq-btn').forEach(b => {
    b.style.borderRadius = 'var(--radius-lg)';
    b.style.borderColor = 'var(--glass-border)';
    b.style.background = 'var(--glass-bg)';
    b.querySelector('.faq-icon-wrapper').style.transform = 'rotate(0deg)';
    b.querySelector('i').className = 'fas fa-plus';
  });

  if (!isOpen) {
    answer.style.display = 'block';
    icon.className = 'fas fa-minus';
    wrapper.style.transform = 'rotate(180deg)';
    btn.style.borderRadius = 'var(--radius-lg) var(--radius-lg) 0 0';
    btn.style.borderColor = 'var(--gold-300)';
    btn.style.background = 'rgba(255,215,0,0.05)';
    if (typeof gsap !== 'undefined') {
      gsap.from(answer.querySelector('div'), { opacity: 0, y: -10, duration: 0.3 });
    }
  }
}

function filterFaqs() {
  const q = document.getElementById('faqSearchInput').value.toLowerCase();
  let count = 0;
  document.querySelectorAll('.faq-searchable').forEach(item => {
    const text = item.querySelector('.faq-q-text').innerText.toLowerCase();
    const ans = item.querySelector('.faq-answer').innerText.toLowerCase();
    if (text.includes(q) || ans.includes(q)) {
      item.style.display = 'block';
      count++;
    } else {
      item.style.display = 'none';
    }
  });
  document.getElementById('noFaqResult').style.display = count === 0 ? 'block' : 'none';
}
</script>
@endsection
