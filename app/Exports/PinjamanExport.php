<?php

namespace App\Exports;

use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PinjamanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $request;
    protected int $no = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return Pinjaman::with(['nasabah', 'karyawan'])
            ->when($this->request->bulan,  fn($q) => $q->whereMonth('tanggal_pengajuan', $this->request->bulan))
            ->when($this->request->tahun,  fn($q) => $q->whereYear('tanggal_pengajuan', $this->request->tahun))
            ->when($this->request->status, fn($q) => $q->where('status', $this->request->status))
            ->get();
    }

    public function headings(): array
    {
        return ['#', 'No. Pinjaman', 'Nasabah', 'No. KTP', 'Karyawan',
                'Tgl Pengajuan', 'Pokok (Rp)', 'Bunga (Rp)', 'Tenor (Bln)', 'Status'];
    }

    public function map($row): array
    {
        $this->no++;
        return [
            $this->no,
            $row->no_pinjaman,
            $row->nasabah->nama_lengkap ?? '-',
            $row->nasabah->no_ktp       ?? '-',
            $row->karyawan->name        ?? '-',
            \Carbon\Carbon::parse($row->tanggal_pengajuan)->format('d/m/Y'),
            $row->jumlah_pinjaman,
            $row->total_bunga,
            $row->tenor_bulan,
            ucwords(str_replace('_', ' ', $row->status)),
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
        return 'Laporan Pinjaman';
    }
}