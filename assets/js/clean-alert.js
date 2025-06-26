
// Disparition automatique des alertes après 3 secondes
// Ex.: <div class="alert alert-info" data-timeout="5000"> (5 secondes)
export function initCleanAlert() {  
    // Disparition automatique des alertes avec durée configurable
    document.querySelectorAll('.alert').forEach((alert) => {
    const timeout = parseInt(alert.dataset.timeout || '3000', 10); // par défaut 3000 ms

    setTimeout(() => {
        alert.classList.add('fade');
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500); // laisse le temps à l'effet de transition
    }, timeout);
    });

}