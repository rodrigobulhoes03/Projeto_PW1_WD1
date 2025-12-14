document.addEventListener('DOMContentLoaded', () => {
    fetch('../controllers/fetch_images.php') // Novo controller a ser criado
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao carregar imagens');
            }
            return response.json();
        })
        .then(images => {
            const gallery = document.getElementById('imageGallery');
            images.forEach(image => {
                // image deve ser um objeto com {id, username, path, description, votes, is_favorite}
                const card = document.createElement('div');
                card.className = 'image-card';
                card.dataset.imageId = image.id; // Para identificar a imagem no clique
                
                // Imagem
                const img = document.createElement('img');
                // O caminho da imagem no ficheiro é ../uploads/..., mas o card.html está em views/,
                // então o caminho relativo para o browser deve ser ajustado.
                // Como o home_page.html (que contém o iframe) está em views/, o caminho deve ser ../uploads/...
                // No entanto, o iframe está a carregar o card.html, o que pode complicar os caminhos relativos.
                // Vamos assumir que o caminho é relativo ao card.html, que está dentro do iframe.
                // O caminho correto deve ser ../uploads/...
                img.src = image.path; 
                img.alt = image.description;
                card.appendChild(img);

                // Descrição (opcional, para cumprir o requisito de ver a descrição)
                const description = document.createElement('p');
                description.className = 'image-description';
                description.textContent = image.description;
                card.appendChild(description);

                // Footer (barra azul escura)
                const footer = document.createElement('div');
                footer.className = 'card-footer';

                // Ícone de Favorito (Coração)
                const heartIcon = document.createElement('span');
                heartIcon.className = 'favorite-icon';
                heartIcon.innerHTML = '<i class="fa-regular fa-heart"></i>'; // Ícone de coração vazio (Font Awesome)
                
                // Se for favorito, preenche o coração
                if (image.is_favorite) {
                    heartIcon.innerHTML = '<i class="fa-solid fa-heart"></i>';
                    heartIcon.classList.add('is-favorite');
                } else {
                    heartIcon.innerHTML = '<i class="fa-regular fa-heart"></i>';
                }

                // Lógica de clique para adicionar/remover favorito
                heartIcon.addEventListener('click', () => {
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
                            // Atualizar o estado visual
                            if (data.is_favorite) {
                                heartIcon.classList.add('is-favorite');
                                heartIcon.innerHTML = '<i class="fa-solid fa-heart"></i>';
                            } else {
                                heartIcon.classList.remove('is-favorite');
                                heartIcon.innerHTML = '<i class="fa-regular fa-heart"></i>';
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
