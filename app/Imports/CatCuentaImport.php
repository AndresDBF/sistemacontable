<?php

namespace App\Imports;

use App\Models\CatCuenta;
use Maatwebsite\Excel\Concerns\ToModel;

class CatCuentaImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
            return new CatCuenta([
                'idcta'   => $row['0'],
                'stscta'  => $row['1'],
                'cta1'    => $row['2'],
                'cta2'    => $row['3'],
                'cta3'    => $row['4'],
                'cta4'    => $row['5'],
<<<<<<< HEAD
/*                 'cta5'    => $row['6'], */
=======
                /* 'cta5'    => $row['6'], */
>>>>>>> 4a51ac9c67a34874a99c2b62bd7b65a48004bf06
            ]);
    }
}
