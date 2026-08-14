# Menerapkan Pembaruan ke Server yang Sudah Berjalan

Panduan ini untuk server yang **sudah berisi data**. Ikuti urutannya.

> **JANGAN jalankan `schema.sql` di server.** Setiap tabel di dalamnya didahului
> `DROP TABLE IF EXISTS`, sehingga seluruh isi basis data akan terhapus.
> Berkas itu hanya untuk pemasangan baru dari nol.

---

## 1. Cadangkan dulu (wajib)

```bash
mysqldump -u USER -p NAMA_DB > backup-$(date +%F-%H%M).sql
```

Pastikan berkasnya tidak berukuran 0 byte sebelum lanjut.

## 2. Cek migrasi mana yang masih perlu dijalankan

```sql
SELECT 'migrate-rpjmn-tugas-fungsi' AS migrasi,
       IF(COUNT(*) = 4, 'sudah', 'BELUM') AS status
  FROM information_schema.TABLES
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME IN ('rpjmn_programs','rpjmn_areas','tf_overview','tf_functions')
UNION ALL
SELECT 'migrate-region-airports',
       IF(COUNT(*) = 1, 'sudah', 'BELUM')
  FROM information_schema.TABLES
 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'region_airports'
UNION ALL
SELECT 'migrate-regulasi-jdih',
       IF(COUNT(*) = 1, 'sudah', 'BELUM')
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME = 'regulations' AND COLUMN_NAME = 'source_url'
UNION ALL
SELECT 'migrate-organisasi-navbar',
       IF(COUNT(*) = 3, 'sudah', 'BELUM')
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME = 'org_units'
   AND COLUMN_NAME IN ('team_lead_name','team_lead_position','team_lead_photo')
UNION ALL
SELECT 'bandaras',
       IF(COUNT(*) = 1, 'sudah', 'BELUM')
  FROM information_schema.TABLES
 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bandaras'
UNION ALL
SELECT 'migrate-daftar-bandara',
       IF(COUNT(*) = 1, 'sudah', 'BELUM')
  FROM `page_meta`
 WHERE `page_key` = 'dokumen-publik'
UNION ALL
SELECT 'migrate-apindo',
       IF(COUNT(*) = 0, 'sudah', 'BELUM')
  FROM `region_airports`
 WHERE `name` LIKE '%(AP I)%' OR `name` LIKE '%(AP II)%';
```

## 3. Jalankan migrasi yang berstatus BELUM

Urutannya bebas, tetapi jalankan satu per satu dan periksa keluarannya.

```bash
mysql -u USER -p NAMA_DB < database/migrate-rpjmn-tugas-fungsi.sql
mysql -u USER -p NAMA_DB < database/migrate-region-airports.sql
mysql -u USER -p NAMA_DB < database/migrate-regulasi-jdih.sql
mysql -u USER -p NAMA_DB < database/migrate-organisasi-navbar.sql
mysql -u USER -p NAMA_DB < database/bandaras.sql
mysql -u USER -p NAMA_DB < database/migrate-daftar-bandara.sql
mysql -u USER -p NAMA_DB < database/migrate-apindo.sql
```

Tanpa pesan galat = berhasil. Ketujuhnya aman dijalankan ulang bila perlu.

Lewat phpMyAdmin: buka basis datanya → tab **Import** → pilih berkas → **Go**.

### Yang akan ditimpa

Migrasi ini menulis ulang isi tabelnya, bukan menambah. Bila konten di bawah ini
sudah pernah diubah lewat CMS di server, perubahan itu akan hilang:

