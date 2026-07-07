<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AdministrationImport implements WithMultipleSheets
{

    public function sheets(): array
    {
        return [
            //0 => new WilayaImport(),
            //1 => new MoughataImport(),
            //2 => new CommuneImport(),
            //3 => new LocaliteImport(),
            'infrastructures' => new InfrastructureImport(),
            //5 => new OperationImport(),
            //6 => new ProjetImport(),
            //7 => new OperateurImport()

        ];
    }
}
