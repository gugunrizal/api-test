<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\ProdukModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class ProdukController extends BaseController
{
    use ResponseTrait;

    protected $produkModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        helper(['form', 'file']);
    }

    // Menampilkan Semua Produk dengan Filter
    public function index()
    {
        $params = $this->request->getGet();

        if (!empty($params)) {
            $produk = $this->produkModel->filterProduk($params);
        } else {
            $produk = $this->produkModel->getProdukWithKategori();
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $produk,
            'total'  => count($produk)
        ], 200);
    }

    // Menampilkan Satu Produk dengan Kategori
    public function show($id = null)
    {
        $produk = $this->produkModel->getProdukWithKategori($id);

        if (!$produk) {
            return $this->failNotFound('Produk tidak ditemukan');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $produk
        ], 200);
    }

    // Menambahkan Produk Baru
    public function create()
    {
        $rules = [
            'kategori_id' => 'required|numeric|is_not_unique[kategori.id]',
            'kode_produk' => 'required|min_length[3]|max_length[50]|is_unique[produk.kode_produk]',
            'nama_produk' => 'required|min_length[3]|max_length[100]',
            'deskripsi'   => 'max_length[500]',
            'harga'       => 'required|numeric|greater_than[0]',
            'stok'        => 'numeric'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }


        $gambar = $this->uploadGambar();
        $data = [
            'kategori_id' => $this->request->getVar('kategori_id'),
            'kode_produk' => $this->request->getVar('kode_produk'),
            'nama_produk' => $this->request->getVar('nama_produk'),
            'deskripsi'   => $this->request->getVar('deskripsi'),
            'harga'       => $this->request->getVar('harga'),
            'stok'        => $this->request->getVar('stok') ?? 0,
            'gambar'      => $gambar
        ];

        if ($this->produkModel->save($data)) {
            return $this->respondCreated([
                'status'  => 'success',
                'message' => 'Produk berhasil ditambahkan',
                'data'    => $data
            ]);
        } else {
            return $this->failServerError('Gagal menambahkan produk');
        }
    }

    public function update($id = null)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return $this->failNotFound('Produk tidak ditemukan');
        }

        $rules = [
            'kategori_id' => 'numeric|is_not_unique[kategori.id]',
            'kode_produk' => "min_length[3]|max_length[50]|is_unique[produk.kode_produk,id,$id]",
            'nama_produk' => 'min_length[3]|max_length[100]',
            'deskripsi'   => 'max_length[500]',
            'harga'       => 'numeric|greater_than[0]',
            'stok'        => 'numeric'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Jika file ada di database
        $gambar = $this->uploadGambar();
        if ($gambar) {
            if (!empty($produk['gambar'])) {
                $this->deleteGambar($produk['gambar']);
            }
        } else {
            $gambar = $produk['gambar'];
        }

        $data = [
            'kategori_id' => $this->request->getVar('kategori_id') ?? $produk['kategori_id'],
            'kode_produk' => $this->request->getVar('kode_produk') ?? $produk['kode_produk'],
            'nama_produk' => $this->request->getVar('nama_produk') ?? $produk['nama_produk'],
            'deskripsi'   => $this->request->getVar('deskripsi') ?? $produk['deskripsi'],
            'harga'       => $this->request->getVar('harga') ?? $produk['harga'],
            'stok'        => $this->request->getVar('stok') ?? $produk['stok'],
            'gambar'      => $gambar
        ];

        if ($this->produkModel->update($id, $data)) {
            return $this->respond([
                'status'  => 'success',
                'message' => 'Produk berhasil diperbarui',
                'data'    => $data
            ], 200);
        } else {
            return $this->failServerError('Gagal memperbarui produk');
        }
    }

    // Untuk Upload Gambar
    private function uploadGambar()
    {
        $gambar = $this->request->getFile('gambar');

        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $newName = $gambar->getRandomName();
            $gambar->move(WRITEPATH . 'uploads/produk', $newName);
            return $newName;
        }

        return null;
    }

    // Untuk hapus gambar
    private function deleteGambar($filename)
    {
        $path = WRITEPATH . 'uploads/produk' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // Untuk mencari produk
    public function search($keyword = null)
    {
        if (!$keyword) {
            $keyword = $this->request->getGet('q');
        }

        $produk = $this->produkModel->like('nama_produk', $keyword)
            ->orLike('kode_produk', $keyword)
            ->orLike('deskripsi', $keyword)
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'data'   => $produk,
            'total'  => count($produk)
        ], 200);
    }
}
