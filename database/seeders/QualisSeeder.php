<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class QualisSeeder extends Seeder
{
    public function run()
    {
        $filePath = storage_path('app/public/qualis.tsv');

        if (File::exists($filePath)) {
            $file = fopen($filePath, 'r');

            while (($line = fgetcsv($file, 1000, "\t")) !== false) {

                $issn = $line[0];
                $titulo = $line[1];
                $areaAvaliacao = $line[2];
                $estrato = $line[3];

                DB::table('qualis')->insert([
                    'issn' => $issn,
                    'titulo' => $titulo,
                    'area' => $areaAvaliacao,
                    'estrato' => $estrato,
                    'ano' => '2017-2020'
                ]);
            }
            fclose($file);
            $this->command->info('Dados do Qualis inseridos com sucesso!');
        } else {
            $this->command->error('Arquivo TSV não encontrado!');
        }
    }
}