# PRD — Portal Direktorat Bandar Udara

Dokumen ini menjelaskan **apa** yang dibangun dan **mengapa**. Cara
membangunnya ada di [IMPLEMENTATION.md](IMPLEMENTATION.md).

---

## 1. Latar Belakang

Direktorat Bandar Udara sebelumnya memakai situs statis HTML (arsipnya ada di
`legacy/`). Seluruh perubahan konten — berita, regulasi, pejabat, foto — harus
lewat penyunting berkas. Portal ini menggantinya dengan situs yang isinya dapat
diubah sendiri oleh pengelola melalui panel CMS, tanpa mengubah tampilan yang
sudah disetujui.

## 2. Tujuan

1. **Kemandirian konten.** Pengelola dapat mengubah hampir seluruh isi situs
   tanpa bantuan pengembang.
2. **Tampilan tetap utuh.** Desain hasil rancangan awal tidak berubah, termasuk
   saat konten belum lengkap — gambar yang kosong diganti gradien placeholder,
   bukan kotak rusak.
3. **Keterbukaan informasi publik.** Data kebandarudaraan yang wajib dibuka —
   daftar bandar udara, regulasi, laporan — dapat ditelusuri pengunjung.
4. **Ringan dipasang.** Berjalan di hosting PHP biasa tanpa Node, Composer, atau
   langkah build.

## 3. Pengguna

| Peran | Kebutuhan |
|---|---|
| **Pengunjung umum** | Mencari informasi bandar udara, layanan, regulasi, dan berita. Sebagian besar dari perangkat bergerak. |
| **Pemohon layanan** | Mengetahui persyaratan dan alur sertifikasi/perizinan, lalu mengajukan permohonan. |
| **Pengelola konten (admin)** | Memperbarui berita, pejabat, regulasi, dan foto tanpa menyentuh kode. |
| **Pengembang** | Menambah modul baru dan menerapkan pembaruan ke server tanpa merusak data. |

## 4. Cakupan

### 4.1 Situs publik

| Halaman | Isi |
|---|---|
| **Beranda** | Slider, statistik, sambutan direktur, peta wilayah OBU I–X, program RPJMN, berita, layanan unggulan, galeri, mitra |
| **Profil** | Tentang, sejarah, visi & misi, nilai organisasi, sasaran strategis, tugas dan fungsi direktorat |
| **Organisasi** | Bagan struktur, lima subdirektorat + jabatan fungsional, dua pejabat per subdirektorat, layanan yang diampu |
| **Informasi Publik** | Daftar bandar udara: peta interaktif + tabel, pencarian, filter pengelola |
| **Dokumen Publik** | Laporan tahunan, laporan kinerja, rencana strategis, unduhan, permohonan PPID |
| **Layanan** | Daftar layanan + detail (persyaratan, alur, unduhan), persyaratan umum, FAQ |
| **Regulasi** | Daftar & detail peraturan, ruang lingkup, tautan JDIH, berkas PDF |
| **Berita & Galeri** | Berita berkategori, pengumuman, foto, album, video |
| **Kontak** | Informasi kontak, formulir dengan pilihan subjek, peta lokasi |

### 4.2 Panel CMS

- Masuk dengan kata sandi ter-hash, sesi terpisah dari situs publik.
- 36 modul CRUD yang seluruhnya lahir dari satu berkas definisi.
- Unggah gambar dan dokumen dengan batas jenis serta ukuran.
- Kotak masuk: pesan kontak, permohonan layanan, pelanggan newsletter.
- Pengaturan global: identitas situs, menu navigasi, footer, SEO tiap halaman.

## 5. Persyaratan Fungsional

### 5.1 Daftar Bandar Udara

Halaman ini menggantikan isi lama halaman Informasi Publik.

- Menampilkan seluruh bandar udara dari tabel `bandaras` (saat ini 608).
- Dua tampilan yang bertukar tanpa muat ulang: **Peta** dan **Tabel**.
- Peta menandai tiap bandara pada koordinatnya; marker berdekatan dikelompokkan
  dan pecah saat diperbesar.
- Mengarahkan kursor ke marker memunculkan **seluruh** kolom data bandara itu.
- Filter pengelola berbentuk chip berwarna yang senada dengan warna marker.
- Pencarian mencakup nama bandara, kode IATA, dan kode ICAO.
- Pencarian dan filter berlaku serentak untuk peta dan tabel, disertai
  penghitung "Menampilkan X dari Y Bandar Udara".
