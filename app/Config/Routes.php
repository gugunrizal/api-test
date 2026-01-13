<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {

    // Kategori
    $routes->get('kategori', 'KategoriController::index');
    $routes->get('kategori/(:num)', 'KategoriController::show/$1');
    $routes->post('kategori', 'KategoriController::create');
    $routes->put('kategori/(:num)', 'KategoriController::update/$1');
    $routes->delete('kategori/(:num)', 'KategoriController::delete/$1');
    $routes->get('kategori/(:num)/produk', 'KategoriController::produkByKategori/$1');

    // Produk
    $routes->get('produk', 'ProdukController::index');
    $routes->get('produk/(:num)', 'ProdukController::show/$1');
    $routes->post('produk', 'ProdukController::create');
    $routes->put('produk/(:num)', 'ProdukController::update/$1');
    $routes->delete('produk/(:num)', 'ProdukController::delete/$1');
    $routes->get('produk/search/(:any)', 'ProdukController::search/$1');
    $routes->get('produk/search', 'ProdukController::search');

    // Filter produk
    $routes->get('produk/filter', 'ProdukController::index');
});

$routes->get('/', 'Home::index');
