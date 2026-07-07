<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Operation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
class OperationImport implements ToModel,SkipsEmptyRows, WithHeadingRow,SkipsOnFailure
{
    use Importable, SkipsFailures;
    public function model(array $row)
    {
        try {
            //code...

        $created_at = date('Y-m-d');

        return new Operation([
            'intitule' => $row['operation'],
            'intitule_fr' => $row['operation'],
            'code' => $row['code'],
            'created_at' => $created_at,
            'updated_at' => $created_at
            
        ]);
    } catch (\Throwable $th) {
        //throw $th;
            //return redirect()->back()->with(['errors' => $th]);
    }
    }

}
