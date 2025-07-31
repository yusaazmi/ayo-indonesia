# Panduan Penggunaan

Berikut adalah sistem backend REST API untuk test di AYO INDONESIA
Requirement untuk menjalankan aplikasi adalah :

- [PHP](https://www.php.net/) (versi 8.3.15 atau lebih tinggi)
- [Composer](https://getcomposer.org/) (versi 2.7 atau lebih tinggi) 
- [Laravel](https://laravel.com/) (versi 12)
- [Database](https://www.mysql.com/) (MySQL, versi 8.1)

## Instalasi

1. Pastikan Anda memiliki PHP, Composer, dan MySQL terpasang di komputer Anda.
2. Clone repositori ini ke komputer Anda.
3. Buka terminal dan navigasikan ke direktori proyek.
4. Jalankan perintah berikut untuk menginstal semua dependensi:

    ```bash
    composer install
    ```

5. Salin file `.env.example` menjadi `.env`:

    ```bash
    cp .env.example .env
    ```

6. Generate kunci aplikasi:

    ```bash
    php artisan key:generate
    ```

7. Jalankan storage link dikarenkan pada project ini penyimpanan tidak menggunakan S3:

    ```bash
    php artisan storage:link
    ```

8. Atur koneksi basis data Anda di dalam file `.env`.
9. Jalankan migrasi untuk membuat tabel basis data dan seeders data:

    ```bash
    php artisan migrate:fresh --seed
    ```

10. Untuk menjalankan aplikasi anda dapat menggunakan perintah berikut :
    ```bash
    php artisan serve
    ```
    Kemudian masuk ke
    ```bash
    http://127.0.0.1:8000/api/login
    ```
11. Anda bisa login dengan memasukkan email default admin
    ```bash
    username : admin@gmail.com
    password : secret
    ```