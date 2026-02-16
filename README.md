# SkyBooking – Backend API Documentation
Laravel REST API + MySQL + Filament PHP • Last Updated: February 2026

📌 **Table of Contents**
1. [Project Overview](#-project-overview)
2. [Core Features](#-core-features)
3. [Authentication & Access Control](#-authentication--access-control)
4. [API Routing Structure](#-api-routing-structure)
5. [Tech Stack](#-tech-stack)
6. [Database Design](#-database-design)
7. [API Endpoints](#-api-endpoints)
8. [Middleware & Security](#-middleware--security)
9. [Installation & Setup](#-installation--setup)
10. [Author](#-author)

---

## 🎯 Project Overview
**SkyBooking Backend** adalah RESTful API yang dibangun menggunakan Laravel untuk mengelola sistem pemesanan tiket pesawat. Dirancang untuk melayani kebutuhan data frontend (Vite/React) dengan fitur manajemen penerbangan, kursi, fasilitas, hingga transaksi multi-penumpang.

**Fungsi Utama:**
*   Manajemen otentikasi & otorisasi pengguna.
*   Sistem pencarian penerbangan dengan filter kompleks (Bandara, Tanggal).
*   Manajemen kursi (Seats) dan kelas penerbangan (Economy, Business, First).
*   Proses transaksi pemesanan tiket dengan dukungan multi-penumpang.
*   Admin Dashboard powerfull menggunakan Filament PHP.
*   Status Produksi: Ready for Deployment (Railway/Render) with MySQL/PostgreSQL.

---

## 🌟 Core Features
### 1️⃣ Flight & Discovery System
*   Listing bandara dan maskapai.
*   Pencarian tiket pesawat berdasarkan rute dan tanggal keberangkatan.
*   Detail penerbangan lengkap dengan informasi transit (Segments), fasilitas, dan harga kelas.

### 2️⃣ Booking & Transaction Flow
*   Pemeriksaan kode promo sebelum transaksi.
*   Pemesanan tiket untuk banyak penumpang sekaligus dalam satu transaksi.
*   Manajemen status pembayaran (Pending, Paid, Failed).
*   Riwayat transaksi bagi pengguna yang terautentikasi.

### 3️⃣ Role-Based Access Control
*   **User**: Menjelajahi jadwal, memesan tiket, dan melihat riwayat profil.
*   **Admin**: Akses penuh (CRUD) melalui Filament Dashboard untuk mengelola semua data master (Penerbangan, Pesawat, Kode Promo).

---

## 🔐 Authentication & Access Control
### Auth Flow
1. Client mengirim kredensial login ke `/api/login`.
2. Backend memvalidasi user dan menghasilkan **Sanctum Token**.
3. Client wajib menyertakan token tersebut di header `Authorization: Bearer <token>` untuk rute terproteksi.

### Access Control Rules
| Route Group | Middleware | Izin Akses |
| :--- | :--- | :--- |
| **Public** | None | Tamu (Guest) |
| **Authenticated** | `auth:sanctum` | User & Admin |
| **Admin Only** | `auth:sanctum` + `is_admin` | Admin Saja |

---

## 🗺️ API Routing Structure
**routes/api.php**
```text
│
├── Public Routes (No Auth)
│   ├── POST   /register
│   ├── POST   /login
│   ├── GET    /airports
│   ├── GET    /flights
│   └── GET    /flights/{id}
│
├── Authenticated Routes (User & Admin)
│   ├── GET    /user (Profile)
│   ├── POST   /logout
│   ├── POST   /promo-codes/check
│   ├── GET    /transactions
│   └── POST   /transactions (Checkout)
│
└── Admin Routes (Admin Only)
    ├── CRUD   /airports, /airlines, /facilities
    ├── CRUD   /promo-codes, /flights
    ├── GET/PU /admin/transactions (Management)
    └── CRUD   /users (User Management)
```

---

## ⚙️ Tech Stack

| Category | Technology |
| :--- | :--- |
| **Framework** | Laravel 12 |
| **Admin Panel** | Filament PHP v3 |
| **Language** | PHP 8.2+ |
| **Database** | MySQL / PostgreSQL |
| **Auth** | Laravel Sanctum |
| **API Format** | JSON REST |

---

## 🗄️ Database Design
### ERD Visualization
> [Link to ERD Diagram](https://app.diagrams.net/) 

### Key Tables
*   **users**: Mengelola data user dan role (admin/user).
*   **flights**: Data utama penerbangan (nomor pesawat, maskapai).
*   **flight_segments**: Mengelola detail rute dan waktu transit.
*   **flight_seats**: Detail kursi per pesawat (nomor kursi, ketersediaan).
*   **transactions**: Data transaksi, total harga, dan status pembayaran.

---

## 🌐 API Endpoints

### Authentication
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/register` | Pendaftaran user baru |
| `POST` | `/login` | Autentikasi & get token |
| `POST` | `/logout` | Revoke/hapus token |

### Flight Discovery
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/flights` | Mencari penerbangan dengan filter |
| `GET` | `/flights/{id}` | Detail penerbangan & data kursi |
| `GET` | `/airports` | Menampilkan semua bandara |

### Transactions (User)
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/transactions` | Riwayat transaksi user |
| `POST` | `/transactions` | Checkout / Simpan booking baru |
| `POST` | `/promo-codes/check` | Validasi kode promo |

---

## 🛡️ Middleware & Security
*   **CORS Configuration**: Sudah mendukung akses dari domain frontend (localhost/Vercel).
*   **auth:sanctum**: Proteksi rute menggunakan Bearer Token.
*   **is_admin**: Proteksi level tinggi untuk rute manajemen data.
*   **Validation**: Menggunakan Laravel Request Validation untuk memastikan data bersih (422 Unprocessable Entity).

---

## 🚀 Installation & Setup

1. **Clone & Install**:
   ```bash
   git clone <repo-url>
   composer install
   ```

2. **Environment**:
   Salin `.env.example` menjadi `.env` dan atur konfigurasi database Anda.

3. **Database Setup**:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

4. **Run Server**:
   ```bash
   php artisan serve
   ```

---

## 📬 Author
**Akbar Pangestu**
Fullstack Developer
*"Designing secure and scalable REST APIs with Laravel."*
