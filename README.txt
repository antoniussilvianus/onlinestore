Rebelstuff Store - PHP Native + MySQL + Bootstrap CDN

Fitur:
- Landing: Hero, Accordion (Filosofi/Kualitas), Tentang Kami (split + timeline), Info Toko + Map
- Katalog: Carousel model baju + caption & tombol add-to-cart, Grid "Pilih Model & Ukuran"
- Keranjang + Checkout (subtotal), Pengiriman (kurir, layanan, ongkir sederhana), Konfirmasi pesanan
- Admin CRUD Produk + Upload gambar
- **Login Admin sederhana** (username/password di config.php)
- Tema gelap "streetwear" dengan aksen neon

Setup (XAMPP):
1) Copy folder 'online-store-php-adminlogin' ke: C:\xampp\htdocs\online-store-php-adminlogin
2) Start Apache & MySQL.
3) phpMyAdmin: import file db.sql (DB 'simple_store').
4) Edit kredensial DB di config.php jika perlu.
5) Buka: http://localhost/online-store-php-adminlogin/index.php
6) Admin: http://localhost/online-store-php-adminlogin/admin/login.php (default admin/admin123).

Catatan: Login admin sederhana (session). Untuk produksi, tambahkan hashing password & CSRF.
