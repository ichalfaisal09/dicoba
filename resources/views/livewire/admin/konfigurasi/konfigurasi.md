# Ringkasan Perubahan Konfigurasi

- **Daftar Konfigurasi** (`resources/views/livewire/admin/konfigurasi/list.blade.php`)
  - Menggunakan `flux:card` + tabel Flux untuk menampilkan subtes, nama konfigurasi, jumlah soal, urutan, nilai minimal, serta waktu pembaruan.
  - Menyediakan callout informasi, tombol `Muat Ulang`, dan tombol `Tambah Konfigurasi`.
  - Menampilkan state kosong ketika belum ada data.

- **Komponen Livewire**
  - `Index` (`app/Livewire/Admin/Konfigurasi/Index.php`) memuat `KonfigurasiDasarSistem::with('subtes')`, menggunakan pagination, dan menyediakan aksi `refresh()`.
  - `Create` (`app/Livewire/Admin/Konfigurasi/Create.php`) memvalidasi subtes, nama, jumlah soal, urutan, nilai minimal; menyimpan konfigurasi baru dan menampilkan callout.

- **Integrasi dengan Paket Tryout**
  - `KonfigurasiDasarSistem` kini memiliki relasi many-to-many `paket()` melalui pivot `konfigurasi_ke_tryout`, memungkinkan konfigurasi dipakai oleh beberapa paket.
  - Perubahan ini tercermin pada `app/Models/KonfigurasiDasarSistem.php` dan dokumentasi paket (`resources/views/livewire/admin/manajementryout/paket/paket.md`).

- **Routing & Navigasi**
  - Rute `admin/konfigurasi` dan `admin/konfigurasi/create` di `routes/web.php` diarahkan ke `Konfigurasi\Index` dan `Konfigurasi\Create` dengan middleware `auth` & `verified`.
  - Sidebar (`resources/views/components/layouts/app/sidebar.blade.php`) menautkan menu "Konfigurasi Dasar Sistem" ke halaman ini.

# Pekerjaan Lanjutan

- **CRUD Lengkap**
  - Tambah fitur edit/hapus konfigurasi dan dukungan pencarian/filter.

- **Validasi bisnis**
  - Tetapkan aturan agar urutan/jumlah soal tetap konsisten per subtes.

- **Integrasi Tryout**
  - Kembangkan UI paket untuk menentukan urutan konfigurasi secara manual bila diperlukan.

- **Pengujian**
  - Tambahkan feature test Livewire untuk memastikan alur create dan daftar bekerja stabil.
