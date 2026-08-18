# Portal Direktorat Bandar Udara

Situs publik + panel CMS untuk mengelola seluruh konten situs Direktorat Bandar
Udara, Ditjen Perhubungan Udara, Kementerian Perhubungan RI.

PHP 8 tanpa framework, basis data MySQL/MariaDB, tanpa langkah build.

| Dokumen | Isi |
|---|---|
| [PRD.md](PRD.md) | Tujuan produk, pengguna, cakupan, dan kriteria selesai |
| [IMPLEMENTATION.md](IMPLEMENTATION.md) | Arsitektur, alur data, dan cara kerja tiap bagian |
| [DESIGN.md](DESIGN.md) | Token warna, komponen, dan aturan tampilan |
| [MEMORY.md](MEMORY.md) | Keputusan yang sudah diambil dan jebakan yang sudah ditemukan |
| [database/DEPLOY.md](database/DEPLOY.md) | Langkah menerapkan pembaruan ke server berjalan |

---

## Struktur Folder

```
dbu-portal/
├── frontend/              Situs publik
│   ├── index.php              Beranda
│   ├── pages/                 17 halaman: profil, organisasi, layanan, regulasi,
│   │                          berita, galeri, kontak, daftar bandar udara …
│   ├── partials/              Header, navbar, footer, peta wilayah, dasbor data
│   ├── submit.php             Penerima formulir kontak / pengajuan / newsletter
│   ├── css/ js/ assets/       Aset tampilan
│   └── bootstrap.php          Pemuat awal situs publik  (tidak dilacak git)
│
├── admin/                 Panel CMS
│   ├── login.php              Halaman masuk
│   ├── index.php              Dasbor
│   ├── resource.php           CRUD generik untuk seluruh modul
│   ├── settings.php           Pengaturan situs
│   ├── inbox.php              Pesan kontak, permohonan layanan, newsletter
│   ├── config/resources.php   Definisi 36 modul CMS
│   ├── includes/              Sesi, tata letak, kolom formulir, unggahan
│   └── assets/                CSS & JS panel admin
│
├── shared/                config.php (tidak dilacak git), db.php, functions.php
├── database/              schema.sql, seed.sql, migrasi, bandaras.sql
├── uploads/               Berkas unggahan
├── legacy/                Arsip situs statis HTML sebelum jadi CMS
├── router.php             Router untuk server bawaan PHP
└── .htaccess              Aturan URL tanpa akhiran .php untuk Apache
```

## Menjalankan (lokal, XAMPP)

Prasyarat: XAMPP dengan PHP 8.x dan MySQL/MariaDB berjalan.

**1. Basis data**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/dbu-portal
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 < database/schema.sql
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 < database/seed.sql
```

Lalu jalankan seluruh migrasi dan data bandar udara — daftarnya beserta urutannya
ada di [database/DEPLOY.md](database/DEPLOY.md).

**2. Server**

```bash
/Applications/XAMPP/xamppfiles/bin/php -S localhost:8088 -t . router.php
```

`router.php` menirukan aturan `.htaccess` sehingga alamat tanpa `.php`
(mis. `/frontend/pages/profil`) tetap bekerja di server bawaan PHP. Tanpa berkas
itu, halaman hanya bisa dibuka dengan akhiran `.php`.

**3. Buka**

| Alamat | Keterangan |
|---|---|
| http://localhost:8088/frontend/ | Situs publik |
| http://localhost:8088/admin/ | Panel CMS |

**Akun awal:** `admin` / `admin123` — **ganti kata sandi ini** lewat menu
*Sistem → Pengguna* setelah masuk pertama kali.

## Konfigurasi

`shared/config.php` membaca variabel lingkungan lebih dulu, jadi tidak perlu
menyunting berkasnya:

| Variabel | Bawaan |
|---|---|
| `DBU_DB_HOST` | `127.0.0.1` |
| `DBU_DB_PORT` | `3306` |
| `DBU_DB_NAME` | `dbu_cms` |
| `DBU_DB_USER` | `root` |
| `DBU_DB_PASS` | *(kosong)* |
| `DBU_BASE_URL` | *(kosong)* — awalan URL bila proyek berada di sub-folder |
| `DBU_DEBUG` | `0` — set `1` untuk menampilkan galat saat pengembangan |

Bila situs dipasang di sub-folder (mis. `http://localhost/dbu/`), jalankan dengan
`DBU_BASE_URL=/dbu`.

`shared/config.php` dan `frontend/bootstrap.php` **tidak dilacak git** karena
berisi penyesuaian per server. Keduanya harus dibuat manual di server baru.

## Cakupan CMS

36 modul dalam 11 kelompok, seluruhnya lewat satu berkas
`admin/config/resources.php`:

| Kelompok | Modul |
|---|---|
| Beranda | 10 |
| Profil | 5 |
| Organisasi | 1 |
| Tugas & Fungsi | 2 |
| Layanan | 4 |
| Regulasi | 2 |
| Berita | 3 |
| Galeri | 3 |
| Kontak | 2 |
| Tata Letak | 3 |
| Sistem | 1 |

**Di luar cakupan CMS** — markup dan datanya tetap di berkas:

- **Daftar Bandar Udara** (`frontend/pages/informasi-publik.php`) — datanya dari
  tabel `bandaras`, diperbarui lewat impor SQL, bukan lewat panel.
- **Dasbor Data Kebandarudaraan** di beranda dan partial `ip-*.php` — grafiknya
  digambar oleh `frontend/js/main.js`.

## Ketergantungan Luar

Situs tidak memakai package manager. Tiga aset diambil dari CDN saat halaman
dibuka:

| Aset | Dipakai di |
|---|---|
| Bootstrap Icons 1.11.3 | seluruh halaman |
| Leaflet 1.9.4 | Daftar Bandar Udara |
| Leaflet.markercluster 1.5.3 | Daftar Bandar Udara |

Ubin peta diambil dari `tile.openstreetmap.org`. Server yang tertutup dari
internet perlu meng-host sendiri berkas-berkas ini.

## Menambah Modul Baru

Tambahkan tabel di basis data lalu satu entri di `admin/config/resources.php` —
menu, daftar, formulir, urutan, dan unggahan terbentuk otomatis. Rinciannya di
[IMPLEMENTATION.md](IMPLEMENTATION.md).