| Berkas | Menimpa |
|---|---|
| `migrate-rpjmn-tugas-fungsi.sql` | Program RPJMN, Tugas & Fungsi, urutan seksi beranda, meta halaman Tugas & Fungsi |
| `migrate-region-airports.sql` | Daftar bandar udara per wilayah, kolom kantor pusat ringkas pada `regions` |
| `migrate-regulasi-jdih.sql` | **Seluruh isi tabel `regulations`** — regulasi yang ditambahkan lewat CMS ikut terhapus |
| `migrate-organisasi-navbar.sql` | Menu utama "Regulasi" pada navbar (dinonaktifkan), label kelompok mega menu Informasi Publik. Menambah kolom Kepala Tim pada `org_units` tanpa menghapus data lama |
| `bandaras.sql` | **Seluruh isi tabel `bandaras`** (608 bandar udara) — berkas ini diawali `DROP TABLE`, jadi perubahan pada tabel itu akan hilang |
| `migrate-daftar-bandara.sql` | Meta halaman Informasi Publik & Dokumen Publik, menambah tautan Dokumen Publik pada mega menu |
| `migrate-apindo.sql` | Label pengelola `(AP I)`/`(AP II)` pada `region_airports` menjadi `(APINDO)` |

Tabel lain tidak disentuh. Tidak ada kolom yang dihapus atau diubah tipenya.

## 4. Unggah berkas aplikasi

Salin seluruh folder proyek **kecuali** dua hal berikut:

- `uploads/` — berisi berkas unggahan; jangan ditimpa.
- `shared/config.php` dan `frontend/bootstrap.php` — berisi penyesuaian server.

Bila server memakai git:

```bash
git pull
```

`shared/config.php` dan `frontend/bootstrap.php` sudah dikeluarkan dari git, jadi
`git pull` tidak lagi menimpa penyesuaian server. Pada server yang belum pernah
menarik pembaruan sejak perubahan itu, berkasnya akan ikut terhapus saat pull —
cadangkan dulu, lalu kembalikan:

```bash
cp shared/config.php frontend/bootstrap.php /tmp/   # sebelum pull
cp /tmp/config.php shared/ ; cp /tmp/bootstrap.php frontend/   # sesudah pull
```

## 5. Verifikasi

- Beranda tampil, peta wilayah bisa diklik dan memunculkan daftar bandar udara.
- Halaman Informasi Publik menampilkan peta 608 bandar udara; marker bisa di-hover
  dan tombol *Tabel* menampilkan daftarnya.
- Halaman Regulasi berisi peraturan dengan tombol "Lihat di JDIH Kemenhub".
- Halaman Profil menampilkan Tugas + 6 Fungsi pada bagian Tugas dan Fungsi.
- Halaman Organisasi menampilkan dua pejabat per subdirektorat.
- Masuk ke `/admin`, buka *Beranda → Peta Wilayah OBU* dan *Regulasi*.

Bila ada yang salah, pulihkan dari cadangan:

```bash
mysql -u USER -p NAMA_DB < backup-XXXX.sql
```

---

## Catatan

**Kompatibilitas.** Migrasi diuji pada MariaDB 10.4 dan memakai sintaks yang juga
berlaku di MySQL 5.7/8.0. Baris `USE` sengaja dinonaktifkan agar cocok dengan
nama basis data apa pun — pilih basis datanya lewat perintah `mysql`.

**Kredensial.** `shared/config.php` membaca variabel lingkungan lebih dulu, jadi
di server sebaiknya set `DBU_DB_NAME`, `DBU_DB_USER`, `DBU_DB_PASS`, dan
`DBU_BASE_URL` daripada menyunting berkasnya. Berkas ini beserta
`frontend/bootstrap.php` sudah tercantum di `.gitignore` di akar proyek.

**Peta bandar udara.** Halaman Daftar Bandar Udara memuat Leaflet dan ubin peta
OpenStreetMap dari internet, jadi server yang dipakai pengunjung perlu akses ke
`cdnjs.cloudflare.com` dan `tile.openstreetmap.org`. Titik marker dihitung dari
kolom `lokasi_arp` (derajat-menit-detik), bukan dari `latitude`/`longitude` yang
sebagian besar kosong.

**Folder unggahan.** Pastikan `uploads/` dapat ditulis oleh web server
(`chmod 755`, pemilik sesuai pengguna PHP) dan `uploads/.htaccess` ikut terunggah —
berkas itu mencegah skrip dieksekusi dari folder unggahan.
