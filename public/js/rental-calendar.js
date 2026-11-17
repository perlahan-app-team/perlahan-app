document.querySelectorAll('[data-calendar]').forEach(calendar => {
    const form = calendar.closest('form');
    const select = form?.elements.lahan_id;
    const start = form?.elements.sewa_mulai;
    const end = form?.elements.sewa_selesai;
    const today = calendar.dataset.today;
    const grid = calendar.querySelector('[data-days]');
    const message = calendar.querySelector('[data-message]');
    const initial = start?.value || today;
    let month = new Date(Number(initial.slice(0, 4)), Number(initial.slice(5, 7)) - 1, 1);
    let controller;
    let pickEnd = false;
    const popup = calendar.dataset.popup === 'true';
    const picker = calendar.closest('.rental-date-picker');
    const triggers = picker ? [...picker.querySelectorAll('[data-date-open]')] : [];
    let activeTrigger;
    function syncInputs() {
        triggers.forEach(trigger => {
            const value = trigger.dataset.dateOpen === 'start' ? start.value : end.value;
            trigger.value = value ? value.split('-').reverse().join('/') : '';
        });
    }
    function close() {
        if (!popup) return;
        calendar.hidden = true;
        triggers.forEach(trigger => trigger.setAttribute('aria-expanded', 'false'));
    }
    function open(trigger) {
        activeTrigger = trigger;
        pickEnd = trigger.dataset.dateOpen === 'end';
        const value = (pickEnd ? end.value || start.value : start.value) || today;
        month = new Date(Number(value.slice(0, 4)), Number(value.slice(5, 7)) - 1, 1);
        calendar.hidden = false;
        triggers.forEach(item => item.setAttribute('aria-expanded', String(item === trigger)));
        render().then(() => calendar.querySelector('button[data-date]:not(:disabled)')?.focus());
    }
    const key = () => `${month.getFullYear()}-${String(month.getMonth() + 1).padStart(2, '0')}`;
    function highlight() {
        grid.querySelectorAll('[data-date]').forEach(button => button.setAttribute('aria-pressed', String(Boolean(start?.value && end?.value && button.dataset.date >= start.value && button.dataset.date <= end.value))));
    }
    async function render() {
        controller?.abort();
        controller = new AbortController();
        const currentRequest = controller;
        grid.replaceChildren();
        calendar.querySelector('[data-month]').textContent = month.toLocaleDateString('id-ID', {month:'long', year:'numeric'});
        const landId = select?.value || calendar.dataset.land;
        if (!landId) { message.textContent = 'Pilih lahan untuk melihat jadwal.'; return; }
        message.textContent = 'Memuat jadwal…';
        try {
            const url = calendar.dataset.endpoint.replace('__LAHAN__', encodeURIComponent(landId));
            const response = await fetch(`${url}?bulan=${key()}`, {signal:controller.signal, headers:{Accept:'application/json'}, cache:'no-store'});
            if (!response.ok) throw new Error('Jadwal tidak tersedia');
            const data = await response.json();
            if (currentRequest.signal.aborted) return;
            const offset = (month.getDay() + 6) % 7;
            for (let i = 0; i < offset; i++) grid.append(document.createElement('span'));
            Object.entries(data.hari).forEach(([date, state]) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.date = date;
                button.textContent = Number(date.slice(8));
                button.className = state !== 'tersedia' ? 'calendar-busy' : 'calendar-free';
                button.disabled = state !== 'tersedia' || date < today || !start;
                if (date < today && state === 'tersedia') button.classList.add('calendar-past');
                button.setAttribute('aria-label', `${date}: ${state === 'disewa' ? 'Sudah disewa' : state === 'tidak_tersedia' ? 'Lahan tidak tersedia' : date < today ? 'Tanggal lampau' : 'Tersedia'}`);
                button.addEventListener('click', () => {
                    if (popup && pickEnd && (!start.value || date < start.value)) {
                        message.textContent = 'Pilih tanggal mulai terlebih dahulu, lalu tanggal selesai pada hari yang sama atau sesudahnya.';
                        return;
                    }
                    if (pickEnd && date >= start.value && [...grid.querySelectorAll('.calendar-busy')].some(day => day.dataset.date >= start.value && day.dataset.date <= date)) {
                        message.textContent = 'Rentang tersebut melewati tanggal merah. Pilih periode yang seluruhnya tersedia.';
                        return;
                    }
                    if (!pickEnd || date < start.value) { start.value = date; end.value = date; pickEnd = true; }
                    else { end.value = date; pickEnd = false; }
                    start.dispatchEvent(new Event('change', {bubbles:true}));
                    end.dispatchEvent(new Event('change', {bubbles:true}));
                    message.textContent = pickEnd ? 'Tanggal mulai dipilih. Klik tanggal selesai; untuk satu hari, klik tanggal yang sama.' : 'Jadwal dipilih. Pastikan seluruh periode tersedia sebelum mengirim.';
                    highlight();
                    syncInputs();
                    if (popup) { close(); activeTrigger?.focus(); }
                });
                grid.append(button);
            });
            message.textContent = start ? 'Klik tanggal hijau untuk memilih awal dan akhir sewa, atau isi kolom tanggal.' : 'Jadwal ketersediaan lahan bulan ini.';
            highlight();
        } catch (error) {
            if (error.name !== 'AbortError') message.textContent = 'Jadwal gagal dimuat. Coba pindah bulan atau muat ulang halaman. Ketersediaan tetap diperiksa saat permintaan dikirim.';
        }
    }
    calendar.querySelector('[data-prev]').addEventListener('click', () => { month.setMonth(month.getMonth() - 1); render(); });
    calendar.querySelector('[data-next]').addEventListener('click', () => { month.setMonth(month.getMonth() + 1); render(); });
    select?.addEventListener('change', () => { pickEnd = false; start?.setCustomValidity(''); render(); });
    start?.addEventListener('change', () => { highlight(); syncInputs(); });
    end?.addEventListener('change', () => { highlight(); syncInputs(); });
    triggers.forEach(trigger => {
        trigger.addEventListener('click', () => open(trigger));
        trigger.addEventListener('keydown', event => {
            if (['Enter', ' ', 'ArrowDown'].includes(event.key)) { event.preventDefault(); open(trigger); }
        });
    });
    if (popup) {
        document.addEventListener('click', event => { if (!picker.contains(event.target)) close(); });
        picker.addEventListener('keydown', event => { if (event.key === 'Escape') { close(); activeTrigger?.focus(); } });
        form.addEventListener('submit', event => {
            if (!start.value || !end.value) { event.preventDefault(); open(triggers[start.value ? 1 : 0]); message.textContent = 'Pilih tanggal mulai dan selesai sewa.'; }
        });
        syncInputs();
    } else render();
});
