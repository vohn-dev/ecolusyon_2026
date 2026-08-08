document.querySelectorAll('.camera-capture').forEach((root) => {
    const startBtn = root.querySelector('.camera-capture__start');
    const shootBtn = root.querySelector('.camera-capture__shoot');
    const retakeBtn = root.querySelector('.camera-capture__retake');
    const video = root.querySelector('.camera-capture__video');
    const canvas = root.querySelector('.camera-capture__canvas');
    const previewWrap = root.querySelector('.camera-capture__preview');
    const previewImg = root.querySelector('.camera-capture__image');
    const fileInput = root.querySelector('.camera-capture__fileinput');
    let stream = null;
 
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        // No camera API support -- the native file input's capture="environment"
        // already opens the default camera app, so just hide this button.
        startBtn.style.display = 'none';
        return;
    }
 
    startBtn.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }, // default = back/rear camera
                audio: false,
            });
            video.srcObject = stream;
            video.style.display = 'block';
            fileInput.style.display = 'none';
            startBtn.style.display = 'none';
            shootBtn.style.display = 'inline-block';
            previewWrap.style.display = 'none';
        } catch (err) {
            // Permission denied or no camera -- fall back silently to the
            // native file input (still opens the device's default camera app).
            console.warn('Camera unavailable, falling back to file input:', err);
        }
    });
 
    shootBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
 
        canvas.toBlob((blob) => {
            const file = new File([blob], 'capture.jpg', { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
 
            previewImg.src = URL.createObjectURL(blob);
            previewWrap.style.display = 'block';
        }, 'image/jpeg', 0.9);
 
        stopStream();
        video.style.display = 'none';
        shootBtn.style.display = 'none';
        retakeBtn.style.display = 'inline-block';
    });
 
    retakeBtn.addEventListener('click', () => {
        previewWrap.style.display = 'none';
        retakeBtn.style.display = 'none';
        startBtn.style.display = 'inline-block';
        fileInput.value = '';
    });
 
    function stopStream() {
        if (stream) {
            stream.getTracks().forEach((t) => t.stop());
            stream = null;
        }
    }
});
