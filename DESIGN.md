# DESIGN — Portal Direktorat Bandar Udara

Aturan tampilan situs publik. Seluruh nilai di bawah ini nyata ada di
`frontend/css/style.css` — bukan usulan.

---

## 1. Prinsip

1. **Tampilan tidak boleh rusak karena konten kosong.** Gambar yang belum diisi
   diganti gradien berikon, bukan kotak rusak. Nilai kosong dicetak `—`, bukan
   dibiarkan hilang.
2. **Satu bahasa visual.** Halaman baru memakai komponen yang sudah ada. Kelas
   baru dibuat hanya bila memang belum ada padanannya.
3. **Warna punya arti.** Biru untuk identitas dan aksi, kuning untuk aksen,
   hijau/merah/kuning hanya untuk status.
4. **Teks Indonesia.** Termasuk label, tombol, dan pesan kosong.

## 2. Token

Didefinisikan pada `:root`. Jangan menulis nilai heksadesimal langsung di
komponen.

### Warna

| Token | Nilai | Pemakaian |
|---|---|---|
| `--primary` | `#005BAC` | Warna utama, tombol, tautan |
| `--primary-dark` | `#00396B` | Judul seksi, kepala tabel |
| `--primary-darker` | `#00223F` | Latar footer, gradien |
| `--primary-light` | `#2E86D8` | Aksen gradien |
| `--sky-50` | `#EEF5FC` | Latar seksi selang-seling, kartu aktif |
| `--sky-100` | `#E0EDF9` | Garis tepi lembut |
| `--gray-50` / `--gray-100` | `#F5F6F8` / `#EDEFF2` | Latar netral |
| `--border` | `#E1E6EB` | Seluruh garis tepi |
| `--text-900` | `#12212F` | Judul dan teks utama |
| `--text-700` | `#3B4B5A` | Teks isi |
| `--text-500` | `#6B7A88` | Teks penunjang, label |
| `--accent` | `#F6CF46` | Garis eyebrow, sorotan merek |

### Status

| Token | Teks | Latar |
|---|---|---|
| `--success` | `#1B8A5A` | `--success-bg` `#E8F5EE` |
| `--danger` | `#C0392B` | `--danger-bg` `#FBEAE8` |
| `--warning` | `#92610A` | `--warning-bg` `#FDF3D9` |
| `--violet` | `#6B3FA0` | `--violet-bg` `#F1EAFB` |

### Bentuk

| Token | Nilai |
|---|---|
| `--r-sm` | `8px` — tombol, isian, kartu kecil |
| `--r-md` | `10px` — kartu, tabel, peta |
| `--r-lg` | `16px` — panel besar |
| `--shadow-sm` | Bayangan diam |
| `--shadow-md` | Bayangan saat disorot |
| `--shadow-lg` | Bayangan lapisan mengambang |
| `--container` | `1240px` |

Font memakai tumpukan sistem (`--font`) — tidak ada webfont yang diunduh.

## 3. Tata Letak

```html
<section class="section">        <!-- jarak vertikal baku -->
  <div class="container">        <!-- lebar maks 1240px, terpusat -->
    <div class="section-head center">
      <div class="eyebrow">Label Kecil</div>
      <h2>Judul Seksi</h2>
      <p>Kalimat penjelas opsional.</p>
    </div>
    …
  </div>
</section>
```

- `.section-alt` memberi latar `--sky-50`; dipakai berselang agar seksi
  berdampingan tidak menyatu.
- `.section-sky` untuk seksi bernuansa biru penuh.
- `.grid.grid-2` … `.grid.grid-5` untuk kisi kolom.
- `.page-title-block` adalah kepala halaman di bawah remah roti.

## 4. Komponen

### Kartu

| Kelas | Bentuk |
|---|---|
| `.card` | Dasar: putih, bertepi, sudut `--r-md` |
| `.card-pad` | Menambah padding dalam |
| `.icon-card` | Ikon bulat di atas, judul, keterangan |
| `.value-card` | Kartu nilai organisasi, rata tengah |
| `.download-card` | Ikon berkas + nama + ukuran + panah |
| `.service-card` | Kartu layanan dengan tautan "Selengkapnya" |

### Tombol

| Kelas | Kegunaan |
|---|---|
| `.btn.btn-primary` | Aksi utama |
| `.btn.btn-outline` | Aksi sekunder |
| `.btn.btn-white` | Di atas latar gelap |
| `.btn-sm` | Ukuran kecil |
| `.icon-btn` | Hanya ikon, bentuk bulat |

### Tabel

