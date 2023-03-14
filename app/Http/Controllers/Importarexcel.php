<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Importarexcel extends Controller
{
    //
    public function impportar()
    {
        return view('excel/importar');
    }
    public function importarexcel(Request $request)
    {
        // recibir el excel y guardarlo
        $file = $request->file('file');
        $nombre =$file->getClientOriginalName();
        $destinationPath = 'uploads';
        $file->move($destinationPath,$file->getClientOriginalName());
        //leer le excel
        $inputFileName = './uploads/'.$nombre;
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        $totalDeHojas = $spreadsheet->getSheetCount();
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        for ($row = 2; $row <= $highestRow; ++$row)
        {
            for ($col = 1; $col <= $highestColumnIndex; ++$col)
            {
                $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
    }
}