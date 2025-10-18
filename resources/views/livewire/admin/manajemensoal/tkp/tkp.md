# Ringkasan Perubahan Manajemen Soal TKP

- **Daftar Soal** (`resources/views/livewire/admin/manajemensoal/tkp/list.blade.php`)
  - Menggunakan `flux:card` dengan header yang memuat penjelasan singkat, tombol `Muat Ulang`, tombol `Import JSON`, dan tombol `Tambah Soal`.
  - Tabel menampilkan kolom `Teks Soal` dan `Aksi`, dengan tombol `Detail` serta `Hapus` per baris. Pagination Tailwind aktif melalui Livewire `ListTkp`.
  - Komponen Livewire `ListTkp` (`app/Livewire/Admin/ManajemenSoal/Tkp/ListTkp.php`) memfilter hanya variasi subtes TKP, menyediakan metode `refresh()`, dan masih menunggu implementasi aksi detail/hapus.

- **Form Tambah Soal** (`resources/views/livewire/admin/manajemensoal/tkp/create.blade.php`)
  - Disusun dalam kartu Informasi Dasar, Konten Soal, Parameter Penilaian, Skenario & Respon, Pembahasan, serta Aksi Form.
  - Mendukung penilaian berbasis skor 1–5 per opsi, dengan validasi bahwa semua skor dalam rentang dan total bobot sesuai skema TKP.

- **Import JSON** (`resources/views/livewire/admin/manajemensoal/tkp/import.blade.php`)
  - Pengguna memilih variasi TKP terlebih dahulu lalu mengunggah berkas JSON memakai `flux:input` tipe file.
  - Contoh struktur JSON TKP ditampilkan pada `<pre><code>` untuk memandu format skor perilaku 0–5 di opsi A–E.
  - Komponen Livewire `ImportTkp` (`app/Livewire/Admin/ManajemenSoal/Tkp/ImportTkp.php`) memvalidasi berkas (`required`, `mimetypes`, `max:2048`), mem-parsing isi JSON, memastikan minimal satu opsi berskor > 0, dan menyimpan soal + pembahasan secara transactional.

- **Routing** (`routes/web.php`)
  - Rute `admin/manajemen-soal/tkp`, `admin/manajemen-soal/tkp/create`, dan `admin/manajemen-soal/tkp/import` terdaftar menuju `ListTkp`, `CreateTkp`, serta `ImportTkp` dengan middleware `auth`, `verified`.

# Pekerjaan Lanjutan

- **Pengujian Import TKP**
  - Tambahkan skenario uji untuk validasi struktur JSON, skor opsi, dan penyimpanan pembahasan.

- **Aksi Detail & Hapus**
  - Implementasi rute/detail komponen serta konfirmasi hapus masih diperlukan agar tombol pada tabel berfungsi penuh.

- **Pengujian Otomatis**
  - Disarankan membuat Feature test Livewire untuk alur pembuatan soal TKP, termasuk penilaian opsi dan validasi skor.
