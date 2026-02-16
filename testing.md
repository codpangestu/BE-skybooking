# API Testing Guide (Unified)

Panduan ini membantu kamu melakukan pengujian API menggunakan Postman dengan alur yang sudah disingkat dan otomatis.

## 1. Persiapan Awal
1.  **Import file**: Import `postman_collection.json` dan `postman_environment.json` ke Postman kamu.
2.  **Pilih Environment**: Pastikan kamu memilih environment **"Final Project Environment"** di pojok kanan atas.
3.  **Database Seeding**: Jalankan `php artisan db:seed --class=AdminUserSeeder` untuk memastikan akun admin ready (`admin@gmail.com` / `password`).

---

## 2. Alur Testing (Otomatis)

### 👮 Side Admin
- **Login**: Jalankan request **Admin Side > Auth > Login Admin**.
- **Otomatis**: Token akan tersimpan di variabel `api_token`.
- **Master Data**: Sekarang kamu bisa menjalankan semua request di folder **Master Data**, **Inventory**, dan **User Management**.

### 👤 Side User (Passenger)
- **Register**: Jalankan request **User Side > Auth > Register** untuk membuat akun baru.
- **Login**: Jalankan request **User Side > Auth > Login**.
- **Otomatis**: Token baru akan menggantikan token sebelumnya di variabel `api_token`.
- **Fitur User**: Sekarang kamu bisa mencoba mencari pesawat, validasi promo, dan melakukan booking di folder **Booking & Transactions**.

---

## 3. Catatan Penting
- **Unified Token**: Semua request yang membutuhkan login kini menggunakan variabel `{{api_token}}`. Kamu hanya perlu login sekali (sebagai admin atau user) untuk mengaktifkan folder tersebut.
- **Response Format**: Semua API akan mengembalikan format:
  ```json
  {
      "success": true,
      "message": "...",
      "data": { ... }
  }
  ```
- **Error Handling**: Jika ada validasi yang gagal, API akan memberikan status `422` dengan detail error di field `errors`.

Selamat mencoba! 🚀
