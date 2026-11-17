
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const searchInput = document.getElementById('searchInput');

    // Membuka dan menutup sidebar pada layar mobile.
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    // Menutup sidebar setelah menu dipilih pada layar mobile.
    document.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 760 && sidebar) {
                sidebar.classList.remove('open');
            }
        });
    });

    // Pencarian kartu lahan dan tabel pengajuan.
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();

            document.querySelectorAll('.land-card').forEach(function (card) {
                const searchableText =
                    (card.dataset.search || card.textContent).toLowerCase();

                card.style.display = searchableText.includes(query)
                    ? ''
                    : 'none';
            });

            document.querySelectorAll('#submissionTable tbody tr')
                .forEach(function (row) {
                    row.style.display = row.textContent
                        .toLowerCase()
                        .includes(query) ? '' : 'none';
                });
        });
    }

    // Tombol buat pengajuan.
    const createButton = document.getElementById('createButton');

    if (createButton) {
        createButton.addEventListener('click', function () {
            alert(
                'Halaman Buat Pengajuan akan dihubungkan ' +
                'setelah alur pengajuan disiapkan.'
            );
        });
    }

    // Tombol catatan admin.
    const notesButton = document.getElementById('notesButton');

    if (notesButton) {
        notesButton.addEventListener('click', function () {
            alert(
                'Pengajuan Kebun Komunitas Rungkut memerlukan perbaikan. ' +
                'Fitur catatan admin akan dihubungkan ke backend.'
            );
        });
    }

    // Tombol notifikasi.
    const notificationButton =
        document.getElementById('notificationButton');

    if (notificationButton) {
        notificationButton.addEventListener('click', function () {
            alert('Fitur notifikasi belum terhubung ke backend.');
        });
    }

    // Tombol keluar masih berupa simulasi.
    const logoutLink = document.getElementById('logoutLink');

    if (logoutLink) {
        logoutLink.addEventListener('click', function (event) {
            event.preventDefault();

            alert(
                'Fitur keluar akan dihubungkan dengan ' +
                'autentikasi aplikasi.'
            );
        });
    }

    // Menampilkan tanggal sesuai tanggal perangkat.
    const currentDate = document.getElementById('currentDate');

    if (currentDate) {
        const today = new Date();

        const dateText = new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }).format(today);

        currentDate.textContent = dateText.toUpperCase();
    }
});
