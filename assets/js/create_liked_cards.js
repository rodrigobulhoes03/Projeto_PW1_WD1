document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('likedGallery');
  if (!container) return;

  fetch('../controllers/fetch_images.php')
    .then(response => {
      if (!response.ok) throw new Error('Erro ao carregar imagens');
      return response.json();
    })
    .then(images => {
      const liked = images.filter(img => img.is_favorite);
      liked.forEach(image => {
        const card = document.createElement('div');
        card.className = 'image-card-modern';
        card.dataset.imageId = image.id;

        const img = document.createElement('img');
        img.src = image.path;
        img.alt = image.description;
        img.style.cursor = 'pointer';
        img.addEventListener('click', () => {
          window.location.href = 'photo.html?id=' + image.id;
        });
        card.appendChild(img);

        const description = document.createElement('p');
        description.className = 'image-description';
        description.textContent = image.description;
        card.appendChild(description);

        const footer = document.createElement('div');
        footer.className = 'card-footer';

        const heartIcon = document.createElement('span');
        heartIcon.className = 'favorite-icon is-favorite';
        heartIcon.setAttribute('role', 'button');
        heartIcon.setAttribute('aria-label', 'Remover dos favoritos');
        heartIcon.textContent = '♥';

        heartIcon.addEventListener('click', (e) => {
          e.stopPropagation();
          fetch('../controllers/toggle_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'image_id=' + image.id
          })
          .then(r => r.json())
          .then(data => {
            if (data.success) {
              if (!data.is_favorite) {
                card.remove();
              } else {
                heartIcon.textContent = '♥';
              }
            } else {
              alert('Erro ao processar favorito: ' + data.message);
            }
          })
          .catch(err => {
            console.error('Erro na requisição:', err);
            alert('Erro de comunicação com o servidor.');
          });
        });

        const voteCount = document.createElement('span');
        voteCount.className = 'vote-count';
        voteCount.textContent = image.votes;

        footer.appendChild(heartIcon);
        footer.appendChild(voteCount);
        card.appendChild(footer);
        container.appendChild(card);
      });
    })
    .catch(error => {
      console.error('Erro:', error);
      container.innerHTML = '<p>Não foi possível carregar os favoritos.</p>';
    });
});
