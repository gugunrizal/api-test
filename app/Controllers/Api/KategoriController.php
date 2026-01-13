<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class KategoriController extends BaseController
{
    use ResponseTrait;

    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        helper('form');
    }

    // Menampilkan Seluruh Kategori
    public function index()
    {
        $semua_kategori = $this->kategoriModel->findAll();

        return $this->respond([
            'status' => 'success',
            'data' => '$semua_kategori',
            'total' => count($semua_kategori)
        ], 200);
    }

    // Menampilkan Satu Kategori
    public function show($id = null)
    {
        $kategori = $this->kategoriModel->find($id);

        if (!$kategori) {
            return $this->failNotFound('Kategori Tidak ditmukan');
        }

        return $this->respond([
            'status' => 'success',
            'data' => $kategori
        ], 200);
    }

    // Menambahkan Kategori
    public function create()
    {
        $rules = [
            'nama_kategori' => 'required|min_length[3]|max_length[100]|is_unique[kategori.nama_kategori]',
            'deskripsi' => 'max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'nama_kategori' => $this->request->getVar('nama_kategori'),
            'deskripsi' => $this->request->getVar('deskripsi')
        ];

        if ($this->kategoriModel->save($data)) {
            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $data
            ]);
        } else {
            return $this->failServerError('Gagal menambahkan Kategori');
        }
    }

    // Update Kategori
    public function update($id = null)
    {
        $kategori = $this->kategoriModel->find($id);

        if (!$kategori) {
            return $this->failNotFound('Kategori tidak ditemukan');
        }

        $rules = [
            'nama_kategori' => "required|min_length[3]|max_length[100]|is_unique[kategori.nama_kategori,id,$id]",
            'deskripsi'     => 'max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'nama_kategori' => $this->request->getVar('nama_kategori'),
            'deskripsi'     => $this->request->getVar('deskripsi')
        ];

        if ($this->kategoriModel->update($id, $data)) {
            return $this->respond([
                'status'  => 'success',
                'message' => 'Kategori berhasil diperbarui',
                'data'    => $data
            ], 200);
        } else {
            return $this->failServerError('Gagal memperbarui kategori');
        }
    }

    // Menghapus Kategori
    public function delete($id = null)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return $this->failNotFound('Kategori tidak ditemukan');
        }

        $produkModel = new \App\Models\ProdukModel();
        $hasProducts = $produkModel->where('kategori_id', $id)->countAllResults();

        if ($hasProducts > 0) {
            return $this->fail('Tidak dapat menghapus kategori yang memiliki produk', 400);
        }

        if ($this->kategoriModel->delete($id)) {
            return $this->respondDeleted([
                'status'  => 'success',
                'message' => 'Kategori berhasil dihapus'
            ]);
        } else {
            return $this->failServerError('Gagal menghapus kategori');
        }
    }

    // Menampilkan Kategori Berdasarkan Id
    public function produkByKategori($id = null)
    {
        $produkModel = new \App\Models\ProdukModel();
        $produk = $produkModel->where('kategori_id', $id)->findAll();

        return $this->respond([
            'status' => 'success',
            'data'   => $produk,
            'total'  => count($produk)
        ], 200);
    }
}
