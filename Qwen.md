# Qwen.md - Rumah Aspirasi Bang Jai

## Deskripsi Proyek

Aplikasi "Rumah Aspirasi Bang Jai" adalah portal aspirasi dan informasi bantuan pertanian berbasis Laravel + FilamentPHP 3.3 (panel admin) dan Blade + Bootstrap (front end publik) dengan penyimpanan data di MySQL.

## Tujuan & Sasaran Pengguna

- Menyediakan platform resmi Bang Jai untuk:
  - Menampilkan sebaran bantuan (alsintan, bibit) ke kelompok tani secara transparan.
  - Mengelola master data wilayah, kelompok tani, dan jenis bantuan dengan rapi.
  - Mempublikasikan artikel/blog, pengumuman, dan katalog bantuan ke publik.
- Pengguna utama:
  - Admin/operator (staf Bang Jai) sebagai pengelola data di panel Filament.
  - Masyarakat/petani sebagai pembaca informasi di front end publik.
- Outcome yang diharapkan:
  - Masyarakat mudah melihat siapa saja penerima bantuan, lokasi, dan jenis bantuan.
  - Bang Jai punya "rumah aspirasi" online yang profesional dan mudah dirawat.

## Arsitektur Aplikasi

- Backend:
  - Laravel 12 + FilamentPHP v3.3 (Admin Panel)
  - Plugin:
    - Filament Shield (Peran & Hak Akses)
    - dotswan-map-picker untuk input `latitude` & `longitude` kelompok tani
    - App Settings plugin untuk pengaturan aplikasi terpusat
- Database:
  - MySQL dengan relasi terstruktur
- Front end publik:
  - Blade + Bootstrap responsive
  - LeafletJS untuk peta sebaran bantuan

## Modul Backend

### 1. Master Data
- Master Kabupaten
- Master Kecamatan
- Master Desa/Kelurahan
- Kategori Bantuan
- Jenis Bantuan
- Jenis Komoditas
- Kelompok Tani (dengan anggota sebagai repeater)

### 2. Sebaran Bantuan
- Mencatat bantuan yang diterima kelompok tani per periode

### 3. Blog & Konten
- Kategori Artikel
- Artikel
- Profil Bang Jai
- Pengumuman
- Hero Banner
- Katalog Bantuan

### 4. Pengaturan
- Peran & Hak Akses (Filament Shield)
- Profil Saya
- Setting Aplikasi (App Settings Plugin)

## Modul Frontend

### 1. Halaman Utama
- Hero Banner
- Highlight Profil Bang Jai
- Pengumuman terbaru
- Katalog Bantuan
- Statistik singkat

### 2. Profil Page
- Menampilkan informasi Bang Jai

### 3. Katalog Bantuan Page
- List katalog bantuan dengan filter

### 4. Sebaran Bantuan (Map Page)
- Peta Leaflet dengan marker kelompok tani yang terima bantuan

### 5. Kelompok Tani Page
- List kelompok tani penerima bantuan

### 6. Kontak Kami Page
- Informasi kontak dan form

## Task Implementasi

1. [x] Setup dasar proyek Laravel + instalasi Filament 3.3, Shield, App Settings, dotswan-map-picker
2. [x] Desain schema database & migrasi semua tabel master + blog + sebaran
3. [x] Implementasi Resources Filament untuk Master Wilayah
4. [x] Implementasi Resources Filament untuk Kategori/Jenis Bantuan
5. [x] Implementasi Resources Filament untuk Komoditas
6. [x] Implementasi Resources Filament untuk Kelompok Tani (dengan repeater anggota)
7. [x] Implementasi Resources Filament untuk Sebaran Bantuan
8. [x] Implementasi Resources Filament untuk Blog & Konten
9. [x] Implementasi Resources Filament untuk Pengaturan
10. [x] Pembuatan layout & partials frontend (Blade + Bootstrap)
11. [x] Implementasi halaman Home
12. [x] Implementasi halaman Profil
13. [x] Implementasi halaman Katalog Bantuan
14. [x] Implementasi halaman Sebaran (Leaflet)
15. [x] Implementasi halaman Kelompok Tani
16. [x] Implementasi halaman Kontak
17. [x] Testing dan penyesuaian
18. [x] UAT & data seeding awal
19. [x] Deployment dan iterasi

## Status Proyek: SELESAI