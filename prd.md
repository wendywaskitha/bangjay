Aplikasi “Rumah Aspirasi Bang Jai” adalah portal aspirasi dan informasi bantuan pertanian berbasis Laravel + FilamentPHP 3.3 (panel admin) dan Blade + Bootstrap (front end publik) dengan penyimpanan data di MySQL. Dokumen berikut adalah PRD (Product Requirements Document) level detail, tanpa kode, yang memetakan semua fitur, struktur data, dan alur utama.

## 1. Tujuan & Sasaran Pengguna

- Menyediakan platform resmi Bang Jai untuk:
  - Menampilkan sebaran bantuan (alsintan, bibit) ke kelompok tani secara transparan.
  - Mengelola master data wilayah, kelompok tani, dan jenis bantuan dengan rapi.
  - Mempublikasikan artikel/blog, pengumuman, dan katalog bantuan ke publik.
- Pengguna utama:
  - Admin/operator (staf Bang Jai) sebagai pengelola data di panel Filament.
  - Masyarakat/petani sebagai pembaca informasi di front end publik.
- Outcome yang diharapkan:
  - Masyarakat mudah melihat siapa saja penerima bantuan, lokasi, dan jenis bantuan.
  - Bang Jai punya “rumah aspirasi” online yang profesional dan mudah dirawat.

## 2. Peran Pengguna & Hak Akses

Peran dikelola dengan Filament Shield untuk integrasi dengan `roles` & `permissions`.[1][2]

- Super Admin
  - Full access semua modul backend.
  - Kelola peran, user, dan pengaturan aplikasi.
- Admin Konten
  - Kelola Blog (kategori, artikel, profil Bang Jai, pengumuman, hero banner, katalog bantuan).
  - Tidak bisa mengubah pengaturan aplikasi tingkat sistem.
- Admin Data Bantuan
  - Kelola Master Data (wilayah, jenis bantuan, komoditas, kelompok tani) dan Sebaran Bantuan.
  - Read-only ke modul Blog jika diperlukan.
- Viewer (Opsional)
  - Hanya bisa melihat data di panel (read-only), misal untuk pimpinan.

Front end publik tidak perlu login (read-only).

## 3. Arsitektur Aplikasi (Tingkat Tinggi)

- Backend:
  - Laravel 11 + FilamentPHP v3.3 (Admin Panel).
  - Struktur: Panel tunggal `/admin` yang memuat Resources untuk semua CRUD.
  - Plugin:
    - Filament Shield (Peran & Hak Akses).
    - dotswan-map-picker untuk input `latitude` & `longitude` kelompok tani.
    - App Settings plugin untuk pengaturan aplikasi terpusat.
- Database:
  - MySQL dengan relasi terstruktur (kabupaten-kecamatan-desa, kelompok tani, jenis bantuan, komoditas, artikel, dsb).
- Front end publik:
  - Blade + Bootstrap responsive.
  - Layout: master layout + partials (`header`, `navbar`, `footer`, `hero`, dsb).
  - LeafletJS untuk peta sebaran bantuan.

## 4. Modul Backend: Master Data

### 4.1 Master Kabupaten

- Tujuan: Menyimpan daftar kabupaten yang relevan (bisa 1 atau beberapa).
- Field utama:
  - `id`, `nama_kabupaten`, `kode_kabupaten` (opsional), `created_at`, `updated_at`.
- Fitur:
  - CRUD penuh via Filament Resource.
  - Validasi: nama kabupaten unik.
- Relasi:
  - One-to-many ke Kecamatan.

### 4.2 Master Kecamatan

- Field:
  - `id`, `kabupaten_id` (FK), `nama_kecamatan`, `kode_kecamatan` (opsional).
- Fitur:
  - Form pilih `kabupaten` (select) yang terhubung ke Master Kabupaten.
  - CRUD + pencarian/filter per kabupaten.
- Relasi:
  - One-to-many ke Desa/Kelurahan.

