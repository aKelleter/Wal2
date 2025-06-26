// copy-button.js
export function initCopyButtons() {
  if (navigator.clipboard) {
    document.querySelectorAll('pre > code').forEach((codeBlock) => {
      const btn = document.createElement('button');
      btn.className = "btn btn-outline-secondary btn-sm btn-copy position-absolute";
      btn.innerHTML = "Copier";
      btn.style.top = "8px";
      btn.style.right = "8px";
      btn.style.zIndex = "10";
      btn.type = "button";

      const pre = codeBlock.parentElement;
      pre.style.position = "relative";
      pre.appendChild(btn);

      btn.addEventListener('click', () => {
        navigator.clipboard.writeText(codeBlock.innerText);
        btn.innerHTML = "✔ Copié !";
        setTimeout(() => btn.innerHTML = "Copier", 1500);
      });
    });
  }
}
