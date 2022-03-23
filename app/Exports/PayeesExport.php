<?php

namespace App\Exports;

use App\Models\Payee;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;

class PayeesExport implements FromArray
{
    use Exportable;
/*    public function headings(): array
    {
        return [
            'Payee ID',
            'Payee',
            'User',
        ];
    }
*/
    public function array(): array
    {
        return Payee::leftJoin('users', 'payees.user_id', '=', 'users.id')->get()->toArray();
    }
}
