document.addEventListener('DOMContentLoaded', () => {
  const menu = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.main-nav');
  if (menu && nav) {
    menu.addEventListener('click', () => {
      const open = menu.getAttribute('aria-expanded') === 'true';
      menu.setAttribute('aria-expanded', String(!open));
      nav.classList.toggle('open', !open);
    });
  }

  document.querySelectorAll('.nav-dropdown > a').forEach((link) => {
    link.addEventListener('click', (event) => {
      if (window.innerWidth <= 980) {
        event.preventDefault();
        link.parentElement.classList.toggle('open');
      }
    });
  });

  const searchToggle = document.querySelector('.search-toggle');
  const searchPanel = document.querySelector('.search-panel');
  if (searchToggle && searchPanel) {
    searchToggle.addEventListener('click', () => {
      searchPanel.hidden = !searchPanel.hidden;
      if (!searchPanel.hidden) {
        const input = searchPanel.querySelector('input');
        if (input) input.focus();
      }
    });
  }

  const slider = document.querySelector('[data-slider]');
  if (slider) {
    const slides = Array.from(slider.querySelectorAll('.hero-slide, .slide'));
    const dots = Array.from(slider.querySelectorAll('.slider-dots button'));
    const prevBtn = slider.querySelector('.hero-prev');
    const nextBtn = slider.querySelector('.hero-next');
    let active = 0;
    let timer;

    const show = (i) => {
      active = (i + slides.length) % slides.length;
      slides.forEach((s, n) => {
        s.classList.toggle('is-active', n === active);
        s.setAttribute('aria-hidden', String(n !== active));
      });
      dots.forEach((d, n) => d.classList.toggle('active', n === active));
    };

    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => {
        show(i);
        restart();
      });
    });

    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        show(active - 1);
        restart();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        show(active + 1);
        restart();
      });
    }

    const restart = () => {
      clearInterval(timer);
      if (slides.length > 1) {
        timer = setInterval(() => show(active + 1), 6000);
      }
    };

    slider.addEventListener('mouseenter', () => clearInterval(timer));
    slider.addEventListener('mouseleave', () => restart());

    // Touch swipe support
    let startX = 0;
    slider.addEventListener('touchstart', (e) => {
      if (e.touches && e.touches[0]) startX = e.touches[0].clientX;
    }, { passive: true });
    slider.addEventListener('touchend', (e) => {
      if (e.changedTouches && e.changedTouches[0]) {
        const diff = e.changedTouches[0].clientX - startX;
        if (Math.abs(diff) > 40) {
          if (diff > 0) show(active - 1);
          else show(active + 1);
          restart();
        }
      }
    }, { passive: true });

    restart();
  }

  const faqs = document.querySelectorAll('.faq-card-item, .faq-pill-item, .faq-item');
  if (faqs.length) {
    faqs.forEach((f) => {
      f.addEventListener('toggle', () => {
        if (f.open) {
          faqs.forEach((other) => {
            if (other !== f) {
              other.removeAttribute('open');
            }
          });
        }
      });
    });
  }
});
