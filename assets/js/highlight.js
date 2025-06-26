// highlight.js
export function afficherCommandeBash(commande, elementCible) {
  const safeCommande = commande
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");

  const html = `<pre><code class="language-bash">${safeCommande}</code></pre>`;

  const cible = document.querySelector(elementCible);
  if (cible) {
    cible.innerHTML = html;

    if (window.hljs) {
      const codeEl = cible.querySelector("code");
      if (codeEl) {
        codeEl.classList.add('language-bash');
        hljs.highlightElement(codeEl);
      }
    }
  }
}

export function initHighlight() {
  if (window.hljs) {
    hljs.highlightAll();
  }
}
