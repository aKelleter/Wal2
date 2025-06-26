// btn-top.js
export function initBtnTop() {
  const btnTop = document.getElementById('btn-top');
  if (btnTop) {
    window.addEventListener('scroll', function () {
      btnTop.style.display = window.scrollY > 200 ? 'block' : 'none';
    });

    btnTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
}
