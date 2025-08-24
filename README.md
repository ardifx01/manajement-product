# Cara Penggunaan

## Instalasi atau Kloning Proyek Ini

1. Kunjungi tautan berikut: https://github.com/jakfarshodiq230/manajement-product.git
2. Buka repository GitHub yang ingin Anda klon.
3. Klik tombol hijau "Code".
4. Pastikan "HTTPS" terpilih dan klik ikon salin untuk menyalin URL: `https://github.com/jakfarshodiq230/manajement-product.git`
5. Buka terminal Anda (atau Git Bash di Windows, Command Prompt/PowerShell).
6. Arahkan terminal ke direktori tempat Anda ingin menyimpan proyek menggunakan perintah `cd`.

## Screenshot Aplikasi

Berikut adalah beberapa screenshot dari aplikasi yang sedang berjalan:

![login](https://github.com/jakfarshodiq230/manajement-product/blob/main/public/screenshots/login.png)
Tampilan halaman login aplikasi

![dashboard](https://github.com/jakfarshodiq230/manajement-product/blob/main/public/screenshots/dashboard.png)
Dashboard admin setelah login berhasil

![manajement](https://github.com/jakfarshodiq230/manajement-product/blob/main/public/screenshots/1.png)

![manajement2](https://github.com/jakfarshodiq230/manajement-product/blob/main/public/screenshots/2.png)

![manajement3](https://github.com/jakfarshodiq230/manajement-product/blob/main/public/screenshots/3.png)

![manajement4](https://github.com/jakfarshodiq230/manajement-product/blob/main/public/screenshots/4.png)
Manajement Products


## Setup

Salin file `env` menjadi `.env` dan sesuaikan untuk aplikasi Anda, khususnya `baseURL`
dan pengaturan database apa pun.

## Perubahan Penting dengan index.php

`index.php` tidak lagi berada di root proyek! File tersebut telah dipindahkan ke dalam folder *public*,
untuk keamanan dan pemisahan komponen yang lebih baik.

Ini berarti Anda harus mengonfigurasi server web Anda untuk "menunjuk" ke folder *public* proyek Anda,
dan bukan ke root proyek. Praktik terbaik adalah mengonfigurasi virtual host untuk menunjuk ke sana. Praktik yang buruk adalah menunjuk server web Anda ke root proyek dan mengharapkan untuk memasukkan *public/...*, karena logika lainnya dan framework menjadi terbuka.

**Silakan** baca panduan pengguna untuk penjelasan yang lebih baik tentang cara kerja CI4!

## Persyaratan Server

Diperlukan PHP versi 8.1 atau lebih tinggi, dengan ekstensi berikut yang diinstal:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!PERINGATAN]
> - PHP 8.1 

Selain itu, pastikan ekstensi berikut diaktifkan di PHP Anda:

- json (diaktifkan secara default - jangan nonaktifkan)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) jika Anda berencana menggunakan MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) jika Anda berencana menggunakan library HTTP\CURLRequest