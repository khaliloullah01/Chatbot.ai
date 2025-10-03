// Initialisation
document.addEventListener('DOMContentLoaded', function() {
  // Toggle menu mobile
  document.getElementById('btn-menu-mobile').addEventListener('click', function() {
    document.getElementById('barre-laterale').classList.toggle('active');
  });
  
  // Gestion des clics sur les boutons "Voir"
  document.querySelectorAll('.btn-voir').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const coursTitre = this.closest('.cours-item').querySelector('h3').textContent;
      alert(`Ouverture du cours: ${coursTitre}`);
      // Ici vous pouvez rediriger vers la page du cours ou ouvrir le PDF
    });
  });
  
  // Gestion des clics sur les boutons "Télécharger"
  document.querySelectorAll('.btn-telecharger').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const coursTitre = this.closest('.cours-item').querySelector('h3').textContent;
      alert(`Téléchargement du cours: ${coursTitre}`);
      // Ici vous pouvez déclencher le téléchargement du fichier
    });
  });
});