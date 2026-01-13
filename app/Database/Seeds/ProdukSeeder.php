<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kategori_id' => 1,
                'kode_produk' => 'ELEC-001',
                'nama_produk' => 'Samsung S24 Ultra',
                'deskripsi' => 'Smartphone Terbaik milik Samsung',
                'harga' => 15000000,
                'stok' => 40,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_id' => 1,
                'kode_produk' => 'ELEC-002',
                'nama_produk' => 'Iphone 17 Pro Max',
                'deskripsi' => 'Smartphone Terbaik milik Apple',
                'harga' => 25000000,
                'stok' => 40,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_id' => 2,
                'kode_produk' => 'PAK-001',
                'nama_produk' => 'Kemeja Eiger Tac',
                'deskripsi' => 'Kemeja Terbaik milik Eiger',
                'harga' => 450000,
                'stok' => 40,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'kategori_id' => 3,
                'kode_produk' => 'FOOD-001',
                'nama_produk' => 'Golda Kopi',
                'deskripsi' => 'Minuman Terbaik milik Golda',
                'harga' => 1500,
                'stok' => 40,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        $this->db->table('produk')->insertBatch($data);
    }
}
