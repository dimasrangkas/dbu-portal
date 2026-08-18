# IMPLEMENTATION — Portal Direktorat Bandar Udara

Cara kerja bagian dalam sistem. Untuk **apa** dan **mengapa**-nya, lihat
[PRD.md](PRD.md).

---

## 1. Bentuk Sistem

PHP 8 tanpa framework. Tidak ada autoloader, router kelas, template engine,
maupun langkah build. Satu permintaan HTTP dilayani oleh satu berkas `.php`
yang memuat bootstrap, menarik data, lalu mencetak HTML.

```
Permintaan
   │
   ├─ .htaccess (Apache) / router.php (server bawaan PHP)
   │     /pages/profil  →  /pages/profil.php
   │
   ├─ frontend/pages/*.php
   │     require bootstrap.php
   │       └─ shared/functions.php → shared/db.php → shared/config.php
   │     page_start()      siapkan judul, deskripsi, remah roti
   │     db_all()/db_one() tarik data
   │     partial('header') cetak kepala + navbar
   │     … isi halaman …
   │     partial('footer') cetak kaki + <script>
   │
   └─ HTML
```

Panel CMS memakai jalur sendiri (`admin/includes/bootstrap.php`) dengan sesi
terpisah, tetapi berbagi `shared/`.

## 2. Lapisan Bersama (`shared/`)

| Berkas | Isi |
|---|---|
| `config.php` | Konstanta koneksi, path, URL, batas unggahan, zona waktu. Membaca variabel lingkungan lebih dulu. **Tidak dilacak git.** |
| `db.php` | Satu koneksi PDO malas (lazy) + pembungkus kueri |
| `functions.php` | Fungsi tampilan dan data yang dipakai kedua sisi |

### Pembungkus basis data

Seluruhnya memakai prepared statement. Tidak ada penyambungan string ke SQL.

```php
db()                                  // PDO tunggal
db_all($sql, $params)                 // banyak baris
db_one($sql, $params)                 // satu baris atau null
db_value($sql, $params, $default)     // satu nilai
db_exec($sql, $params)                // jumlah baris terdampak
db_insert($table, $data)              // id baru
db_update($table, $data, $id)
db_delete($table, $id)
```

### Fungsi bantu penting

| Fungsi | Guna |
|---|---|
| `e($teks)` | Escape HTML. **Setiap** nilai dari basis data melewatinya |
| `eurl($url)` | Escape khusus atribut `href` |
| `url($path)` | Bangun URL dengan menghormati `DBU_BASE_URL` |
| `asset_url($path)` | URL berkas unggahan atau aset |
| `paragraphs($teks)` | Ubah baris kosong jadi `<p>` |
| `split_list($teks, $sep)` | Pecah kolom bernilai jamak |
| `art_block(...)` | Gambar, atau gradien placeholder bila gambar kosong |
| `setting($kunci)` | Nilai dari tabel `settings` |
| `sections($halaman)` | Judul & label seksi per halaman |
| `menu_items($lokasi)` | Menu navigasi beserta anaknya |
| `arp_to_latlng($arp)` | Koordinat DMS → desimal (lihat §6) |

## 3. Alur Halaman Publik

`frontend/bootstrap.php` menyediakan:

- `page_start($kunci, $opsi)` — menarik baris `page_meta`, menyusun judul,
  deskripsi, dan remah roti, lalu menyimpannya di `$GLOBALS['dbu_page']`.
  Mengembalikan baris meta agar halaman dapat memakai `heading`/`subtitle`.
- `page_ctx()` — membaca konteks itu dari partial.
- `partial($nama, $vars)` — menyertakan `frontend/partials/$nama.php`.

### Aset khusus satu halaman

`frontend/partials/header.php` mencetak `$GLOBALS['dbu_head']` tepat sebelum
`</head>`. Halaman yang butuh CSS tambahan mengisinya **sebelum** memanggil
`partial('header')`:

```php
$GLOBALS['dbu_head'] = '<link rel="stylesheet" href="…">';
page_start('informasi-publik', [...]);
partial('header');
```

Jalur ini dipilih karena `page_start()` berada di `frontend/bootstrap.php` yang
tidak dilacak git — menambah opsi di sana tidak akan sampai ke server.

