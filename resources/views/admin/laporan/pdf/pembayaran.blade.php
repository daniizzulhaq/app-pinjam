<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            font-size: 10.5px;
            color: #1a1d23;
            background: #ffffff;
            padding: 32px 36px;
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding-bottom: 20px;
            border-bottom: 2px solid #0f2d6b;
            margin-bottom: 20px;
        }
        .brand-mark {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-icon {
            width: 38px;
            height: 38px;
            background: #0f2d6b;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brand-icon svg { display: block; }
        .brand-text .name {
            font-size: 14px;
            font-weight: 700;
            color: #0f2d6b;
            letter-spacing: -0.3px;
        }
        .brand-text .tagline {
            font-size: 9px;
            color: #6b7280;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 1px;
        }
        .header-right { text-align: right; }
        .doc-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f2d6b;
            letter-spacing: -0.5px;
        }
        .doc-meta {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 3px;
        }
        .periode-badge {
            display: inline-block;
            background: #eff6ff;
            color: #1e40af;
            font-size: 9px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            margin-top: 5px;
            border: 1px solid #bfdbfe;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* ── SUMMARY CARDS ── */
        .summary-grid {
            display: table;
            width: 100%;
            border-spacing: 0;
            margin-bottom: 22px;
        }
        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 14px 18px;
            vertical-align: top;
        }
        .summary-card:first-child {
            background: #0f2d6b;
            border-radius: 10px 0 0 10px;
        }
        .summary-card:nth-child(2) {
            background: #1a4080;
        }
        .summary-card:last-child {
            background: #22529a;
            border-radius: 0 10px 10px 0;
        }
        .card-label {
            font-size: 8.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(255,255,255,0.65);
            margin-bottom: 6px;
        }
        .card-value {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            line-height: 1;
        }
        .card-value.large { font-size: 24px; }
        .card-sub {
            font-size: 8.5px;
            color: rgba(255,255,255,0.5);
            margin-top: 4px;
        }

        /* ── TABLE ── */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        thead tr th {
            background: #f8fafc;
            color: #374151;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 10px 12px;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }
        thead tr th:first-child { border-left: 1px solid #e5e7eb; border-radius: 8px 0 0 0; padding-left: 14px; }
        thead tr th:last-child  { border-right: 1px solid #e5e7eb; border-radius: 0 8px 0 0; }

        tbody tr td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 10px;
            color: #374151;
        }
        tbody tr td:first-child { padding-left: 14px; border-left: 1px solid #e5e7eb; }
        tbody tr td:last-child  { border-right: 1px solid #e5e7eb; }

        tbody tr:last-child td:first-child { border-radius: 0 0 0 8px; }
        tbody tr:last-child td:last-child  { border-radius: 0 0 8px 0; }
        tbody tr:last-child td { border-bottom: 1px solid #e5e7eb; }

        tbody tr:hover td { background: #f8fafc; }

        .right  { text-align: right; }
        .center { text-align: center; }

        /* ── CELLS ── */
        .no-pinjaman {
            font-family: 'Courier New', monospace;
            font-size: 9.5px;
            font-weight: 600;
            color: #1e40af;
            background: #eff6ff;
            padding: 2px 7px;
            border-radius: 4px;
            display: inline-block;
        }
        .nasabah-name { font-weight: 600; font-size: 10.5px; color: #111827; }
        .nasabah-ktp  { font-size: 8.5px; color: #9ca3af; margin-top: 1px; }
        .karyawan     { font-size: 9.5px; color: #4b5563; }
        .tgl          { font-size: 9.5px; color: #6b7280; white-space: nowrap; }
        .amount {
            font-size: 10.5px;
            font-weight: 600;
            color: #111827;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }
        .amount-dibayar { color: #059669; }
        .amount-denda   { color: #dc2626; }
        .amount-muted   { color: #d1d5db; font-weight: 400; }
        .amount-total   { color: #111827; }

        .row-num {
            font-size: 9.5px;
            color: #d1d5db;
            font-weight: 600;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
        }
        .footer-left {
            font-size: 8.5px;
            color: #9ca3af;
        }
        .footer-right {
            font-size: 8.5px;
            color: #9ca3af;
            text-align: right;
        }
        .footer-accent {
            display: inline-block;
            width: 6px;
            height: 6px;
            background: #0f2d6b;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="brand-mark">
            <div class="brand-icon">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="10" width="4" height="9" rx="1" fill="white" opacity="0.6"/>
                    <rect x="9" y="6" width="4" height="13" rx="1" fill="white" opacity="0.8"/>
                    <rect x="15" y="3" width="4" height="16" rx="1" fill="white"/>
                </svg>
            </div>
            <div class="brand-text">
                <div class="name">KoperasiSejahtera</div>
                <div class="tagline">Sistem Informasi Keuangan</div>
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">Laporan Pembayaran</div>
            <div class="doc-meta">Dicetak: {{ now()->format('d M Y, H:i') }} WIB</div>
            <div class="periode-badge">
                @if($request->bulan || $request->tahun)
                    &#128197;&nbsp;
                    {{ $request->bulan ? \Carbon\Carbon::create()->month($request->bulan)->translatedFormat('F') : 'Semua Bulan' }}
                    {{ $request->tahun ?? '' }}
                @else
                    &#128197;&nbsp; Semua Periode
                @endif
            </div>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <table class="summary-grid">
        <tr>
            <td class="summary-card" style="background:#0f2d6b;border-radius:10px 0 0 10px;">
                <div class="card-label">Total Transaksi</div>
                <div class="card-value large">{{ $pembayaran->count() }}</div>
                <div class="card-sub">entri tercatat</div>
            </td>
            <td class="summary-card" style="background:#1a4080;">
                <div class="card-label">Total Dibayar</div>
                <div class="card-value">Rp {{ number_format($totalDibayar,0,',','.') }}</div>
                <div class="card-sub">jumlah pembayaran diterima</div>
            </td>
            <td class="summary-card" style="background:#22529a;border-radius:0 10px 10px 0;">
                <div class="card-label">Total Denda</div>
                <div class="card-value">Rp {{ number_format($totalDenda,0,',','.') }}</div>
                <div class="card-sub">akumulasi denda keterlambatan</div>
            </td>
        </tr>
    </table>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th style="width:28px">#</th>
                <th>Nasabah</th>
                <th>No. Pinjaman</th>
                <th>Karyawan</th>
                <th>Tgl Bayar</th>
                <th class="right">Dibayar</th>
                <th class="right">Denda</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembayaran as $i => $item)
            <tr>
                <td><span class="row-num">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span></td>
                <td>
                    <div class="nasabah-name">{{ $item->pinjaman->nasabah->nama_lengkap ?? '-' }}</div>
                    <div class="nasabah-ktp">{{ $item->pinjaman->nasabah->no_ktp ?? '' }}</div>
                </td>
                <td><span class="no-pinjaman">{{ $item->pinjaman->no_pinjaman ?? '-' }}</span></td>
                <td><span class="karyawan">{{ $item->karyawan->name ?? '-' }}</span></td>
                <td><span class="tgl">{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d M Y') }}</span></td>
                <td class="right"><span class="amount amount-dibayar">Rp {{ number_format($item->jumlah_dibayar,0,',','.') }}</span></td>
                <td class="right">
                    @if($item->denda > 0)
                        <span class="amount amount-denda">Rp {{ number_format($item->denda,0,',','.') }}</span>
                    @else
                        <span class="amount amount-muted">&mdash;</span>
                    @endif
                </td>
                <td class="right"><span class="amount amount-total">Rp {{ number_format($item->jumlah_dibayar + $item->denda,0,',','.') }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-left">
            <span class="footer-accent"></span>
            Dokumen ini dicetak secara otomatis oleh sistem &mdash; tidak memerlukan tanda tangan.
        </div>
        <div class="footer-right">
            Halaman <span style="font-weight:600;color:#374151;">1</span> dari <span style="font-weight:600;color:#374151;">1</span>
            &nbsp;&middot;&nbsp; Total {{ $pembayaran->count() }} data
        </div>
    </div>

</body>
</html>