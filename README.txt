============================================================
🚀 MYINVEST - INVESTMENT MANAGEMENT SYSTEM (MVP)
============================================================

Aplikasi manajemen investasi saham dan kripto berbasis web dengan 
fitur simulasi pasar, dompet digital, dan analisis portofolio.

Dibuat bersama: Oja (AI Partner)
Tech Stack: Laravel, MySQL, Tailwind CSS, Chart.js, SweetAlert2.

============================================================
1. CARA MENYALAKAN APLIKASI (HOW TO RUN)
============================================================

Pastikan XAMPP (MySQL) sudah menyala.

Buka 2 Terminal berbeda:

TERMINAL 1 (Untuk menjalankan Server Laravel):
   cd /path/ke/project/kamu
   php artisan serve

TERMINAL 2 (Untuk menjalankan CSS/Vite):
    Windows (PowerShell) yang secara default memblokir eksekusi script asing (termasuk script npm).
    PowerShell (Run a Admin) - Set-ExecutionPolicy RemoteSigned
   cd /path/ke/project/kamu
   npm run dev

-> Akses aplikasi di browser: http://127.0.0.1:8000

PENTING:
Jika gambar bukti transfer atau avatar tidak muncul, jalankan perintah ini sekali saja:
   php artisan storage:link

============================================================
2. STATUS FITUR SAAT INI (COMPLETED)
============================================================

[A] SISI USER (INVESTOR)
-------------------------
1. Dashboard
   - Ringkasan total kekayaan (Uang Tunai + Nilai Aset).
   - Grafik Donat (Chart.js) alokasi aset.
   - Tabel Portofolio dengan indikator Profit/Loss (Hijau/Merah).

2. Wallet (Dompet & RDN)
   - Multi-Account: Bisa tambah banyak dompet (RDN BCA, Crypto Wallet, dll).
   - Tampilan Kartu Virtual (Gradient UI).
   - Top Up Saldo (Upload Bukti Transfer).
   - Withdraw (Tarik Dana).

3. Market (Pasar)
   - Daftar harga aset Real-time (Sync dari CoinGecko).
   - Fitur Pencarian Instan (Search).
   - Shortcut tombol "Beli".

4. Transaksi
   - Beli Aset (Buy): Support input harga manual & tanggal mundur (Backdate) untuk pencatatan transaksi luar (OKX/Binance).
   - Jual Aset (Sell): Menghitung realisasi keuntungan.
   - History: Riwayat lengkap dengan status (Pending/Approved).

5. Profil
   - Ganti Foto Profil (Avatar).
   - Update Nama & Password.

[B] SISI ADMIN (BACKOFFICE)
---------------------------
1. Approval System
   - Cek Bukti Transfer User -> Approve/Reject Top Up.
   - Cek Request Withdraw -> Approve/Reject.

2. Manajemen Aset
   - CRUD Aset (Tambah/Edit/Hapus).
   - Sync Harga Otomatis via API CoinGecko.
   - Update harga manual.

[C] UI/UX UPGRADE
---------------------------
- Landing Page Dark Mode (Ala Startup Crypto).
- Sidebar Navigasi Responsif.
- SweetAlert2 (Popup Notifikasi Cantik).
- Loading State (Tombol berputar saat diproses).
- Hover Effects & Glassmorphism.

============================================================
3. CATATAN PENGEMBANGAN
============================================================
- Database: finance_app
- API Harga: CoinGecko Public API (Gratis)
- Keamanan: Middleware 'auth' dan 'admin'.

============================================================
Happy Coding! 🚀