### 4.3 Master Desa/Kelurahan

- Field:
  - `id`, `kecamatan_id` (FK), `nama_desa`, `tipe` (Desa/Kelurahan), `kode_desa` (opsional).
- Fitur:
  - Form pilih Kecamatan (select).
  - CRUD + filter by Kecamatan/Kabupaten (via relationship filter).
- Relasi:
  - One-to-many ke Kelompok Tani.

### 4.4 Kategori Bantuan

- Contoh value: Alsintan, Bibit.
- Field:
  - `id`, `nama_kategori` (unique), `deskripsi` (nullable), `is_active` (bool).
- Fitur:
  - CRUD sederhana.
- Relasi:
  - One-to-many ke Jenis Bantuan.

### 4.5 Jenis Bantuan

- Tujuan: Registrasi detail tiap jenis bantuan per periode.
- Field:
  - `id`
  - `kategori_bantuan_id` (FK)
  - `nama_bantuan`
  - `periode_tahun` (tahun anggaran, integer)
  - `deskripsi` (nullable)
  - `is_active`
- Fitur:
  - CRUD + filter berdasarkan kategori dan tahun.
  - List dapat dipakai di Katalog Bantuan & Sebaran Bantuan.
- Relasi:
  - One-to-many ke Sebaran Bantuan (1 jenis bantuan bisa tersebar ke banyak kelompok).

### 4.6 Jenis Komoditas

- Field:
  - `id`, `nama_komoditas` (padi, jagung, kakao, dsb), `deskripsi` (nullable), `is_active`.
- Fitur:
  - CRUD sederhana.
- Relasi:
  - Digunakan di repeater anggota Kelompok Tani sebagai select (FK / enum).

### 4.7 Kelompok Tani

#### 4.7.1 Data Induk Kelompok Tani

- Field:
  - `id`
  - `desa_id` (FK)
  - `nama_kelompok`
  - `jumlah_anggota` (integer)
  - `profil` (text)
  - `latitude` (nullable, decimal)
  - `longitude` (nullable, decimal)
- Pemetaan lokasi:
  - Input `latitude` & `longitude` memakai plugin dotswan-map-picker di Form Filament.
  - Validasi: minimal salah satu koordinat terisi jika ingin ikut peta sebaran bantuan.

#### 4.7.2 Anggota Kelompok Tani (Repeater)

- Model terpisah `kelompok_tani_anggota` atau JSON? Disarankan tabel terpisah agar scalable.
- Field anggota:
  - `id`, `kelompok_tani_id` (FK)
  - `nama_anggota`
  - `jabatan` (Ketua, Sekretaris, Bendahara, Anggota)
  - `no_hp`
  - `luas_lahan` (decimal)
  - `jenis_komoditas_id` (FK ke Jenis Komoditas)
- Di Filament:
  - Repeater pada form Kelompok Tani yang memetakan ke relasi hasMany (RelationManager style atau custom form relation).
  - Validasi minimal 1 anggota.
- Fitur tambahan:
  - Otomatis hitung `jumlah_anggota` dari repeater (observer/hook pada save) agar konsisten.

## 5. Modul Backend: Sebaran Bantuan

### 5.1 Struktur Data Sebaran Bantuan

- Tujuan: Mencatat bantuan apa saja yang sudah diterima suatu kelompok tani per periode.
- Field:
  - `id`
  - `kelompok_tani_id` (FK)
  - `catatan` (nullable, misal tahun, sumber anggaran, keterangan verifikasi)
  - `tanggal_penetapan` (date, optional)
- Relasi jenis bantuan (karena “bulleted, jika lebih dari 1 bantuan”):
  - Tabel pivot `sebaran_bantuan_jenis`:
    - `id`
    - `sebaran_bantuan_id` (FK)
    - `jenis_bantuan_id` (FK)
    - `volume` / `satuan` (opsional jika ingin detail kuantitas).
