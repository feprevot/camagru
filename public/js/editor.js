const video = document.getElementById('preview');
const overlaySelect = document.getElementById('overlay-select');
const captureBtn = document.getElementById('capture-btn');
const canvas = document.getElementById('canvas');
const overlayPreview = document.getElementById('overlay-preview');

navigator.mediaDevices.getUserMedia({ video: true })
  .then(stream => {
    video.srcObject = stream;

    video.onloadedmetadata = () => {
      video.width = video.videoWidth;
      video.height = video.videoHeight;
      overlayPreview.width = video.videoWidth;
      overlayPreview.height = video.videoHeight;
    };
  })
  .catch(err => {
    alert("Webcam inaccessible : " + err.message);
  });

overlaySelect.addEventListener('change', () => {
  if (overlaySelect.value) {
    overlayPreview.src = overlaySelect.value;
    captureBtn.disabled = false;
  } else {
    overlayPreview.src = '';
    captureBtn.disabled = true;
  }
});

captureBtn.addEventListener('click', () => {
    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
  
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
  
    const overlayImg = new Image();
    overlayImg.src = overlayPreview.src;
  
    overlayImg.onload = () => {
      ctx.drawImage(overlayImg, 0, 0, canvas.width, canvas.height);
      sendImage(canvas.toDataURL('image/png'));
    };
  });
  

function sendImage(dataUrl) {
  console.log("Envoi vers /upload", dataUrl);
  fetch('/upload', {
    method: 'POST',
    body: JSON.stringify({
      image: dataUrl,
      overlay: overlaySelect.value
    }),
    headers: {
      'Content-Type': 'application/json'
    }
  })
  .then(res => res.text())
  .then(msg => {
    alert("Image enregistrée !");
    location.reload();
  })
  .catch(err => alert("Erreur : " + err));
}