## 4. Panel CMS Berbasis Definisi

Seluruh 36 modul lahir dari `admin/config/resources.php`. Berkas itu
mengembalikan array; satu entri menghasilkan menu, halaman daftar, formulir,
validasi, unggahan, dan pengurutan.

```php
'news' => [
    'group' => 'Berita', 'label' => 'Berita', 'icon' => 'bi-newspaper',
    'table' => 'news', 'order' => 'published_at DESC',
    'list'  => ['title' => 'Judul', 'is_published' => 'Tayang'],
    'fields' => [
        'title' => ['label' => 'Judul', 'type' => 'text', 'required' => true],
        'slug'  => ['label' => 'Slug', 'type' => 'slug', 'from' => 'title'],
    ],
    'children' => [ /* baris berulang */ ],
],
```

Kunci yang tersedia: `group`, `label`, `icon`, `table`, `order`, `list`,
`fields`, `children`, `single`, `reorder`, `search`, `per_page`, `preview`,
`no_create`, `no_delete`, `hint`.

Jenis kolom: `text`, `textarea`, `richtext`, `number`, `date`, `select`,
`checkbox`, `password`, `slug`, `icon`, `image`, `file`.

Fungsi `f_icon()`, `f_sort()`, `f_active()`, dan `f_visual()` di bagian atas
berkas menyeragamkan kolom yang berulang.

**Menambah modul:** buat tabelnya, tambahkan satu entri. Tidak ada berkas lain
yang perlu disentuh.

### Baris berulang (`children`)

Layanan, Regulasi, dan Organisasi punya baris anak di dalam formulir induknya.
Saat disimpan, baris yang dihapus dari formulir ikut terhapus dari basis data.

## 5. Basis Data

51 tabel. Pola yang berulang:

- `sort` + `is_active` pada tabel yang tampil sebagai daftar.
- `page_meta` menyimpan judul, deskripsi, eyebrow, heading, dan remah roti tiap
  halaman — satu baris per `page_key`.
- `sections` menyimpan judul dan label per seksi per halaman.
- `settings` menyimpan pasangan kunci–nilai global.
- `menu_items` menyimpan navbar dan footer dalam satu tabel, dibedakan kolom
  `location`, dengan `parent_id` untuk mega menu dan `group_label` sebagai
  judul kolomnya.

### Migrasi

Perubahan basis data **tidak** dilakukan langsung di server. Setiap perubahan
ditulis sebagai berkas di `database/`, aman dijalankan berulang
(`ADD COLUMN IF NOT EXISTS`, `ON DUPLICATE KEY UPDATE`, `UPDATE … WHERE`).
Daftar dan urutannya ada di [database/DEPLOY.md](database/DEPLOY.md), lengkap
dengan kueri untuk memeriksa migrasi mana yang belum jalan.

`schema.sql` mengandung `DROP TABLE` — hanya untuk pemasangan baru dari nol,
**tidak pernah** dijalankan di server berisi data.

## 6. Daftar Bandar Udara

Bagian paling banyak logikanya. Tiga berkas:

| Berkas | Peran |
|---|---|
| `frontend/pages/informasi-publik.php` | Menarik 608 baris, menghitung koordinat, mencetak kerangka + JSON |
| `frontend/js/bandara-map.js` | Peta Leaflet, tabel, pencarian, filter, paginasi |
| `frontend/pages/bandara-detail.php` | Halaman satu bandara |

### Koordinat

Kolom `latitude`/`longitude` pada `bandaras` hampir seluruhnya kosong — hanya 2
dari 608 terisi. Yang terisi penuh adalah `lokasi_arp` dalam bentuk
derajat-menit-detik:

```
08° 44' 51" LS 115° 10' 09" BT
```

`arp_to_latlng()` di `shared/functions.php` mengubahnya menjadi desimal. Parser
harus menangani tiga variasi yang ada di data:

1. Arah Indonesia (`LS`/`LU`/`BT`/`BB`) — 606 baris.
2. Arah Inggris (`S`/`N`/`E`/`W`) — 2 baris.
3. Koma sebagai tanda desimal (`31,62"`) — 1 baris, dinormalkan lebih dulu.

