# Mantra - Backend API (Sistem Kasir Toko)

Repository ini berisi **Backend / Server-side** untuk aplikasi **Mantra**, sebuah sistem Point of Sale (POS) yang dibangun menggunakan **Laravel 12**.

Backend ini menyediakan REST API untuk menangani autentikasi pengguna, manajemen inventaris, transaksi penjualan, dan pelaporan data.

> **Catatan:** Frontend (React) untuk aplikasi ini terdapat di repository terpisah:
> **https://github.com/CorvoGithub/aplikasi-kasirtoko-frontend**

---

## Fitur Backend

Backend ini menangani logika bisnis sesuai spesifikasi **Soal Tes Tipe 2**:

1.  **Authentication (Sanctum):**
    * Register & Login dengan validasi email unik.
    * Enkripsi password menggunakan Bcrypt.
    * Pemisahan data antar user (Multi-tenancy logic).
2.  **Database Management:**
    * Struktur tabel relasional untuk User, Products, Transactions, dan Transaction Details.
    * Foreign Key constraints untuk integritas data.
3.  **API Endpoints:**
    * CRUD Barang (Upload gambar, validasi stok).
    * Proses Transaksi (Pencatatan detail item, kalkulasi total).
    * Reporting (Filter riwayat berdasarkan tanggal/jam).

---

## Teknologi

* **Framework:** Laravel 12
* **Database:** PostgreSQL
* **Auth:** Laravel Sanctum
* **Storage:** Local Storage (Public Link)

---

## Struktur Database

File database lengkap (`kasirtoko_structure.sql`) telah disertakan di root repository ini.

* **`users`**: Data pemilik toko.
* **`products`**: Inventaris barang (terikat ke `user_id`).
* **`transactions`**: Header penjualan (Kode invoice, Total, Pembayaran).
* **`transaction_details`**: Item spesifik per transaksi.

---

## Cara Menjalankan (Localhost)

Ikuti langkah ini untuk menjalankan server API:

1.  **Clone Repository & Install Dependencies:**
    ```bash
    git clone https://github.com/CorvoGithub/aplikasi-kasirtoko-backend
    cd kasirtoko-backend
    composer install
    ```

2.  **Setup Environment:**
    * Duplikat file `.env.example` menjadi `.env`.
    * Generate Application Key:
        ```bash
        php artisan key:generate
        ```
    * Sesuaikan konfigurasi database di file `.env`:
        ```env
        DB_DATABASE=kasirtoko
        DB_USERNAME=**your_username**
        DB_PASSWORD=**your_password**
        ```

3.  **Migrasi & Storage:**
    Lakukan migrasi database dan buka akses folder storage publik (Penting untuk gambar produk):
    ```bash
    php artisan migrate
    php artisan storage:link
    ```

4.  **Jalankan Server:**
    ```bash
    php artisan serve
    ```
    *Server akan berjalan di: http://127.0.0.1:8000*

---

## 📹 Video Demonstrasi

Berikut adalah link video penjelasan kode, struktur database, dan demonstrasi penggunaan aplikasi:

**https://drive.google.com/drive/folders/1rEJgcqvZjZ-vCUCDXXSrLgVeihcP7nqi?usp=drive_link**

---

**Dibuat oleh:** Andhika Farizky Mansyur