- Di Filament:
  - Resource `SebaranBantuanResource`.
  - Form:
    - Pilih Kelompok Tani (select searchable).
    - Repeater / CheckboxList untuk banyak `Jenis Bantuan`.
    - Field tambahan `volume`, `satuan` di dalam repeater jika diperlukan.
- Fitur:
  - Index dengan filter:
    - Per tahun bantuan (join ke `periode_tahun` di Jenis Bantuan).
    - Per kategori bantuan.
    - Per kecamatan/desa (via relasi kelompok tani → desa → kecamatan).

## 6. Modul Backend: Blog & Konten

### 6.1 Kategori Artikel

- Field:
  - `id`, `nama_kategori`, `slug`, `deskripsi` (nullable), `is_active`.
- Fitur:
  - CRUD.
  - Digunakan sebagai kategori artikel blog.

### 6.2 Artikel

- Field:
  - `id`
  - `kategori_id` (FK)
  - `judul`
  - `slug`
  - `ringkasan` (excerpt)
  - `konten` (longtext – rich text editor)
  - `thumbnail` (path file)
  - `status` (draft/published)
  - `published_at` (nullable)
  - `author_id` (FK ke users)
- Fitur:
  - CRUD article dengan editor WYSIWYG / rich text.
  - Filter by status, kategori.
  - Published-only yang tampil di front end.

### 6.3 Profil Bang Jai

- Bisa disimpan:
  - Sebagai satu record di tabel `profil_bang_jai` atau sebagai bagian dari App Settings.
- Field:
  - `judul`, `konten_profil`, `foto_profil` / `foto_banner`.
- Fitur:
  - CRUD simple (maksimum 1 active record, enforce via logic).
- Front end:
  - Tampil di Profil Page.

### 6.4 Pengumuman

- Field:
  - `id`, `judul`, `slug`, `isi`, `mulai_tayang` (date), `selesai_tayang` (date, nullable), `is_active`.
- Fitur:
  - Ditampilkan di Home Page dan/atau halaman khusus Pengumuman jika masih dalam periode tayang.

### 6.5 Hero Banner

- Field:
  - `id`, `judul`, `subjudul`, `deskripsi_singkat`, `gambar`, `cta_text`, `cta_link`, `is_active`, `urutan`.
- Fitur:
  - Bisa lebih dari satu untuk slider di home.
  - Filter `is_active` di front end.

### 6.6 Katalog Bantuan

- Tujuan: Menjelaskan deskripsi umum jenis bantuan yang tersedia/akan dibuka.
- Field:
  - `id`
  - `jenis_bantuan_id` (FK, optional atau bisa `nama_bantuan_custom` jika tidak ingin terkait)
  - `judul`
  - `deskripsi`
  - `foto`
  - `tanggal_mulai`
  - `tanggal_selesai`
  - `is_active`
- Fitur:
  - List katalog bantuan di front end dengan filter status (berlangsung / akan datang / selesai).

## 7. Modul Backend: Pengaturan

### 7.1 Peran & Hak Akses (Filament Shield)

- Entity:
  - `roles`, `permissions`, `model_has_roles`, dll (mengikuti package).
- Fitur:
  - Generate permission per Resource.
  - UI di admin untuk assign role ke user.
- Kebutuhan:
  - Mapping default: `super_admin`, `admin_konten`, `admin_data_bantuan`.

### 7.2 Profil Saya

- Di panel Filament:
  - Halaman profil user login.
- Field:
  - `foto_profile`
  - `name`
  - `username` (unique)
  - `password` + `password_confirmation` (opsional, hanya saat ganti)
  - `role` (hanya bisa diubah oleh Super Admin; user biasa hanya lihat)
- Fitur:
  - Upload foto profil.
  - Ubah password dengan validasi kuat.

### 7.3 Setting Aplikasi (App Settings Plugin)

