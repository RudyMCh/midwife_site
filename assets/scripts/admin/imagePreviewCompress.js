export default class ImagePreviewCompress {
    static init() {
        this.preview();
    }

    static preview() {
        const compressBtn = document.getElementById('compressImage');
        if (!compressBtn) return;

        const fileId = compressBtn.dataset.fileId;
        const img = document.getElementById('previewCompressedImage');

        let errorMsg = null;

        compressBtn.addEventListener('click', async () => {
            const value = document.getElementById('image_compress_quality').value;

            errorMsg?.remove();
            errorMsg = null;

            try {
                const response = await fetch('/admin/compress-preview/' + fileId, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'quality=' + encodeURIComponent(value),
                });

                if (!response.ok) {
                    throw new Error(`Erreur HTTP ${response.status}`);
                }

                const data = await response.json();

                if (data.error) {
                    errorMsg = document.createElement('p');
                    errorMsg.textContent = data.error;
                    img.after(errorMsg);
                } else if (data.path) {
                    img.setAttribute('src', data.path);
                }
            } catch (err) {
                console.error('Compression échouée :', err);
            }
        });
    }
}
