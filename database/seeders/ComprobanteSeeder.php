<?php

namespace Database\Seeders;

use App\Models\Comprobante;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComprobanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Comprobante::insert([
            [
                'tipo_comprobante'=> 'Boleta'
            ],
            [
                'tipo_comprobante'=> 'Factura'
            ],
        ]);
    }
}
