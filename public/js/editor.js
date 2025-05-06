const video         = document.getElementById('preview');
const overlaySelect = document.getElementById('overlay-select');
const captureBtn    = document.getElementById('capture-btn');
const canvas        = document.getElementById('canvas');
const overlayPrev   = document.getElementById('overlay-preview');

navigator.mediaDevices.getUserMedia({ video:true })
  .then(stream => {
      video.srcObject = stream;
      video.onloadedmetadata = () => {
          video.width  = video.videoWidth;
          video.height = video.videoHeight;
          overlayPrev.width  = video.videoWidth;
          overlayPrev.height = video.videoHeight;
      };
  })
  .catch(err => alert("Webcam inaccessible : "+err.message));

overlaySelect.addEventListener('change', () => {

    const val = overlaySelect.value;

    if (val === '') {
        overlayPrev.src = '';
        captureBtn.disabled = true;
        return;
    }

    if (val === 'none') {
        overlayPrev.src = '';
    } else {
        overlayPrev.src = val;
    }
    captureBtn.disabled = false;
});

captureBtn.addEventListener('click', () => {
  const ctx = canvas.getContext('2d');
  canvas.width  = video.videoWidth;
  canvas.height = video.videoHeight;
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

  sendImage(canvas.toDataURL('image/png'));
});


function sendImage(dataUrl){
    fetch('/upload', {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body:JSON.stringify({
            image   : dataUrl,
            overlay : overlaySelect.value 
        })
    })
    .then(res => res.text())
    .then(()  => { alert('Image saved !'); location.reload(); })
    .catch(err=> alert('Erreur : '+err));
}

document.getElementById('upload-file').addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const validTypes = ['image/png'];
    if (!validTypes.includes(file.type)) {
        alert("Only PNG files are allowed.");
        e.target.value = '';
    }
});
