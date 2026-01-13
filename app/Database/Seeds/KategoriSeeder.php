<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_kategori' => 'Elektronik',
                'deskripsi' => 'Produk Elektronik dan Gadget',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Pakaian',
                'deskripsi' => 'Fashion dan Style',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Makanan',
                'deskripsi' => 'Produk Makanan dan Minuman',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Olahraga',
                'deskripsi' => 'Alat Olahraga dan Kebugaran',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Buku',
                'deskripsi' => 'Produk Buku dan Literatur',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('kategori')->insertBatch($data);
    }
}
