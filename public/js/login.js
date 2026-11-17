document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('loginForm');
    const tabs = form.querySelectorAll('.tab');
    const errorBox = document.getElementById('formError');
    let role = 'warga';

    // Ganti peran
    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            role = tab.dataset.role;
            tabs.forEach((t) => t.classList.toggle('is-active', t === tab));
        });
    });

    const showError = (msg) => {
        errorBox.textContent = msg;
        errorBox.hidden = false;
    };

    // Simulasi login (frontend saja): validasi sederhana lalu pindah halaman sesuai peran
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        errorBox.hidden = true;

        const email = form.email.value.trim();
        const password = form.password.value;

        if (!/^\S+@\S+\.\S+$/.test(email)) return showError('Masukkan alamat email yang valid.');
        if (password.length < 8) return showError('Password minimal 8 karakter.');

        window.location.href = form.dataset['redirect' + role.charAt(0).toUpperCase() + role.slice(1)];
    });
});