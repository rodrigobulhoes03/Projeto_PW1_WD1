document.addEventListener('DOMContentLoaded', () => {
    fetch('../controllers/fetch_images.php') 
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao carregar imagens');
            }
            return response.json();
        })
        .then(images => {
            const gallery = document.getElementById('imageGallery');
            images.forEach(image => {
                const card = document.createElement('div');
                card.className = 'image-card-modern';
                card.dataset.imageId = image.id;
                

                const img = document.createElement('img');
                img.src = image.path; 
                img.alt = image.description;
                card.appendChild(img);

                // Navegar para a página de detalhe ao clicar na imagem
                img.style.cursor = 'pointer';
                img.addEventListener('click', () => {
                    window.location.href = 'photo.html?id=' + image.id;
                });

                const description = document.createElement('p');
                description.className = 'image-description';
                description.textContent = image.description;
                card.appendChild(description);

                const footer = document.createElement('div');
                footer.className = 'card-footer';

                const heartIcon = document.createElement('span');
                heartIcon.className = 'favorite-icon';
                heartIcon.setAttribute('role', 'button');
                heartIcon.setAttribute('aria-label', 'Marcar como favorito');
                heartIcon.textContent = '♡';

                if (image.is_favorite) {
                    heartIcon.classList.add('is-favorite');
                    heartIcon.textContent = '♥';
                } else {
                    heartIcon.textContent = '♡';
                }

                heartIcon.addEventListener('click', (e) => {
                    // Evita navegar quando clicar no coração
                    e.stopPropagation();
                    fetch('../controllers/toggle_favorite.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'image_id=' + image.id
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('loading');

                            if (data.is_favorite) {
                                heartIcon.classList.add('is-favorite');
                                heartIcon.textContent = '♥';
                            } else {
                                heartIcon.classList.remove('is-favorite');
                                heartIcon.textContent = '♡';
                            }
                            // Atualizar a contagem de votos
                            voteCount.textContent = data.new_votes;
                        } else {
                            alert('Erro ao processar favorito: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro na requisição:', error);
                        alert('Erro de comunicação com o servidor.');
                    });
                });

                // Contagem de votos (opcional, mas útil)
                const voteCount = document.createElement('span');
                voteCount.className = 'vote-count';
                voteCount.textContent = image.votes;

                footer.appendChild(heartIcon);
                footer.appendChild(voteCount);
                card.appendChild(footer);
                gallery.appendChild(card);
            });
        })
        .catch(error => {
            console.error('Erro:', error);
            document.getElementById('imageGallery').innerHTML = '<p>Não foi possível carregar a galeria de imagens.</p>';
        });
});