- Tiap bandara punya halaman detail tersendiri.

### 5.2 Organisasi

- Bagan menampilkan Direktur di atas, lima subdirektorat dan satu unit
  fungsional di bawahnya.
- Tiap subdirektorat menampilkan **dua** pejabat: Kepala Subdirektorat dan
  Kepala Tim, masing-masing dengan foto, nama, dan jabatan.
- Pejabat yang namanya belum diisi tetap tampil sebagai tempat kosong yang rapi,
  bukan bagian yang hilang.

### 5.3 Tugas dan Fungsi

- Tugas pokok dan enam fungsi direktorat tampil pada halaman **Profil**.
- Halaman `/pages/tugas-fungsi` yang lama dialihkan permanen ke sana agar tautan
  luar tidak mati.
- Penyuntingannya hanya di satu tempat pada panel CMS.

### 5.4 Peta Wilayah OBU

- Sepuluh wilayah kerja Otoritas Bandar Udara dapat diklik dan memunculkan
  daftar bandar udara wilayah itu.
- Label pengelola memakai **APINDO** (PT Angkasa Pura Indonesia), menggantikan
  AP I dan AP II yang sudah bergabung. Kepanjangannya tampil sekali sebagai
  keterangan.

## 6. Persyaratan Non-Fungsional

| Aspek | Ketentuan |
|---|---|
| **Peramban** | Dua versi terakhir Chrome, Firefox, Safari, Edge |
| **Perangkat** | Responsif hingga lebar 360 px |
| **Keamanan** | Kata sandi bcrypt, token CSRF di seluruh formulir admin, prepared statement, unggahan dibatasi jenis & ukuran (8 MB), folder unggahan menolak eksekusi skrip |
| **Ketersediaan konten** | Halaman tetap tampil wajar bila kolom opsional kosong |
| **Pemasangan** | Cukup PHP 8 + MySQL/MariaDB; tanpa Node, Composer, atau build |
| **Pembaruan server** | Setiap perubahan basis data dikirim sebagai berkas migrasi yang aman dijalankan berulang |

## 7. Bukan Cakupan

- Pendaftaran atau akun untuk pengunjung umum.
- Proses persetujuan permohonan layanan secara daring — formulir hanya mencatat
  pengajuan; tindak lanjutnya di luar sistem.
- Versi bahasa Inggris. Tombol ID/EN sudah ada di antarmuka tetapi belum
  terhubung ke terjemahan.
- Penyuntingan data bandar udara lewat panel CMS. Pembaruannya lewat impor SQL.
- Pencarian menyeluruh lintas halaman. Kotak pencarian di header belum berfungsi.

## 8. Kriteria Selesai

Sebuah perubahan dianggap selesai bila:

1. Seluruh halaman membalas HTTP 200 (kecuali pengalihan yang disengaja).
2. Tidak ada galat PHP maupun galat JavaScript di konsol.
3. Perubahan basis data punya berkas migrasi dan tercatat di
   [database/DEPLOY.md](database/DEPLOY.md).
4. Fitur yang punya sisi tampilan sudah dilihat langsung di peramban, bukan
   hanya lolos pemeriksaan kode.
5. Konten yang dipindah atau dihapus dinyatakan secara terbuka, bukan hilang
   diam-diam.

## 9. Riwayat Keputusan Besar

| Keputusan | Alasan |
|---|---|
| Halaman Informasi Publik menjadi Daftar Bandar Udara | Mega menu sudah menamainya "Daftar Bandar Udara"; dasbor datanya sudah pindah ke halaman per subdirektorat |
| Dokumen & PPID pindah ke `dokumen-publik` | Isi lama halaman Informasi Publik tetap dapat diakses setelah halamannya berganti fungsi |
| Menu utama "Regulasi" dihilangkan | Sudah ada di mega menu Informasi Publik; baris navigasi jadi lebih ringkas |
| Koordinat peta dihitung dari `lokasi_arp` | Kolom `latitude`/`longitude` hampir seluruhnya kosong (2 dari 608) |
| `(APINDO)` bukan `(APINDO (Angkasa Pura Indonesia))` | Nama bandara sudah memakai kurung; versi panjang menghasilkan kurung bersarang di 33 tempat |
