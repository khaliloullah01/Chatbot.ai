document.addEventListener('DOMContentLoaded', function() {
  // Toggle menu mobile
  document.getElementById('btn-menu-mobile').addEventListener('click', function() {
    document.getElementById('barre-laterale').classList.toggle('active');
  });

  // Gestion du clic sur "Voir" pour les cours
  document.querySelectorAll('.btn-voir').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const targetId = this.getAttribute('href');
      if (targetId && targetId.startsWith('#')) {
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      }
    });
  });

  // Animation pour les items de cours
  const coursItems = document.querySelectorAll('.cours-item');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, { threshold: 0.1 });

  coursItems.forEach(item => {
    item.style.opacity = '0';
    item.style.transform = 'translateY(20px)';
    item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(item);
  });
});