- Menggunakan plugin App Settings agar pengaturan tersentral.[2][1]
- Contoh group settings:
  - General:
    - `app_name` (Rumah Aspirasi Bang Jai)
    - `app_logo`, `app_favicon`
  - Kontak:
    - `alamat_kantor`
    - `no_telepon`
    - `email`
    - `link_whatsapp`
    - `link_facebook`, `link_youtube`, dll.
  - Tampilan:
    - `footer_text`
    - `hero_default_title` jika tidak ada data hero.
- Semua setting dibaca oleh Blade dan panel sesuai kebutuhan.

## 8. Front End: Struktur Umum

### 8.1 Teknologi & Layout

- Blade + Bootstrap 5, fokus responsive mobile-first.
- Struktur partials:
  - `layouts/app.blade.php`
  - `partials/navbar.blade.php`
  - `partials/footer.blade.php`
  - `partials/hero.blade.php`
  - `partials/breadcrumbs.blade.php` (opsional)
  - `partials/alerts.blade.php` (untuk flash message, jika diperlukan).
- SEO dasar:
  - Dynamic `<title>`, `<meta description>` untuk tiap page (ditarik dari DB atau default).

### 8.2 Home Page

Konten utama:

- Hero Banner:
  - Slider dari tabel Hero Banner (hanya `is_active`).
- Highlight:
  - Sekilas Profil Bang Jai (ringkasan + link ke halaman Profil).
  - Highlight Pengumuman terbaru (3 item).
- Katalog Bantuan:
  - Grid 3–6 item bantuan aktif (dalam rentang tanggal).
- Statistik singkat (opsional):
  - Jumlah Kelompok Tani, Jumlah Bantuan, Jumlah Artikel.

### 8.3 Profil Page

- Menampilkan Profil Bang Jai:
  - Judul, konten lengkap, foto / banner.
- Bisa ditambah:
  - Struktur organisasi simple atau gambar bagan jika nanti dibutuhkan (out of scope awal, tapi schema disiapkan di konten).

### 8.4 Katalog Bantuan Page

- List semua Katalog Bantuan dengan filter:
  - Berdasarkan periode tanggal (berlangsung / akan datang / selesai).
  - Berdasarkan kategori bantuan (Alsintan/Bibit – join melalui Jenis Bantuan).
- Detail halaman bantuan:
  - Judul, deskripsi, foto, periode, jenis bantuan terkait (kategori, tahun).

### 8.5 Sebaran Bantuan (Map Leaflet) Page

- Peta Leaflet:
  - Base map (misal OSM).
  - Marker lokasi Kelompok Tani yang punya Sebaran Bantuan.
- Fitur:
  - Filter di sidebar/top:
    - Tahun bantuan (dari `periode_tahun`).
    - Kategori (Alsintan/Bibit).
    - Kecamatan/Desa.
  - Klik marker:
    - Popup menampilkan:
      - Nama Kelompok Tani.
      - Desa/Kecamatan.
      - Daftar jenis bantuan (bulleted dari relasi pivot).
      - Tahun/Periode.
- Kebutuhan data:
  - Hanya kelompok dengan `latitude` & `longitude` dan punya minimal 1 Sebaran Bantuan.

### 8.6 Kelompok Tani (penerima bantuan) Page

- List kelompok tani yang sudah mendapatkan bantuan:
  - Table atau card view.
  - Informasi: Nama, Desa, Kecamatan, jumlah anggota, komoditas dominan (bisa dari anggota ketua, atau agregat).
- Filter:
  - Kecamatan, Desa, Tahun bantuan, Kategori bantuan.
- Detail kelompok tani:
  - Informasi profil kelompok.
  - Daftar anggota (opsi tampilkan ringkas – nama & jabatan).
  - Riwayat bantuan (list dari Sebaran Bantuan + jenis).

### 8.7 Kontak Kami Page

- Konten:
  - Alamat kantor, email, no telp, WhatsApp (dari App Settings).
  - Form kontak sederhana (opsional, bisa tahap berikut):
    - Nama, email, pesan → disimpan di tabel `kontak` atau dikirim email.