```html
<div class="table-wrap">        <!-- wajib: pembungkus yang bisa digeser -->
  <table class="reg-table"> … </table>
</div>
```

`.reg-table` berkepala `--primary-dark` dan lebar minimum 720 px. Pembungkus
`.table-wrap` menahan gulir mendatar agar badan halaman tidak ikut bergeser.

Varian `.detail-table` untuk tabel dua kolom label–nilai pada halaman detail:
tanpa kepala, tanpa efek sorot.

### Lencana

`.badge` dengan `.badge-primary`, `.badge-success`, `.badge-accent`,
`.badge-gray`, `.badge-danger`. Dipakai untuk status dan penggolongan, bukan
untuk hiasan.

### Penyaring

```html
<div class="filter-bar">
  <div class="grow"><i class="bi bi-search"></i><input type="text"></div>
  <select> … </select>
</div>
```

`.chip-filter` + `.chip-radio` untuk pilihan berbentuk pil dengan titik warna —
dipakai pada filter pengelola bandar udara. Titik warnanya diisi JavaScript dari
peta warna yang sama dengan marker peta, sehingga tidak pernah berbeda.

### Paginasi

- `.pagination button` — paginasi yang digambar JavaScript.
- `.pagination .page-btn` — paginasi berbentuk tautan yang dirender server.
- `.page-gap` untuk elipsis di antara nomor yang berjauhan.

Pola nomornya: halaman pertama, kedua, tetangga halaman aktif, dua terakhir.

### Peta bandar udara

| Kelas | Bentuk |
|---|---|
| `.view-switch` | Tombol berpasangan Peta / Tabel |
| `.bandara-peta` | Wadah peta, tinggi 620 px |
| `.peta-kartu` | Kartu data di dalam tooltip dan popup, lebar 460 px, isi dua kolom |
| `.peta-kaki` | Baris keterangan dan penghitung di bawah peta |
| `.peta-reset` | Tombol kembali ke tampilan awal |

Kartu dibuat dua kolom karena satu kolom membuatnya terlalu tinggi dan terpotong
di tepi peta.

### Organisasi

| Kelas | Bentuk |
|---|---|
| `.org-unit` | Kotak unit pada bagan |
| `.org-photos` | Dua pasfoto berdampingan di dalam kotak |
| `.org-pejabat` | Kisi dua kolom berisi kartu pejabat |
| `.org-person` | Foto + nama + jabatan |
| `.org-sub` | Judul kecil berikon di dalam panel detail |

## 5. Ikon

Bootstrap Icons 1.11.3, ditulis sebagai `<i class="bi bi-nama-ikon"></i>`.
Kolom ikon di CMS menyimpan nama lengkap termasuk awalan `bi-`.

## 6. Gambar dan Placeholder

Setiap konten visual punya tiga kolom berpasangan: **Gambar**, **Warna
Placeholder** (`art-1` … `art-8`), dan **Ikon Placeholder**. Bila gambar kosong,
`art_block()` mencetak gradien berikon dengan proporsi yang sama.

Akibatnya tata letak tidak pernah berubah karena gambar belum diunggah — ini
disengaja, bukan sementara.

## 7. Responsif

Titik henti yang benar-benar dipakai di `style.css`, dari lebar ke sempit:

| Lebar | Perubahan utama |
|---|---|
| `980px` | Kisi RPJMN dan petanya menumpuk; chip filter jadi dua kolom |
| `900px` | Navbar berganti tombol burger; kotak pencarian header disembunyikan; bagan organisasi jadi satu kolom |
| `860px` | Kisi umum menyempit; penyaring layanan menumpuk |
| `768px` | Kartu dan eyebrow mengecil; kisi layanan jadi satu kolom |
| `640px` | Titik henti ponsel utama — kepala lengket, jalur penerbangan, dan bagan organisasi disesuaikan |
| `600px` | Kartu data peta jadi satu kolom |
| `560px` | Chip filter jadi satu kolom |
| `380px` | Penyesuaian terakhir untuk layar tersempit |

Aturan tetap: tabel lebar hidup di dalam `.table-wrap`; badan halaman tidak
pernah bergulir mendatar.

## 8. Menulis Teks Antarmuka

- Bahasa Indonesia, kalimat pendek, sapaan netral.
- Keadaan kosong menjelaskan langkah berikutnya, bukan sekadar "tidak ada data".
  Contoh: "Coba ubah kata kunci, pilih provinsi lain, atau atur ulang pencarian."
- Singkatan dijelaskan sekali di tempat pertama kali muncul, bukan diulang di
  setiap baris.
- Angka besar memakai pemisah ribuan gaya Indonesia (`3.407`).
