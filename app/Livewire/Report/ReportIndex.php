<?php

namespace App\Livewire\Report;

use App\Models\Expense;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportIndex extends Component
{
    public $activeTab = 'sales';
    public $dateFrom = '';
    public $dateTo = '';
    public $category = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function exportPdf($type)
    {
        $reportService = app(ReportService::class);
        $outletId = auth()->user()->outlet_id;
        
        if ($type === 'sales') {
            $data = $reportService->getSalesReport($this->dateFrom, $this->dateTo, $outletId);
            $pdf = Pdf::loadView('exports.sales-pdf', compact('data'));
            return response()->streamDownload(fn() => print($pdf->output()), 'laporan-penjualan.pdf');
        } elseif ($type === 'profit') {
            $data = $reportService->getProfitLoss($this->dateFrom, $this->dateTo, $outletId);
            $pdf = Pdf::loadView('exports.profit-pdf', compact('data'));
            return response()->streamDownload(fn() => print($pdf->output()), 'laporan-lab-rugi.pdf');
        }
    }

    public function exportExcel($type)
    {
        $reportService = app(ReportService::class);
        $outletId = auth()->user()->outlet_id;

        if ($type === 'sales') {
            $data = $reportService->getSalesReport($this->dateFrom, $this->dateTo, $outletId);
            $exportData = $data->map(function ($s) {
                return [
                    'Invoice' => $s->invoice_number,
                    'Tanggal' => $s->transaction_date->format('d/m/Y'),
                    'Pelanggan' => $s->customer?->name ?? 'Umum',
                    'Kasir' => $s->user?->name,
                    'Total' => $s->total_amount,
                    'Diskon' => $s->discount,
                    'Grand Total' => $s->grand_total,
                ];
            });
            $filename = 'laporan-penjualan-' . now()->format('Ymd-His') . '.xlsx';

        } elseif ($type === 'profit') {
            $data = $reportService->getProfitLoss($this->dateFrom, $this->dateTo, $outletId);
            $exportData = collect([
                ['Keterangan' => 'Total Penjualan', 'Jumlah' => $data['total_sales']],
                ['Keterangan' => 'HPP (Harga Pokok Penjualan)', 'Jumlah' => $data['hpp']],
                ['Keterangan' => 'Laba Kotor', 'Jumlah' => $data['gross_profit']],
                ['Keterangan' => 'Total Pengeluaran', 'Jumlah' => $data['total_expenses']],
                ['Keterangan' => 'Laba Bersih', 'Jumlah' => $data['net_profit']],
            ]);
            $filename = 'laporan-lab-rugi-' . now()->format('Ymd-His') . '.xlsx';

        } elseif ($type === 'stock') {
            $data = $reportService->getStockReport($outletId);
            $exportData = $data->map(function ($p) {
                return [
                    'Nama Produk' => $p->name,
                    'SKU' => $p->sku,
                    'Kategori' => $p->category?->name,
                    'Satuan' => $p->unit?->name,
                    'Stok' => $p->stock,
                    'Min Stok' => $p->min_stock_alert,
                    'Status' => $p->isStockLow() ? 'Menipis' : 'Aman',
                ];
            });
            $filename = 'laporan-stok-' . now()->format('Ymd-His') . '.xlsx';

        } elseif ($type === 'cashflow') {
            $data = $reportService->getCashFlow($this->dateFrom, $this->dateTo, $outletId);
            $exportData = $data->map(function ($cf) {
                return [
                    'Tanggal' => $cf->created_at->format('d/m/Y H:i'),
                    'Deskripsi' => $cf->description,
                    'Tipe' => $cf->transaction_type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    'Jumlah' => $cf->amount,
                ];
            });
            $filename = 'laporan-arus-kas-' . now()->format('Ymd-His') . '.xlsx';

        } elseif ($type === 'expenses') {
            $data = Expense::whereBetween('expense_date', [$this->dateFrom, $this->dateTo])
                ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
                ->when($this->category, fn($q) => $q->where('category', $this->category))
                ->orderBy('expense_date', 'desc')
                ->get();
            $exportData = $data->map(function ($e) {
                return [
                    'Tanggal' => $e->expense_date->format('d/m/Y'),
                    'Deskripsi' => $e->description,
                    'Kategori' => $e->category ?? '-',
                    'Jumlah' => $e->amount,
                ];
            });
            $filename = 'laporan-pengeluaran-' . now()->format('Ymd-His') . '.xlsx';

        } else {
            return;
        }

        return response()->streamDownload(function () use ($exportData) {
            echo (new FastExcel($exportData))->export('php://output');
        }, $filename);
    }

    public function render()
    {
        $reportService = app(ReportService::class);
        $outletId = auth()->user()->outlet_id;

        $salesReport = [];
        $profitLoss = [];
        $stockReport = [];
        $cashFlow = [];
        $expenses = [];

        if ($this->activeTab === 'sales') {
            $salesReport = $reportService->getSalesReport($this->dateFrom, $this->dateTo, $outletId);
        } elseif ($this->activeTab === 'profit') {
            $profitLoss = $reportService->getProfitLoss($this->dateFrom, $this->dateTo, $outletId);
        } elseif ($this->activeTab === 'stock') {
            $stockReport = $reportService->getStockReport($outletId);
        } elseif ($this->activeTab === 'cashflow') {
            $cashFlow = $reportService->getCashFlow($this->dateFrom, $this->dateTo);
        } elseif ($this->activeTab === 'expenses') {
            $expenses = Expense::whereBetween('expense_date', [$this->dateFrom, $this->dateTo])
                ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
                ->when($this->category, fn($q) => $q->where('category', $this->category))
                ->orderBy('expense_date', 'desc')
                ->get();
        }

        return view('livewire.report.report-index', compact(
            'salesReport', 'profitLoss', 'stockReport', 'cashFlow', 'expenses'
        ))->layout('layouts.app', ['title' => 'Laporan']);
    }
}
