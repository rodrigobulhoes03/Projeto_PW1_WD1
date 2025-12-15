(function(){
  function getQueryParam(name){
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const id = getQueryParam('id');
    const content = document.getElementById('photoContent');
    if (!id) {
      content.innerHTML = '<p>Foto não especificada.</p>';
      return;
    }

    fetch('../controllers/get_image.php?id=' + encodeURIComponent(id))
      .then(r => { if (!r.ok) throw new Error('Falha ao carregar imagem'); return r.json(); })
      .then(image => {
        const wrapper = document.createElement('div');
        wrapper.style.display = 'grid';
        wrapper.style.gridTemplateColumns = '1fr';
        wrapper.style.gap = '16px';

        const card = document.createElement('div');
        card.className = 'image-card-modern';

        const img = document.createElement('img');
        img.src = image.path;
        img.alt = image.description || 'Imagem';
        card.appendChild(img);

        const description = document.createElement('p');
        description.className = 'image-description';
        description.textContent = image.description || '';
        card.appendChild(description);

        const footer = document.createElement('div');
        footer.className = 'card-footer';

        const heartIcon = document.createElement('span');
        heartIcon.className = 'favorite-icon';
        heartIcon.setAttribute('role', 'button');
        heartIcon.setAttribute('aria-label', 'Adicionar aos favoritos');
        heartIcon.textContent = image.is_favorite ? '♥' : '♡';
        if (image.is_favorite) heartIcon.classList.add('is-favorite');

        const voteCount = document.createElement('span');
        voteCount.className = 'vote-count';
        voteCount.textContent = image.votes;

        heartIcon.addEventListener('click', () => {
          fetch('../controllers/toggle_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'image_id=' + image.id
          })
          .then(r => r.json())
          .then(data => {
            if (data.success) {
              if (data.is_favorite) {
                heartIcon.classList.add('is-favorite');
                heartIcon.textContent = '♥';
              } else {
                heartIcon.classList.remove('is-favorite');
                heartIcon.textContent = '♡';
              }
              voteCount.textContent = data.new_votes;
            } else {
              alert('Erro ao processar favorito: ' + data.message);
            }
          })
          .catch(err => {
            console.error('Erro na requisição:', err);
            alert('Erro de comunicação com o servidor.');
          });
        });

        footer.appendChild(heartIcon);
        footer.appendChild(voteCount);
        card.appendChild(footer);

        wrapper.appendChild(card);
        content.appendChild(wrapper);
      })
      .catch(err => {
        console.error(err);
        content.innerHTML = '<p>Não foi possível carregar a fotografia.</p>';
      });
  });
})();
