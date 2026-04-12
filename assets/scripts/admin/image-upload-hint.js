export function initImageUploadHints() {
    console.log('teste')
    document.querySelectorAll('input[type="file"][data-img-min-width], input[type="file"][data-img-min-height]').forEach((input) => {
        const minWidth = parseInt(input.dataset.imgMinWidth ?? '0', 10);
        const minHeight = parseInt(input.dataset.imgMinHeight ?? '0', 10);
        const feedback = input.closest('.media-file-widget')?.querySelector('.image-hint-feedback');

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file || !file.type.startsWith('image/')) {
                clearFeedback(feedback, input);
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => validateDimensions(img.naturalWidth, img.naturalHeight, minWidth, minHeight, input, feedback);
                img.src = /** @type {string} */ (e.target?.result);
            };
            reader.readAsDataURL(file);
        });
    });

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (e) => {
            const blocked = form.querySelectorAll('input[data-img-blocked="1"]');
            if (blocked.length > 0) {
                e.preventDefault();
                blocked[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
}

function clearFeedback(feedback, input) {
    if (feedback) {
        feedback.textContent = '';
        feedback.className = 'image-hint-feedback';
    }
    input.removeAttribute('data-img-blocked');
}

function validateDimensions(width, height, minWidth, minHeight, input, feedback) {
    const widthOk = minWidth === 0 || width >= minWidth;
    const heightOk = minHeight === 0 || height >= minHeight;

    if (widthOk && heightOk) {
        showFeedback(feedback, `Dimensions correctes (${width}\u202f\u00d7\u202f${height}\u202fpx)`, 'success');
        input.removeAttribute('data-img-blocked');
        return;
    }

    const parts = [];
    if (!widthOk) {
        parts.push(`largeur\u00a0: ${width}\u202fpx (minimum\u00a0${minWidth}\u202fpx)`);
    }
    if (!heightOk) {
        parts.push(`hauteur\u00a0: ${height}\u202fpx (minimum\u00a0${minHeight}\u202fpx)`);
    }

    const clearlyInsufficient =
        (minWidth > 0 && width < minWidth / 2) ||
        (minHeight > 0 && height < minHeight / 2);

    if (clearlyInsufficient) {
        showFeedback(feedback, `Image trop petite\u00a0\u2014 ${parts.join(', ')}. Veuillez choisir une image plus grande.`, 'danger');
    } else {
        showFeedback(feedback, `Dimensions insuffisantes\u00a0\u2014 ${parts.join(', ')}. L\u2019image risque d\u2019\u00eatre floue ou pixelis\u00e9e.`, 'warning');
    }

    input.dataset.imgBlocked = '1';
}

function showFeedback(feedback, message, level) {
    if (!feedback) return;
    feedback.textContent = message;
    feedback.className = `image-hint-feedback image-hint-feedback--${level}`;
}
