# MEMORY — Konteks Proyek

Hal-hal yang **tidak terbaca dari kode**: keputusan yang sudah diambil beserta
alasannya, dan jebakan yang sudah ditemukan dengan cara yang mahal. Dibaca lebih
dulu sebelum menyentuh bagian yang bersangkutan.

Fakta yang dapat dibaca dari kode tidak ditulis di sini — untuk itu ada
[IMPLEMENTATION.md](IMPLEMENTATION.md).

---

## Jebakan

### `shared/config.php` dan `frontend/bootstrap.php` tidak dilacak git

Keduanya berisi penyesuaian per server. Akibatnya:

- Menambahkan fitur di `page_start()` **tidak akan sampai ke server**. Bila
  butuh kait baru untuk halaman, letakkan di berkas yang dilacak — misalnya
  `$GLOBALS['dbu_head']` yang dibaca `frontend/partials/header.php`.
- Di server yang belum pernah pull sejak keduanya dikeluarkan dari git, berkas
  itu akan terhapus saat `git pull`. Cadangkan dulu.

Sebelumnya aturan ignore-nya salah tempat — ditulis di `uploads/.gitignore`,
sehingga terbaca sebagai `uploads/shared/config.php` dan tidak pernah berlaku.
Sekarang benar, di `.gitignore` akar proyek.

### Server bawaan PHP hanya satu proses

`php -S` melayani satu permintaan pada satu waktu. Sebuah halaman **tidak bisa**
mengambil URL dirinya sendiri lewat `file_get_contents()` atau `curl` — akan
menggantung sampai batas waktu. Ini pernah menghabiskan waktu saat mencoba
membuat halaman pratinjau.

### Ganti label pengelola harus menyertakan tanda kurung

Di `region_airports`, label pengelola menyatu dengan nama bandara:
`Soekarno–Hatta (AP II)`. Mencocokkan `AP I` tanpa kurung ikut mengenai
`UPBU Tanjung Api, Ampana`. Selalu cocokkan `(AP I)` dan `(AP II)` lengkap
dengan kurungnya.

### `bandaras.sql` diawali `DROP TABLE`

Setiap impor ulang menghapus tabelnya. Karena itu jangan menambah kolom hasil
perhitungan ke `bandaras` — kolom itu akan hilang pada impor berikutnya.
Koordinat desimal karena itu dihitung saat render, bukan disimpan.

### `latitude` / `longitude` pada `bandaras` praktis kosong

Hanya 2 dari 608 baris terisi. Sumber koordinat yang benar adalah `lokasi_arp`
dalam bentuk derajat-menit-detik, dibaca oleh `arp_to_latlng()`. Datanya punya
tiga variasi penulisan — arah Indonesia, arah Inggris, dan koma desimal —
ketiganya sudah ditangani. Jangan menyederhanakan parsernya tanpa menguji ulang
seluruh 608 baris.

### `schema.sql` menghapus seluruh basis data

Berisi `DROP TABLE IF EXISTS` untuk setiap tabel. Hanya untuk pemasangan baru.
Untuk server berisi data, selalu lewat berkas migrasi di `database/`.

### MarkerCluster butuh dua berkas CSS

`MarkerCluster.min.css` saja membuat cluster tampil sebagai angka telanjang
tanpa lingkaran. `MarkerCluster.Default.min.css` harus ikut dimuat.

## Keputusan

### Halaman Informasi Publik = Daftar Bandar Udara

Isi lamanya (dokumen unduhan + PPID) dipindah ke `pages/dokumen-publik.php`,
tautannya ditambahkan ke mega menu. Lima dasbor data yang sebelumnya menempel di
halaman itu tidak dipindah karena memang duplikat dari partial `ip-*.php` yang
sudah tayang di halaman per subdirektorat.

`pages/informasi-publik-detail.php` kini yatim — tidak ada yang menautkannya.
Dibiarkan, belum dihapus.

### Tugas & Fungsi digabung ke Profil

Halaman `pages/tugas-fungsi.php` sekarang hanya mengalihkan (301) ke
`pages/profil#tugas-pokok`. Sumber datanya `tf_overview` + `tf_functions`.

Akibatnya `directorate_functions` dan `profil_about.tugas_pokok` tidak lagi
dipakai halaman mana pun. Keduanya **dilepas dari panel CMS** supaya tidak ada
dua tempat menyunting hal yang sama, tetapi **datanya sengaja dibiarkan utuh**
di basis data.

### Seluruh 608 bandara dikirim sekaligus ke peramban

Halaman Daftar Bandar Udara berukuran ±320 KB. Ditukar dengan pencarian dan
filter yang bekerja seketika dan berlaku serentak untuk peta dan tabel. Bila
suatu saat terasa berat, jalur perbaikannya adalah memindahkan JSON ke endpoint
terpisah yang dapat di-cache — bukan mengembalikan penyaringan ke server.

### Filter pengelola dibangun dari data

Chip filter tidak didaftar manual, melainkan dihitung dari isi kolom
`pengelola`. Karena itu "Masyarakat" yang ada di rancangan rujukan tidak muncul
— memang tidak ada di data. Bila kelak ada, chip-nya muncul sendiri.

### `(APINDO)` bukan `(APINDO (Angkasa Pura Indonesia))`

Nama bandara sudah memakai kurung, jadi versi panjang menghasilkan kurung
bersarang di 33 tempat. Kepanjangannya ditampilkan sekali sebagai keterangan di
bawah daftar wilayah, dan hanya pada wilayah yang punya bandara APINDO.

### Data bandar udara tidak masuk CMS

Pembaruannya lewat impor `database/bandaras.sql`, bukan lewat panel. Ini pilihan
sadar: datanya berasal dari sumber luar dan diperbarui sebagai satu kesatuan.

## Kebiasaan Kerja yang Berlaku di Proyek Ini

- **Setiap perubahan basis data jadi berkas migrasi** di `database/`, aman
  dijalankan berulang, lalu didaftarkan di `database/DEPLOY.md` — termasuk kueri
  pemeriksa "sudah / BELUM".
- **Fitur bertampilan diperiksa di peramban sungguhan**, bukan hanya lolos
  `php -l` dan HTTP 200. Tangkapan layar headless Chrome dipakai untuk melihat
  hasilnya.
- **Konten yang dipindah atau dihapus dinyatakan terbuka**, tidak hilang
  diam-diam.
- **Komentar dan teks antarmuka berbahasa Indonesia**, mengikuti gaya berkas di
  sekitarnya.

## Yang Belum Selesai

| Hal | Keadaan |
|---|---|
| Tombol bahasa ID/EN | Ada di antarmuka, belum terhubung terjemahan |
| Kotak pencarian di header | Ada, belum berfungsi |
| `pages/informasi-publik-detail.php` | Yatim setelah halaman induknya berganti fungsi |
| Nama pejabat organisasi | Kolom Kepala Subdirektorat & Kepala Tim masih kosong di basis data |
| Aset CDN | Leaflet dan Bootstrap Icons diambil dari internet; server tertutup perlu meng-host sendiri |
