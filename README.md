# CMS Direktorat Bandar Udara

Situs publik + panel CMS untuk mengelola seluruh konten situs Direktorat Bandar Udara.
Backend PHP (tanpa framework) dengan basis data MySQL/MariaDB.

## Struktur Folder

```
dbu/
├── frontend/     Situs publik (yang dilihat pengunjung)
│   ├── index.php          Beranda
│   ├── pages/             Halaman: profil, organisasi, layanan, regulasi, berita, galeri, kontak …
│   ├── partials/          Header, navbar, footer, peta wilayah, dasbor data
│   ├── submit.php         Penerima formulir kontak / pengajuan / newsletter
│   ├── css/ js/ assets/   Aset tampilan (tidak berubah dari desain awal)
│   └── bootstrap.php      Pemuat awal situs publik
│
├── admin/        Panel CMS
│   ├── login.php          Halaman masuk
│   ├── index.php          Dasbor
│   ├── resource.php       CRUD generik untuk seluruh modul
│   ├── settings.php       Pengaturan situs
│   ├── inbox.php          Pesan kontak, permohonan layanan, pelanggan newsletter
│   ├── config/            Definisi modul CMS (resources.php)
│   ├── includes/          Sesi, tata letak, kolom formulir, unggahan
│   └── assets/            CSS & JS panel admin
│
├── shared/       Konfigurasi, koneksi PDO, dan fungsi bersama kedua folder
├── database/     schema.sql (struktur) & seed.sql (data awal dari situs statis)
├── uploads/      Berkas unggahan (gambar, dokumen) — dibuat otomatis
└── legacy/       Arsip situs statis HTML sebelum dijadikan CMS (referensi)
```

## Instalasi (lokal, XAMPP)

Prasyarat: XAMPP dengan PHP 8.x dan MySQL/MariaDB berjalan.

**1. Buat basis data dan isi data awal**

```bash
cd /Users/mac/Documents/Kantor/dbu
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 < database/schema.sql
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 < database/seed.sql
```

**2. Jalankan server**

Cara tercepat — server bawaan PHP dari folder proyek:

```bash
/Applications/XAMPP/xamppfiles/bin/php -S localhost:8088 -t .
```

Atau lewat Apache XAMPP: buat symlink dari `htdocs` ke folder proyek, lalu akses
`http://localhost/dbu/frontend/`.

**3. Buka**

| Alamat | Keterangan |
|---|---|
| http://localhost:8088/frontend/ | Situs publik |
| http://localhost:8088/admin/ | Panel CMS |

**Akun awal:** `admin` / `admin123` — **ganti kata sandi ini** lewat menu
*Sistem → Pengguna* setelah masuk pertama kali.

## Konfigurasi

Semua pengaturan koneksi ada di `shared/config.php` dan dapat ditimpa lewat
variabel lingkungan tanpa mengubah berkas:

| Variabel | Bawaan |
|---|---|
| `DBU_DB_HOST` | `127.0.0.1` |
| `DBU_DB_NAME` | `dbu_cms` |
| `DBU_DB_USER` | `root` |
| `DBU_DB_PASS` | *(kosong)* |
| `DBU_BASE_URL` | *(kosong)* — awalan URL bila proyek berada di sub-folder |
| `DBU_DEBUG` | `0` — set `1` untuk menampilkan galat saat pengembangan |

Bila situs dipasang di sub-folder (mis. `http://localhost/dbu/`), jalankan dengan
`DBU_BASE_URL=/dbu`.

## Cakupan CMS

**Dikelola lewat CMS**

| Halaman | Bagian yang dapat diubah |
|---|---|
| Beranda | Slider utama, statistik, sambutan direktur, kartu tentang kami, peta wilayah OBU (teks & cakupan), akses cepat, berita & pengumuman, layanan unggulan, galeri, video profil, mitra kerja, newsletter |
| Profil | Tentang, sejarah (linimasa), visi, misi, nilai organisasi, sasaran strategis, tugas pokok, fungsi |
| Organisasi | Seluruh unit kerja, deskripsi, kata kunci, posisi bagan |
| Tugas & Fungsi | Unit kerja beserta 8 rincian per unit |
| Layanan | Daftar layanan + halaman detail per layanan (persyaratan, alur, unduhan), persyaratan umum, alur proses, FAQ |
| Regulasi | Daftar & detail regulasi, ruang lingkup, berkas PDF, kategori |
| Berita | Berita + kategori, pengumuman |
| Galeri | Foto, album, video |
| Kontak | Informasi kontak, pilihan subjek, peta |
| Global | Menu navigasi, footer, identitas situs, media sosial, disclaimer, judul & SEO tiap halaman |

**Di luar cakupan CMS** (sesuai permintaan — markup dan data tetap statis):

- Halaman **Informasi Publik** dan Informasi Publik Detail
  (`frontend/pages/informasi-publik*.php`) — hanya kerangka situs
  (header/navbar/footer) yang mengikuti CMS.
- **Dasbor Data Kebandarudaraan** (korsel) di beranda —
  `frontend/partials/dashboard-carousel.php`, beserta grafiknya di
  `frontend/js/main.js`.

## Catatan Teknis

- **Gambar vs placeholder.** Setiap konten visual punya kolom *Gambar* dan
  pasangan *Warna Placeholder* + *Ikon*. Bila gambar dikosongkan, situs memakai
  gradien placeholder seperti desain awal — jadi tampilan tetap utuh sebelum
  foto asli tersedia.
- **Baris anak (repeater).** Layanan, Tugas & Fungsi, dan Regulasi punya baris
  berulang di dalam formulirnya (persyaratan, alur, rincian, ruang lingkup).
  Baris yang dihapus dari formulir ikut terhapus dari basis data saat disimpan.
- **Keamanan.** Kata sandi di-hash bcrypt, seluruh formulir admin memakai token
  CSRF, kueri memakai prepared statement, unggahan dibatasi jenis & ukuran
  (8 MB) dan folder `uploads/` menolak eksekusi skrip.
- **Menambah modul baru.** Cukup tambahkan tabel di basis data lalu satu entri
  di `admin/config/resources.php` — menu, daftar, dan formulir admin terbentuk
  otomatis.
