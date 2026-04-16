// SPA-like AJAX handler for Forms
document.addEventListener('submit', async function(e) {
    if (e.target.tagName === 'FORM' && e.target.classList.contains('ajax-form')) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        let originalHtml = '';
        
        if (btn) {
            originalHtml = btn.innerHTML;
            // Ne pas modifier la taille du bouton
            const w = btn.offsetWidth;
            btn.style.width = w + 'px';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
        }

        try {
            const formData = new FormData(form);
            const action = form.action || window.location.href;
            const method = (form.querySelector('input[name="_method"]')?.value || form.method || 'POST').toUpperCase();
            
            const fetchMethod = method === 'GET' ? 'GET' : 'POST';
            const fetchUrl = fetchMethod === 'GET' ? action + '?' + new URLSearchParams(formData) : action;

            const res = await fetch(fetchUrl, {
                method: fetchMethod,
                body: fetchMethod === 'GET' ? null : formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html' // Force le retour HTML (pour récupérer la vue après redirection)
                }
            });

            if (res.ok) {
                const text = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, 'text/html');
                
                // 1. Remplacer le contenu principal (<main>)
                const currentMain = document.querySelector('main');
                const newMain = doc.querySelector('main');
                if (currentMain && newMain) {
                    currentMain.innerHTML = newMain.innerHTML;
                }

                // 2. Extraire et exécuter les nouveaux scripts contenus dans <main>
                const newScripts = currentMain.querySelectorAll('script');
                newScripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });

                // 3. Afficher les notifications Flash
                const flashMsg = doc.getElementById('flashMsg');
                if (flashMsg) {
                    const existingMsg = document.getElementById('flashMsg');
                    if(existingMsg) existingMsg.remove();
                    document.body.appendChild(flashMsg);
                }

                // 4. Rédéclencher les évènements GSAP / Turbo load
                setTimeout(() => {
                    document.dispatchEvent(new Event('turbo:load'));
                }, 100);
            } else {
                console.error("Erreur HTTP:", res.status);
                // Si la session expire ou erreur 500, forcer rechargement
                if(res.status === 401 || res.status === 419) window.location.reload();
            }

        } catch (err) {
            console.error("Erreur AJAX:", err);
        } finally {
            if (btn) {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                btn.style.width = 'auto'; // Reset
            }
        }
    }
});
