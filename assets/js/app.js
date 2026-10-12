document.addEventListener('DOMContentLoaded', () => {
  const menu = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.main-nav');
  const closeMenu = () => { menu?.setAttribute('aria-expanded', 'false'); nav?.classList.remove('open'); };
  menu?.addEventListener('click', () => {
    const open = menu.getAttribute('aria-expanded') !== 'true';
    menu.setAttribute('aria-expanded', String(open)); nav?.classList.toggle('open', open);
  });
  document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
  document.addEventListener('click', event => { if (!event.target.closest('.site-header')) closeMenu(); });
  document.querySelectorAll('.nav-dropdown > a').forEach(link => {
    link.setAttribute('aria-expanded', 'false');
    link.addEventListener('click', event => {
      if (window.innerWidth <= 980 && !link.parentElement.classList.contains('open')) {
        event.preventDefault(); link.parentElement.classList.add('open'); link.setAttribute('aria-expanded', 'true');
      }
    });
  });
  const slider = document.querySelector('[data-slider]');
  if (slider) {
    const slides = [...slider.querySelectorAll('[data-slide]')];
    const dots = [...slider.querySelectorAll('.slider-dots button')];
    const pause = slider.querySelector('.slider-pause');
    let active = 0, timer, paused = false, hover = false;
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const show = index => {
      active = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => { slide.classList.toggle('is-active', i === active); slide.setAttribute('aria-hidden', String(i !== active)); slide.inert = i !== active; });
      dots.forEach((dot, i) => { dot.classList.toggle('active', i === active); dot.setAttribute('aria-current', String(i === active)); });
    };
    const restart = () => {
      clearInterval(timer);
      if (!paused && !hover && !document.hidden && !reduced.matches && !slider.matches(':focus-within')) timer = setInterval(() => show(active + 1), 2000);
    };
    dots.forEach((dot, i) => dot.addEventListener('click', () => { show(i); restart(); }));
    slider.querySelector('.hero-prev')?.addEventListener('click', () => { show(active - 1); restart(); });
    slider.querySelector('.hero-next')?.addEventListener('click', () => { show(active + 1); restart(); });
    pause?.addEventListener('click', () => { paused = !paused; pause.setAttribute('aria-pressed', String(paused)); pause.setAttribute('aria-label', paused ? 'Slaytları otomatik oynat' : 'Slaytları duraklat'); pause.textContent = paused ? 'Oynat' : 'Duraklat'; restart(); });
    slider.addEventListener('mouseenter', () => { hover = true; restart(); });
    slider.addEventListener('mouseleave', () => { hover = false; restart(); });
    slider.addEventListener('focusin', restart); slider.addEventListener('focusout', () => setTimeout(restart, 0));
    document.addEventListener('visibilitychange', restart);
    let startX = 0, startY = 0;
    slider.addEventListener('touchstart', e => { startX = e.touches[0].clientX; startY = e.touches[0].clientY; }, { passive: true });
    slider.addEventListener('touchend', e => { const dx = e.changedTouches[0].clientX-startX, dy = e.changedTouches[0].clientY-startY; if (Math.abs(dx)>60 && Math.abs(dx)>Math.abs(dy)) { show(active + (dx < 0 ? 1 : -1)); restart(); } }, { passive: true });
    if (pause) {
      pause.setAttribute('aria-pressed', String(paused));
      pause.setAttribute('aria-label', paused ? 'Slaytları otomatik oynat' : 'Slaytları duraklat');
      pause.textContent = paused ? 'Oynat' : 'Duraklat';
    }
    show(0);
    restart();
  }
  document.querySelectorAll('[data-faq-accordion] details').forEach(detail => detail.addEventListener('toggle', () => {
    if (detail.open) detail.closest('[data-faq-accordion]').querySelectorAll('details').forEach(other => { if (other !== detail) other.open = false; });
  }));
  document.querySelector('.contact-form')?.addEventListener('submit', event => {
    event.preventDefault();
    const data = new FormData(event.currentTarget);
    const body = 'Ad Soyad: ' + data.get('name') + '\nE-posta: ' + data.get('email') + '\nTelefon: ' + data.get('phone') + '\n\n' + data.get('message');
    window.location.href = 'mailto:info@nextlabcosmetic.com?subject=' + encodeURIComponent(data.get('subject') || 'Nextlab iletişim talebi') + '&body=' + encodeURIComponent(body);
  });
});