Hasil: 608 dari 608 terbaca, seluruhnya jatuh di dalam wilayah Indonesia.
Nilai di luar rentang bumi ditolak sebagai pembacaan gagal.

Perhitungan dilakukan saat halaman dirender, bukan disimpan ke kolom baru,
karena `bandaras.sql` diawali `DROP TABLE` sehingga kolom tambahan akan hilang
setiap kali datanya diimpor ulang.

### Pengiriman data

Seluruh 608 bandara dikirim sekali sebagai JSON di dalam
`<script type="application/json">`, dengan kunci dipendekkan (`n` nama, `ic`
ICAO, `pg` pengelola, …). Halaman menjadi ±320 KB, ditukar dengan pencarian dan
filter yang bekerja seketika tanpa permintaan ke server.

### Peta

Leaflet + markercluster. Marker berupa `circleMarker` — jauh lebih ringan
daripada marker bergambar untuk ratusan titik. Warna mengikuti pengelola melalui
peta warna di `bandara-map.js`; chip filter mengambil warna dari peta yang sama
agar keduanya tidak pernah berbeda.

Tiap marker mengikat dua hal: **tooltip** (hover) berisi seluruh kolom data, dan
**popup** (klik) berisi hal yang sama plus tautan ke halaman detail. Arah
tooltip diatur `auto` agar Leaflet memilih sisi yang muat di dalam peta.

Setelah pencarian atau filter, peta memanggil `fitBounds()` pada hasil yang
tersisa sehingga langsung mendekat ke titik yang dicari.

### Alih tampilan

Panel Peta dan Tabel memakai atribut `hidden`. Saat kembali ke Peta, Leaflet
dipanggil `invalidateSize()` — tanpa itu peta salah ukuran karena sempat
tersembunyi.

## 7. Peta Wilayah OBU (beranda)

Bentuk peta adalah SVG inline di `frontend/partials/indonesia-map.php`; metadata
wilayah dan daftar bandar udaranya dari tabel `regions` dan `region_airports`.
`frontend/js/main.js` menghubungkan bentuk SVG, legenda, dan panel daftar lewat
atribut `data-region` / `data-legend-region` / `data-airports-region`.

Label pengelola disimpan menyatu dengan nama bandara, mis.
`Soekarno–Hatta (APINDO)`. Saat mengganti label secara massal, pola pencocokan
**harus** menyertakan tanda kurung — tanpa itu nama seperti
`UPBU Tanjung Api, Ampana` ikut terkena.

## 8. JavaScript

Tidak ada bundler. Dua berkas:

- `frontend/js/main.js` (1630 baris) — navigasi, korsel, tab, lightbox, peta
  wilayah, dan seluruh grafik dasbor yang digambar sebagai SVG buatan sendiri.
- `frontend/js/bandara-map.js` (289 baris) — hanya untuk Daftar Bandar Udara.

Setiap blok di `main.js` dibungkus penjaga `if (elemen) { … }` sehingga aman
dimuat di halaman yang tidak memiliki elemen tersebut.

## 9. Keamanan

| Titik | Penanganan |
|---|---|
| Kata sandi | bcrypt lewat `password_hash()` |
| Formulir admin | Token CSRF di setiap kiriman |
| Kueri | Prepared statement tanpa kecuali |
| Keluaran | `e()` pada setiap nilai; JSON memakai `JSON_HEX_TAG` dan `JSON_HEX_AMP` |
| Unggahan | Daftar putih ekstensi, batas 8 MB, `uploads/.htaccess` menolak eksekusi skrip |
| Galat | `display_errors` mati kecuali `DBU_DEBUG=1` |

## 10. Menjalankan dan Memeriksa

```bash
# server
/Applications/XAMPP/xamppfiles/bin/php -S localhost:8088 -t . router.php

# periksa sintaks setelah menyunting
/Applications/XAMPP/xamppfiles/bin/php -l frontend/pages/nama-halaman.php

# uji seluruh halaman
for u in /frontend/ /frontend/pages/profil /frontend/pages/organisasi; do
  curl -s -o /dev/null -w "$u %{http_code}\n" "http://localhost:8088$u"
done
```

Server bawaan PHP bersifat satu proses: sebuah halaman **tidak bisa** melakukan
permintaan HTTP ke dirinya sendiri — permintaan itu akan menggantung.
