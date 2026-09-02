# Cara install
1. Cloning
   ```
   git clone https://github.com/AzizHusain23/CRM-Disty-Teknologi.git
   ```
2. Masuk ke folder project
   ```
   cd CRM-Disty-Teknologi
   ```
3. Install Vendor
   ```
   composer install
   ```
4. Copy file .env
   ```
   cp .env.example .env
   ```
5. Generate Key untuk masing-masing komputer/laptop
   ```
   php artisan key:generate
   ```
6. Migrasi database (jangan lupa XAMPP nya di nyalain dulu)
   ```
   php artisan migrate
   ```
7. masuk ke codingan nya
   ```
   code .
   ```
8. jalankan server-nya
   ```
   php artisan serve
   ```
---
## Buka terminal baru
1. Install Node.JS
   ```
   npm install
   ```
2. building untuk development
   ```
   npm run build
   ```
2. Jalankan Node.js
   ```
   npm run dev
   ```
