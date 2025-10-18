# Ringkasan Perubahan Manajemen Soal TWK

- **Daftar Soal** (`resources/views/livewire/admin/manajemensoal/twk/list.blade.php`)
  - Menggunakan `flux:card` dengan header yang memuat penjelasan singkat, tombol `Muat Ulang`, tombol `Import JSON`, dan tombol `Tambah Soal`.
  - Tabel menampilkan kolom `Teks Soal` dan `Aksi`, dengan tombol `Detail` serta `Hapus` per baris. Pagination Tailwind aktif melalui Livewire `ListTwk`.
  - Komponen Livewire `ListTwk` (`app/Livewire/Admin/ManajemenSoal/Twk/ListTwk.php`) memfilter soal berdasarkan subtes TWK (`whereHas('variasi.materi.subtes', 'TWK')`), menyediakan metode `refresh()`, dan masih menunggu aksi detail/hapus.

- **Form Tambah Soal** (`resources/views/livewire/admin/manajemensoal/twk/create.blade.php`)
  - Terbagi dalam empat kartu: Informasi Dasar, Konten Soal, Opsi Jawaban A–E, serta Pembahasan & Aksi Form.
  - Dropdown variasi menampilkan kelompok `<optgroup>` berdasarkan nama materi, bersumber dari `CreateTwk::render()` yang memfilter subtes TWK.
  - Opsi jawaban memiliki input skor integer 0–5 dan textarea teks, masing-masing disertai helper text agar konsisten dengan pedoman desain.

- **Komponen Livewire Create** (`app/Livewire/Admin/ManajemenSoal/Twk/CreateTwk.php`)
  - Menyediakan state lengkap (`variasiId`, `teksSoal`, `tingkatKesulitan`, `opsi` A–E, `teksPembahasan`, `referensi`) dan generator kode otomatis `TWK-xxx`.
  - Validasi memastikan satu opsi minimal berskor > 0, skor berada di rentang 0–5, serta referensi opsional maks 255 karakter.
  - Metode `store()` menyimpan `SoalPertanyaan`, `SoalOpsiJawaban`, dan `SoalPembahasan` (bila ada), menggunakan transaksi database dan menampilkan callout sukses.
  - Metode `resetForm()` mengembalikan state ke nilai default sembari menghasilkan kode baru dan mereset validasi.

- **Import JSON** (`resources/views/livewire/admin/manajemensoal/twk/import.blade.php`)
  - Pengguna memilih variasi TWK terlebih dahulu lalu mengunggah berkas JSON memakai `flux:input` tipe file dengan bantuan `WithFileUploads`.
  - Contoh struktur JSON ditampilkan menggunakan elemen `<pre><code>` bergaya Tailwind agar kompatibel dengan komponen Flux.
  - Komponen Livewire `ImportTwk` (`app/Livewire/Admin/ManajemenSoal/Twk/ImportTwk.php`) memvalidasi file (`required`, `mimetypes`, `max:2048`), membersihkan error ketika file diganti (`updatedJsonFile()`), melakukan parsing + validasi tiap soal (`opsi` A–E, skor > 0), serta menyimpan data dalam transaksi.

- **Routing** (`routes/web.php`)
  - Rute `admin/manajemen-soal/twk`, `admin/manajemen-soal/twk/create`, dan `admin/manajemen-soal/twk/import` terdaftar menuju `ListTwk`, `CreateTwk`, serta `ImportTwk` dengan middleware `auth`, `verified`.

# Pekerjaan Lanjutan

- **Aksi Detail & Hapus**
  - Implementasi rute/detail komponen serta konfirmasi hapus masih diperlukan agar tombol pada tabel berfungsi penuh.

- **Preview Opsi**
  - Pertimbangkan penambahan preview ringkas jawaban benar sebelum simpan untuk mengurangi kesalahan skor.

- **Pengujian Otomatis**
  - Disarankan membuat Feature test Livewire untuk alur pembuatan soal serta skenario import JSON (valid/invalid) guna memastikan konsistensi validasi.
