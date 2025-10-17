# Ringkasan Perubahan Paket Tryout

- **Daftar Paket** (`resources/views/livewire/admin/manajementryout/paket/list.blade.php`)
  - Menggunakan `flux:card` + `flux:table` untuk menampilkan nama paket, daftar konfigurasi dasar (lengkap dengan subtes, urutan, jumlah soal), durasi, harga, status aktif, dan waktu pembaruan.
  - Menyediakan tombol `Muat Ulang`, `Tambah Paket`, serta aksi toggle status.

- **Form Paket** (`resources/views/livewire/admin/manajementryout/paket/create.blade.php`)
  - Form Livewire dengan input `Nama Paket`, checklist **konfigurasi dasar** (bukan sekadar subtes), `Waktu Pengerjaan`, `Harga`, dan `Status Paket`.
  - Validasi dan pesan error via `flux:error`, tombol aksi `Simpan`, `Kembali`, `Reset`.

- **Komponen Livewire**
  - `Index` (`app/Livewire/Admin/ManajemenTryout/Paket/Index.php`) memuat relasi `konfigurasiDasar.subtes`, mendukung pagination dan toggle status.
  - `Create` (`app/Livewire/Admin/ManajemenTryout/Paket/Create.php`) memvalidasi input, membuat paket, lalu melakukan `sync` konfigurasi ke pivot `konfigurasi_ke_tryout` dengan penyimpanan urutan.

- **Model & Relasi**
  - `TryoutPaket` memiliki relasi many-to-many `konfigurasiDasar()` ke `KonfigurasiDasarSistem` melalui tabel pivot `konfigurasi_ke_tryout`.
  - `KonfigurasiDasarSistem` memiliki relasi many-to-many `paket()` ke `TryoutPaket`.

- **Database**
  - Migrasi `database/migrations/2025_10_17_012855_create_konfigurasi_ke_tryout_table.php` membuat pivot `konfigurasi_ke_tryout` (FK paket, FK konfigurasi, kolom `urutan`, index unik pendek).
  - Migrasi `database/migrations/2025_10_17_012854_create_tryout_paket_table.php` menyusun tabel `tryout_paket` dengan kolom inti (nama, durasi, harga, status).

- **Routing**
  - Rute `admin/manajemen-tryout/paket` dan `/create` pada `routes/web.php` diarahkan ke komponen Livewire `Paket\Index` & `Paket\Create` dengan middleware `auth` & `verified`.

# Pekerjaan Lanjutan

- **CRUD Lanjutan**
  - Implementasi edit/hapus paket, mengatur ulang urutan konfigurasi via UI, dan manajemen harga lanjutan.

- **Integrasi Booking**
  - Sesuaikan modul booking/registrasi peserta agar membaca konfigurasi dari pivot baru.

- **Pengujian**
  - Tambahkan feature test untuk pembuatan paket multi-konfigurasi dan tampilan daftar.
