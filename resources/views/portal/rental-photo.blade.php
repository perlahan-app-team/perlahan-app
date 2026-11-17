<figure data-rental-photo hidden>
    <img class="land-photo" data-rental-photo-image alt="Foto lahan yang dipilih" loading="lazy">
    <figcaption class="subtle" data-rental-photo-caption></figcaption>
</figure>
@push('scripts')
<script>
(() => {
    const figure = document.querySelector('[data-rental-photo]');
    const select = figure.closest('form').elements.lahan_id;
    const image = figure.querySelector('[data-rental-photo-image]');
    const caption = figure.querySelector('[data-rental-photo-caption]');
    const update = () => {
        const option = select.selectedOptions[0];
        const url = option?.dataset.foto || '';
        figure.hidden = !url;
        if (url) {
            image.src = url;
            image.alt = 'Foto ' + option.textContent.trim();
            caption.textContent = option.textContent.trim();
        } else {
            image.removeAttribute('src');
            caption.textContent = '';
        }
    };
    image.addEventListener('error', () => { figure.hidden = true; });
    select.addEventListener('change', update);
    update();
})();
</script>
@endpush
