# Tryout Booking

## Sebelum Rencana
- Sistem peserta sudah dapat melihat paket tryout di `resources/views/livewire/peserta/tryouttersedia/list.blade.php`.
- Komponen Livewire `App\Livewire\Peserta\TryoutTersedia\ListTryoutTersedia` masih memakai data contoh, belum terhubung ke database.
- Belum ada struktur tabel `tryout_booking`; migrasi `2025_10_18_051952_create_tryout_booking_table.php` masih kosong.
- Belum ada mekanisme bagi peserta untuk mendaftar paket atau menyimpan status booking.

## Rencana
- Definisikan skema tabel `tryout_booking` meliputi referensi ke peserta (`users`) dan paket (`tryout_paket`), status booking, jadwal, dan metadata pembayaran.
- Update model terkait (mis. `TryoutPaket`, `User`) untuk relasi booking serta buat model `TryoutBooking`.
- Sesuaikan komponen Livewire peserta untuk membaca paket aktual, menampilkan status booking, dan menyediakan aksi daftar/batal.
- Tambahkan validasi dan logika pendaftaran tryout (contoh: mengecek apakah peserta sudah terdaftar, kuota tersedia, dsb.) serta feedback ke pengguna.
