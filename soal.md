# Take Home Quiz: Sistem Manajemen Gudang Sederhana

## Deskripsi Umum
Buatlah aplikasi Sistem Manajemen Gudang Sederhana menggunakan framework CodeIgniter versi 3 atau 4. Aplikasi ini bertujuan untuk mencatat keluar masuk barang dan memantau stok barang yang tersedia di gudang.


## Functional Requirements (Fungsional)
1. Manajemen Data Barang
    - Admin dapat menambahkan, mengedit, dan menghapus data barang.
    - Stok barang tidak boleh minus
    - Data barang mencakup: nama barang, kode barang, kategori, satuan, jumlah stok.
2. Manajemen Kategori Barang
    - Admin dapat mengelola (CRUD) kategori barang.
    - Contoh kategori barang: IT EQUIPMENTS, OFFICE EQUIPMENTS, SPARE PART, KENDARAAN DAN ALAT BERAT
3. Manajemen Pembelian/Purchase
    - Admin dapat mengelola(CRUD) pembelian barang
    - Contoh data pemebelian: Nama vendor, alamat vendor, tanggal pembelian, nama pembeli, detail barang yang di beli.
4. Transaksi Barang Masuk
    - Admin dapat mencatat transaksi barang masuk.
    - Transaksi barang masuk harus berasal dari pembelian, jumlah barang masuk dengan yang dibeli harus sama.
    - Setelah transaksi berhasil, jumlah stok barang akan bertambah sesuai jumlah barang masuk.
5. Transaksi Barang Keluar
    - Admin dapat mencatat transaksi barang keluar.
    - Setelah transaksi berhasil, jumlah stok barang akan berkurang sesuai jumlah barang keluar.
6. Laporan
    - Admin dapat melihat laporan:
      - Barang masuk berdasarkan rentang tanggal.
      - Barang keluar berdasarkan rentang tanggal.
      - Stok barang terkini.
6. Dashboard (Opsional / Bonus)
    - Menampilkan ringkasan stok barang, jumlah transaksi hari ini, dll.


## Teknologi
    Framework: CodeIgniter 3 atau CodeIgniter 4
    Database: MySQL
    Frontend: Bebas (boleh pakai Bootstrap, Tailwind, atau HTML+CSS)


## Pengumpulan
Silakan buat repositori GitHub (public) dan kirimkan link repositorinya. Pastikan sudah menyertakan:
- Petunjuk instalasi & setup project (README.md)
- Database dump (.sql)
- Screenshots UI (opsional tapi disarankan)

## Estimasi Waktu Pengerjaan
3 hari sejak test diberikan.


## Desain ERD (Entity Relationship Diagram)
Berikut desain ERD sederhana yang bisa digunakan, dapat diubah sesuai kebutuhan:
Tables:

### Categories Table
| Column Name | Type   | Key | Constraint |
| ----------- | ------ | --- | ---------- |
| `id`        | serial | PK  |            |
| `name`      |        |     |            |

### Products Table
| Column Name   | Type    | Key | Constraint |
| ------------- | ------- | --- | ---------- |
| `id`          | serial  | PK  |            |
| `category_id` | int     | FK  |            |
| `name`        |         |     |            |
| `code`        | varchar |     | UNIQUE     |
| `unit`        |         |     |            |
| `stock`       | float4  |     |            |

### Incoming Items Table
| Column Name  | Type      | Key | Constraint |
| ------------ | --------- | --- | ---------- |
| `id`         | serial    | PK  |            |
| `product_id` | int       | FK  |            |
| `date`       | timestamp |     |            |
| `quantity`   | float4    |     |            |

### Outgoing Items Table
| Column Name  | Type      | Key | Constraint |
| ------------ | --------- | --- | ---------- |
| `id`         | serial    | PK  |            |
| `product_id` | int       | FK  |            |
| `date`       | timestamp |     |            |
| `quantity`   | float4    |     |            |


## Syarat & Ketentuan
- Anda wajib memahami seluruh kode yang Anda buat. Akan ada sesi live review/interview singkat untuk menjelaskan kode Anda
- Sertakan file README.md yang menjelaskan:
   Struktur proyek
   Penjelasan fitur
   Petunjuk instalasi & setup project
   Tantangan selama pengerjaan dan cara menyelesaikannya


## Kriteria penilaian
(40%) Implementasi Fitur, fitur bekerja dengan baik
    
    - CRUD Barang
    - CRUD Kategori
    - Pembelian
    - Barang Masuk (update stok)
    - Barang Keluar (update stok)
    - Laporan
  
(25%) Kualitas Kode 

    - Validasi input & error handling
    - Kualitas kode & konsistensi
    - Struktur & Arsitektur Project, Kode harus bersih dan mudah diikuti

(15%) Desain Database & ERD sesuai dan jelas 

(10%) UI fungsional & mudah digunakan 

(5%) Dokumentasi	

    - README dengan petunjuk
    - Database dump tersedia

(5%) Bonus (Opsional) 
    
    Fitur tambahan (auth, filter, dll.)