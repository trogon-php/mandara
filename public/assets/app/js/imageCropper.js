let activeCropper = null;
let currentConfig = null;

export function initImageCropper(config) {
    console.log(config);
    const {
        inputSelector,
        previewSelector,
        previewImgSelector,
        removeBtnSelector,
        aspectRatio = 1 / 1,
        quality = 0.8,
        maxSize = 2 * 1024 * 1024,
        onFileReady
    } = config;

    const input = document.querySelector(inputSelector);
    const preview = document.querySelector(previewSelector);
    const previewImg = document.querySelector(previewImgSelector);
    const removeBtn = document.querySelector(removeBtnSelector);

    input?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file?.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('image-to-crop').src = event.target.result;

            // Store config context
            currentConfig = {
                preview,
                previewImg,
                onFileReady,
                quality,
                maxSize,
                aspectRatio
            };

            const modal = new bootstrap.Modal(document.getElementById('cropperModal'));
            modal.show();
        };
        reader.readAsDataURL(file);
    });

    removeBtn?.addEventListener('click', () => {
        preview?.style?.setProperty('display', 'none');
        previewImg.src = '';
        input.value = '';
        onFileReady(null);
    });
}

// Global cropper logic (only once per page)
document.getElementById('cropperModal')?.addEventListener('shown.bs.modal', () => {
    const image = document.getElementById('image-to-crop');
    if (activeCropper) activeCropper.destroy();
    activeCropper = new Cropper(image, {
        aspectRatio: currentConfig.aspectRatio || 3 / 2,
        viewMode: 2,
        autoCropArea: 1,
    });
});

document.getElementById('crop-button')?.addEventListener('click', () => {
    if (!activeCropper || !currentConfig) return;

    const canvas = activeCropper.getCroppedCanvas();
    canvas.toBlob(async (blob) => {
        const compressed = await compressImage(blob, currentConfig.quality, currentConfig.maxSize);
        const file = new File([compressed], 'cropped_' + Date.now() + '.jpg', { type: 'image/jpeg' });

        // Preview and callback
        currentConfig.previewImg.src = URL.createObjectURL(file);
        currentConfig.preview.style.display = 'block';
        currentConfig.onFileReady(file);

        bootstrap.Modal.getInstance(document.getElementById('cropperModal')).hide();
    }, 'image/jpeg', currentConfig.quality);
});

function compressImage(blob, quality, maxSize) {
    return new Promise((resolve) => {
        if (blob.size <= maxSize) return resolve(blob);

        const img = new Image();
        img.onload = function () {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            canvas.getContext('2d').drawImage(img, 0, 0);

            let q = quality;
            function tryCompress() {
                canvas.toBlob((newBlob) => {
                    if (newBlob.size <= maxSize || q <= 0.2) {
                        resolve(newBlob);
                    } else {
                        q -= 0.1;
                        tryCompress();
                    }
                }, 'image/jpeg', q);
            }
            tryCompress();
        };
        img.src = URL.createObjectURL(blob);
    });
}
