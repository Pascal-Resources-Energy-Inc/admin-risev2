<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class MonthlySalesExport implements FromArray, WithEvents
{
    protected $rows;
    protected $products;
    protected $paymentColumns;
    protected $period;

    public function __construct(Collection $rows, Collection $products, Collection $paymentColumns, Carbon $period)
    {
        $this->rows = $rows;
        $this->products = $products;
        $this->paymentColumns = $paymentColumns;
        $this->period = $period;
    }

    public function array(): array
    {
        $firstHeader = ['Region', 'Authorized Distributor ID', 'Business Name', 'Customer Type', 'Project (if any)'];
        $secondHeader = ['', '', '', '', ''];

        foreach ($this->products as $product) {
            $firstHeader[] = trim(($product->sku ? $product->sku . ' ' : '') . $product->product_name);
            $firstHeader[] = '';
            $secondHeader[] = 'Qty';
            $secondHeader[] = 'Amount';
        }

        $firstHeader[] = 'Total Amount';
        $secondHeader[] = '';
        $firstHeader[] = 'Payment';
        $secondHeader[] = $this->paymentColumns->first();

        foreach ($this->paymentColumns->slice(1) as $label) {
            $firstHeader[] = '';
            $secondHeader[] = $label;
        }

        $data = [$firstHeader, $secondHeader];

        foreach ($this->rows as $row) {
            $line = [
                $row->region,
                $row->distributor_id,
                $row->business_name,
                $row->customer_type,
                $row->projects->implode(', '),
            ];

            foreach ($this->products as $product) {
                $total = $row->product_totals->get($product->key);
                $line[] = (float) $total->qty;
                $line[] = (float) $total->amount;
            }

            $line[] = (float) $row->total_amount;

            foreach ($this->paymentColumns as $key => $label) {
                $line[] = (float) $row->payment_totals->get($key, 0);
            }

            $data[] = $line;
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $productStart = 6;
                $totalColumn = $productStart + ($this->products->count() * 2);
                $paymentStart = $totalColumn + 1;
                $lastColumn = $paymentStart + $this->paymentColumns->count() - 1;

                for ($column = 1; $column <= 5; $column++) {
                    $letter = Coordinate::stringFromColumnIndex($column);
                    $sheet->mergeCells($letter . '1:' . $letter . '2');
                }

                foreach ($this->products as $index => $product) {
                    $start = $productStart + ($index * 2);
                    $sheet->mergeCells(
                        Coordinate::stringFromColumnIndex($start) . '1:' .
                        Coordinate::stringFromColumnIndex($start + 1) . '1'
                    );
                }

                $totalLetter = Coordinate::stringFromColumnIndex($totalColumn);
                $sheet->mergeCells($totalLetter . '1:' . $totalLetter . '2');
                $sheet->mergeCells(
                    Coordinate::stringFromColumnIndex($paymentStart) . '1:' .
                    Coordinate::stringFromColumnIndex($lastColumn) . '1'
                );

                $lastLetter = Coordinate::stringFromColumnIndex($lastColumn);
                $sheet->freezePane('F3');
                $sheet->setAutoFilter('A2:' . $lastLetter . '2');
                $sheet->getStyle('A1:' . $lastLetter . '2')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF536A7F']],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                        'wrapText' => true,
                    ],
                ]);
                $sheet->getStyle('A1:' . $lastLetter . $sheet->getHighestRow())
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle('thin')
                    ->getColor()->setARGB('FFD7DEE7');
                $sheet->getRowDimension(1)->setRowHeight(42);
                $sheet->getRowDimension(2)->setRowHeight(24);
                $sheet->getPageSetup()->setOrientation('landscape');
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->setTitle(substr($this->period->format('M Y'), 0, 31));
            },
        ];
    }
}
