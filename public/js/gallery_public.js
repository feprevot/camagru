let page = 1, loading = false, done = false;
const cont = document.getElementById('gallery-container');
const loadEl = document.getElementById('loading');

async function load() {
  if (loading || done) return;
  loading = true;
  const res  = await fetch(`/api/gallery?page=${page}`);
  const imgs = await res.json();

  imgs.forEach(i => {
    const d = document.createElement('div');
    d.innerHTML = `<img src="/uploads/${i.filename}" alt="">`;
    cont.appendChild(d);
  });
  page++; loading = false;
}
window.addEventListener('scroll', () => {
  if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 300) load();
});
load();
