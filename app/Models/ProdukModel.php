<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table            = 'produk';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $protectFields    = true;
    protected $allowedFields    = [
        'kategori_id',
        'kode_produk',
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'gambar'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules    = [
        'kategori_id' => 'required|numeric',
        'kode_produk' => 'required|min_length[3]|max_length[50]|is_unique[produk.kode_produk,id,{id}]',
        'nama_produk' => 'required|min_length[3]|max_length[100]',
        'harga'       => 'required|numeric|greater_than[0]',
        'stok'        => 'numeric',
    ];

    protected $validationMessages = [
        'kode_produk' => [
            'is_unique' => 'Kode produk sudah terdaftar',
        ],
        'harga' => [
            'greater_than' => 'Harga harus lebih dari 0',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Method untuk mendapatkan produk dengan kategori
    public function getProdukWithKategori($id = null)
    {
        $builder = $this->db->table('produk p');
        $builder->select('p.*, k.nama_kategori, k.deskripsi as deskripsi_kategori');
        $builder->join('kategori k', 'k.id = p.kategori_id', 'left');

        if ($id) {
            $builder->where('p.id', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    // Method untuk filter produk
    public function filterProduk($params = [])
    {
        $builder = $this->db->table('produk p');
        $builder->select('p.*, k.nama_kategori');
        $builder->join('kategori k', 'k.id = p.kategori_id', 'left');

        if (isset($params['kategori_id']) && !empty($params['kategori_id'])) {
            $builder->where('p.kategori_id', $params['kategori_id']);
        }

        if (isset($params['min_harga']) && !empty($params['min_harga'])) {
            $builder->where('p.harga >=', $params['min_harga']);
        }

        if (isset($params['max_harga']) && !empty($params['max_harga'])) {
            $builder->where('p.harga <=', $params['max_harga']);
        }

        if (isset($params['search']) && !empty($params['search'])) {
            $builder->groupStart();
            $builder->like('p.nama_produk', $params['search']);
            $builder->orLike('p.kode_produk', $params['search']);
            $builder->orLike('k.nama_kategori', $params['search']);
            $builder->groupEnd();
        }

        // Sorting
        $sortField = $params['sort_by'] ?? 'p.created_at';
        $sortOrder = $params['sort_order'] ?? 'DESC';
        $builder->orderBy($sortField, $sortOrder);

        return $builder->get()->getResultArray();
    }
}
