let currentPage = 1;
let loading = false;
let done = false;

const container = document.getElementById('gallery-container');
const loadingIndicator = document.getElementById('loading');

async function loadImages() {
    if (loading || done) return;
    loading = true;

    try {
        const res = await fetch(`/api/gallery?page=${currentPage}`);
        const images = await res.json();

        if (images.length === 0) {
            done = true;
            loadingIndicator.innerText = '';
            return;
        }

        images.forEach(img => {
            const div = document.createElement('div');
            div.classList.add('image-block');
            div.innerHTML = `
                <img src="/uploads/${img.filename}" width="400">
                <p><strong>${img.username}</strong></p>
            
                <button class="like-btn ${img.liked ? 'liked' : ''}" data-id="${img.id}">
                    ❤️ <span class="like-count">${img.likes}</span>
                </button>
            
                <div class="comments" data-id="${img.id}">
                    ${(img.comments || []).map(c =>
                    `<p><strong>${c.username}:</strong> ${c.content}</p>`).join('')}
                </div>
            
                <form class="comment-form" data-id="${img.id}">
                    <input type="text" name="comment" placeholder="Your com" required>
                    <button type="submit">Send</button>
                </form>
            `;        
            container.appendChild(div);
        });

        currentPage++;
    } catch (err) {
        console.error("err images :", err);
    } finally {
        loading = false;
    }
}

window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 300) {
        loadImages();
    }
});

loadImages();

container.addEventListener('click', async e => {
    if (e.target.classList.contains('like-btn')) {
        const imageId = e.target.dataset.id;

        try {
            const res  = await fetch('/like', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ image_id: imageId })
            });

            const text = await res.text();
            let result;
            try {                      
                result = JSON.parse(text);
            } catch {
                console.error('response non‑JSON :', text);
                return;
            }

            if (result.status && result.likes !== undefined) {
                e.target.querySelector('.like-count').innerText = result.likes;
                e.target.classList.toggle('liked', result.status === 'liked');
            }

        } catch (err) {
            console.error('error like :', err);
        }
    }
});


container.addEventListener('submit', async e => {
    if (e.target.classList.contains('comment-form')) {
        e.preventDefault();
        const imageId = e.target.dataset.id;
        const input = e.target.querySelector('input[name="comment"]');
        const content = input.value.trim();

        if (!content) return;

        try {
            const res = await fetch('/comment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ image_id: imageId, content })
            });

            const newComment = await res.json();
            const commentDiv = container.querySelector(`.comments[data-id="${imageId}"]`);
            commentDiv.innerHTML += `<p><strong>${newComment.username}:</strong> ${newComment.content}</p>`;
            input.value = '';
        } catch (err) {
            console.error("err com :", err);
        }
    }
});
