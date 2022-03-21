<?php

namespace App\Exports;

use App\Models\Payee;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvoicesExport implements FromCollection
{
    public function collection()
    {
        return Payee::all();
    }
}
