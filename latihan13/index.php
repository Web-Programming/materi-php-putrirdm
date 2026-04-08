<?php
// Memuat file class
require_once 'produk.php';
require_once 'service.php';

// Menggunakan alias (use) karena nama class-nya sama
use App\Produk\Item as ProdukItem;
use App\Service\Item as ServiceItem;

// Membuat instance dari class Item milik App\Produk
$itemProduk = new ProdukItem("Laptop Asus");
echo $itemProduk->info();

// Membuat instance dari class Item milik App\Service
$itemService = new ServiceItem("Instalasi Windows");
echo $itemService->info();