- Opsional:
  - Embed peta kantor menggunakan Leaflet (koordinat kantor dari settings).

## 9. Non-Fungsional Requirements

- Performa:
  - Index page (Home, Sebaran Bantuan) harus cepat; gunakan eager loading & indeks DB pada FK.
- Keamanan:
  - Otentikasi Filament standar + password hashing.
  - Role-based access control via Filament Shield.
- Audit (opsional tahap 2):
  - Tambah `created_by`, `updated_by` di beberapa tabel penting (Kelompok Tani, Sebaran Bantuan) untuk tracking.
- Maintainability:
  - Pemisahan jelas antara panel admin (Filament) dan front end Blade.
  - Partials Blade meminimalkan duplikasi layout.

## 10. Tahapan Implementasi (Tanpa Koding Detil)

1. Setup dasar:
   - Project Laravel + instalasi Filament 3.3, Shield, App Settings, dotswan-map-picker.
   - Desain schema database & migrasi semua tabel master + blog + sebaran.
2. Implementasi Resources Filament:
   - Urutan: Master Wilayah → Kategori/Jenis Bantuan → Komoditas → Kelompok Tani (+ repeater anggota) → Sebaran Bantuan → Blog & Konten → Pengaturan.
3. Implementasi Front End:
   - Layout & partials → Home → Profil → Katalog → Sebaran (Leaflet) → Kelompok Tani → Kontak.
4. UAT & data seeding awal:
   - Isi master wilayah, contoh data kelompok tani, jenis bantuan, artikel, dsb.
5. Go live & iterasi:
   - Tambah fitur lanjutan seperti formulir aspirasi online atau notifikasi (tahap berikut).

Jika ingin, langkah selanjutnya bisa disusun ERD tabel satu per satu (nama tabel + field + tipe data + indeks) agar implementasi MySQL lebih rapi sejak awal.

[1](https://filamentphp.com/docs/3.x/panels/configuration)
[2](https://filamentphp.com/docs/3.x/panels/resources/getting-started)
[3](https://www.tiktok.com/@carik.jakarta/video/7551636133675912464)
[4](https://www.youtube.com/watch?v=-2hlJN5c8IA)
[5](https://www.tiktok.com/@dukcapiljakarta/video/7449725531290963207)
[6](https://sumut.kemenag.go.id/upload/majalah/444183966_AGUSTUS_compressed.pdf)
[7](https://polkam.go.id/konten/unggahan/2023/05/Buku_LapTah_2022_Deputi-Kesbang-compressed-2-1.pdf)
[8](https://www.youtube.com/watch?v=I7I8v2N2OkY)
[9](https://psp.pertanian.go.id/storage/1686/Juknis-Banpem-2024.pdf)
[10](https://jdih.sumutprov.go.id/assets/reg/1726029250278_LAMPIRAN_PERGUB_NOMOR_18_TAHUN_2024.pdf)
[11](https://dpkp.fakfakkab.go.id/?p=183)
[12](https://simlitbang.balitbangdiklat.net/assets_front/pdf/1668573038Together.pdf)
[13](https://filamentphp.com/docs/1.x/admin/resources)
[14](https://ppid.kemendagri.go.id/front/dokumen/download/500090103)
[15](https://repository.ar-raniry.ac.id/id/eprint/6918/1/Muhammad%20Furqan.pdf)
[16](https://filamentphp.com/docs/3.x/panels/pages)
[17](https://distanbun.acehprov.go.id)
[18](http://cipg.or.id/wp-content/uploads/2015/06/MEDIA-3-Kelompok-Rentan-2012.pdf)
[19](https://laraveldaily.com/post/filament-v3-nested-resources-trait-pages)
[20](https://lldikti6.id/wp-content/uploads/2025/07/20250523_Lampiran-II-Daftar-Penerima-Pendanaan-Program-Pengabdian-kepada-Masyarakat-Tahun-Pelaksanaan-2025.pdf)
