<?php

namespace App\Exports;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembayaranExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $request;
    protected int $no = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return Pembayaran::with(['pinjaman.nasabah', 'karyawan'])
            ->when($this->request->bulan, fn($q) => $q->whereMonth('tanggal_bayar', $this->request->bulan))
            ->when($this->request->tahun, fn($q) => $q->whereYear('tanggal_bayar', $this->request->tahun))
            ->get();
    }

    public function headings(): array
    {
        return ['#', 'Nasabah', 'No. KTP', 'No. Pinjaman', 'Karyawan',
                'Tgl Bayar', 'Dibayar (Rp)', 'Denda (Rp)', 'Total (Rp)'];
    }

    public function map($row): array
    {
        $this->no++;
        return [
            $this->no,
            $row->pinjaman->nasabah->nama_lengkap ?? '-',
            $row->pinjaman->nasabah->no_ktp       ?? '-',
            $row->pinjaman->no_pinjaman           ?? '-',
            $row->karyawan->name                  ?? '-',
            \Carbon\Carbon::parse($row->tanggal_bayar)->format('d/m/Y'),
            $row->jumlah_dibayar,
            $row->denda,
            $row->jumlah_dibayar + $row->denda,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Pembayaran';
    }
}