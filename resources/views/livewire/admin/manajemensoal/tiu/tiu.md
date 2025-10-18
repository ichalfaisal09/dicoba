# Ringkasan Perubahan Manajemen Soal TIU

- **Daftar Soal** (`resources/views/livewire/admin/manajemensoal/tiu/list.blade.php`)
  - Menggunakan `flux:card` dengan heading, deskripsi, tombol `Muat Ulang`, tombol `Import JSON`, dan `Tambah Soal` yang menavigasi ke halaman create/import.
  - Tabel menampilkan kolom `Teks Soal` dan `Aksi`, dengan tombol `Detail` serta `Hapus` di setiap baris. Pagination memanfaatkan Livewire `ListTiu`.
  - Komponen Livewire `ListTiu` (`app/Livewire/Admin/ManajemenSoal/Tiu/ListTiu.php`) memfilter `SoalPertanyaan` berdasarkan subtes `TIU`, menyediakan `refresh()`, serta placeholder `confirmDeletion()`.

- **Form Tambah Soal** (`resources/views/livewire/admin/manajemensoal/tiu/create.blade.php`)
  - Disusun dalam lima kartu: Informasi Dasar, Konten Soal, Opsi Jawaban A–E, Pembahasan & Referensi, dan Aksi Form.
  - Dropdown variasi menggunakan `<optgroup>` per materi, bersumber dari `CreateTiu::render()` yang memfilter subtes `TIU`.
  - Opsi jawaban menggunakan input skor integer 0–5 dan textarea teks, dilengkapi helper text sesuai pedoman desain.

- **Komponen Livewire Create** (`app/Livewire/Admin/ManajemenSoal/Tiu/CreateTiu.php`)
  - Menyediakan state lengkap (`variasiId`, `teksSoal`, `tingkatKesulitan`, `opsi` A–E, `teksPembahasan`, `referensi`) serta generator kode otomatis `TIU-xxx`.
  - Validasi memastikan minimal satu opsi memiliki skor > 0, skor berada pada rentang 0–5, dan referensi opsional maksimal 255 karakter.
  - Metode `store()` menyimpan `SoalPertanyaan`, `SoalOpsiJawaban`, dan `SoalPembahasan` (opsional) dalam transaksi DB serta memberikan callout sukses.
  - Metode `resetForm()` mengembalikan state default, menghasilkan kode baru, dan mereset validasi.

- **Import JSON** (`resources/views/livewire/admin/manajemensoal/tiu/import.blade.php`)
  - Halaman memuat callout sesi, form pemilihan variasi TIU, unggah berkas JSON, serta aksi `Import`, `Reset`, dan kembali ke daftar.
  - Contoh struktur JSON TIU ditampilkan melalui `<pre><code>` agar sesuai dengan komponen Flux.
  - Komponen Livewire `ImportTiu` (`app/Livewire/Admin/ManajemenSoal/Tiu/ImportTiu.php`) memvalidasi berkas (`required`, `mimetypes`, `max:2048`), mem-parsing tiap soal, memastikan minimal satu opsi berskor > 0, dan menyimpan soal beserta pembahasan dalam transaksi.

- **Routing** (`routes/web.php`)
  - Rute `admin/manajemen-soal/tiu`, `admin/manajemen-soal/tiu/create`, dan `admin/manajemen-soal/tiu/import` diarahkan ke `ListTiu`, `CreateTiu`, dan `ImportTiu` dengan middleware `auth`, `verified`.

- **Navigasi** (`resources/views/components/layouts/app/sidebar.blade.php`)
  - Menu Manajemen Soal kini menyertakan tautan `Soal TIU` yang menyorot rute aktif dan mendukung `wire:navigate`.

# Pekerjaan Lanjutan

- **Aksi Detail & Hapus**
  - Perlu implementasi rute/komponen detail serta mekanisme konfirmasi sebelum menghapus soal TIU pada tombol tabel.

- **Pengujian Import TIU**
  - Tambahkan skenario uji untuk validasi struktur JSON, skor opsi, dan penyimpanan pembahasan.

- **Pengujian Otomatis**
  - Disarankan menambah Feature test Livewire untuk alur pembuatan soal TIU guna memastikan validasi dan penyimpanan bekerja stabil.
