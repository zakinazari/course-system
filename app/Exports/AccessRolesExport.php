<?php

namespace App\Exports;

use App\Models\Settings\AccessRole;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class AccessRolesExport implements FromQuery, WithHeadings
{
    
    public $appliedSearch = [];

    public function __construct($appliedSearch = [])
    {
        $this->appliedSearch = $appliedSearch;
    }


    public function query()
    {
        return AccessRole::query()->select('id', 'role_name')
            ->when(
                !empty($this->appliedSearch['role_name']), fn($q) => $q->where('role_name', 'like', "%{$this->appliedSearch['role_name']}%")
            );
    }

    public function headings(): array
    {
        return ['ID', 'Role Name'];
    }

}
