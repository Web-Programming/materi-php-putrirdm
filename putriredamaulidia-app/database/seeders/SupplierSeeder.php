<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT. Sumber Makmur',
                'phone' => '021-12345678',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat'
            ],
            [
                'name' => 'CV. Jaya Abadi',
                'phone' => '022-87654321',
                'address' => 'Jl. Asia Afrika No. 45, Bandung'
            ],
            [
                'name' => 'UD. Berkah Jaya',
                'phone' => '031-55667788',
                'address' => 'Jl. Raya Darmo No. 89, Surabaya'
            ],
            [
                'name' => 'PT. Global Mandiri',
                'phone' => '024-99887766',
                'address' => 'Jl. Pemuda No. 67, Semarang'
            ],
            [
                'name' => 'CV. Mitra Sejahtera',
                'phone' => '0274-5544332',
                'address' => 'Jl. Malioboro No. 12, Yogyakarta'
            ],
            [
                'name' => 'Toko Sentosa',
                'phone' => '0361-223344',
                'address' => 'Jl. Sunset Road No. 56, Denpasar'
            ],
            [
                'name' => 'PT. Indo Sukses',
                'phone' => '021-77889900',
                'address' => 'Jl. Gatot Subroto No. 234, Jakarta Selatan'
            ],
            [
                'name' => 'CV. Sinar Terang',
                'phone' => '025-66778899',
                'address' => null
            ],
            [
                'name' => 'UD. Harapan Baru',
                'phone' => '0411-334455',
                'address' => 'Jl. Pettarani No. 78, Makassar'
            ],
            [
                'name' => 'PT. Cahaya Utama',
                'phone' => '061-44556677',
                'address' => 'Jl. Gatot Subroto No. 90, Medan'
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}