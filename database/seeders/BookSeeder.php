<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'judul'        => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'penulis'      => 'Robert C. Martin',
                'kategori'     => 'teknologi',
                'tahun_terbit' => 2008,
                'stok'         => 12,
                'deskripsi'    => 'Panduan praktis menulis kode program yang bersih, mudah dibaca, dan mudah dipelihara oleh pengembang perangkat lunak.',
                'cover_url'    => 'https://images-na.ssl-images-amazon.com/images/I/41xShlnTZTL._SX376_BO1,204,203,200_.jpg',
            ],
            [
                'judul'        => 'Laskar Pelangi',
                'penulis'      => 'Andrea Hirata',
                'kategori'     => 'fiksi',
                'tahun_terbit' => 2005,
                'stok'         => 8,
                'deskripsi'    => 'Kisah perjuangan sepuluh anak dari keluarga miskin di Pulau Belitung dalam menempuh pendidikan di sekolah Muhammadiyah yang hampir roboh.',
                'cover_url'    => 'https://upload.wikimedia.org/wikipedia/id/8/8e/Laskar_pelangi_sampul.jpg',
            ],
            [
                'judul'        => 'Sapiens: Riwayat Singkat Umat Manusia',
                'penulis'      => 'Yuval Noah Harari',
                'kategori'     => 'sejarah',
                'tahun_terbit' => 2011,
                'stok'         => 5,
                'deskripsi'    => 'Menelusuri perjalanan evolusi manusia purba hingga membentuk peradaban modern, ekonomi, dan teknologi saat ini.',
                'cover_url'    => 'https://images-na.ssl-images-amazon.com/images/I/713jIoMO3UL.jpg',
            ],
            [
                'judul'        => 'A Brief History of Time',
                'penulis'      => 'Stephen Hawking',
                'kategori'     => 'sains',
                'tahun_terbit' => 1988,
                'stok'         => 4,
                'deskripsi'    => 'Penjelasan populer mengenai kosmologi, lubang hitam, dentuman besar (Big Bang), dan hakikat ruang dan waktu.',
                'cover_url'    => 'https://images-na.ssl-images-amazon.com/images/I/A1xkFZX5k-L.jpg',
            ],
            [
                'judul'        => 'Atomic Habits',
                'penulis'      => 'James Clear',
                'kategori'     => 'non-fiksi',
                'tahun_terbit' => 2018,
                'stok'         => 15,
                'deskripsi'    => 'Cara membangun kebiasaan baik dan menghilangkan kebiasaan buruk melalui perubahan-perubahan kecil yang konsisten.',
                'cover_url'    => 'https://images-na.ssl-images-amazon.com/images/I/91bYsX41DVL.jpg',
            ],
            [
                'judul'        => 'The Pragmatic Programmer',
                'penulis'      => 'Andrew Hunt & David Thomas',
                'kategori'     => 'teknologi',
                'tahun_terbit' => 2019,
                'stok'         => 7,
                'deskripsi'    => 'Kumpulan prinsip dan praktik terbaik bagi programmer modern dalam merancang arsitektur perangkat lunak.',
                'cover_url'    => 'https://images-na.ssl-images-amazon.com/images/I/51W1sBPO7tL._SX380_BO1,204,203,200_.jpg',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
