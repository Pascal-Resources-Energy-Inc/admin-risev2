<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VoucherHistoryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Voucher Code',
            'Distributor',
            'History Type',
            'Event',
            'Details',
            'Actor',
            'Status',
            'Period Voucher Rebate',
        ];
    }

    public function map($row): array
    {
        return [
            $row->date,
            $row->voucher_code,
            $row->distributor,
            $row->event_type,
            $row->event,
            $row->details,
            $row->actor,
            $row->status,
            (float) $row->rebate_total,
        ];
    }
}
