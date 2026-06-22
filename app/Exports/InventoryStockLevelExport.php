<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class InventoryStockLevelExport implements FromArray, ShouldAutoSize, WithEvents, WithHeadings
{
    protected $rows;
    protected $products;
    protected $asOf;

    public function __construct(Collection $rows, Collection $products, Carbon $asOf)
    {
        $this->rows = $rows;
        $this->products = $products;
        $this->asOf = $asOf;
    }

    public function headings(): array
    {
        return array_merge([
            'Region',
            'Authorized Distributor ID',
            'IL Business Name',
            'Customer Type',
            'Authorized Territories',
        ], $this->products->map(function ($product) {
            return trim(($product->sku ? $product->sku . ' - ' : '') . $product->product_name);
        })->all(), [
            'Total Stock',
            'As Of',
        ]);
    }

    public function array(): array
    {
        return $this->rows->map(function ($row) {
            return array_merge([
                $row->region,
                $row->distributor_id,
                $row->business_name,
                $row->customer_type,
                $row->territories->implode(', '),
            ], $this->products->map(function ($product) use ($row) {
                return (float) $row->stock->get($product->key, 0);
            })->all(), [
                (float) $row->total_stock,
                $this->asOf->format('Y-m-d'),
            ]);
        })->all();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $sheet->freezePane('F2');
                $sheet->setAutoFilter('A1:' . $highestColumn . $highestRow);
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['argb' => 'FF0F766E'],
                    ],
                    'alignment' => [
                        'vertical' => 'center',
                        'wrapText' => true,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(34);
            },
        ];
    }

}
