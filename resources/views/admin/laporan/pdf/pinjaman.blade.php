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

        .right { text-align: right; }
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
        .karyawan { font-size: 9.5px; color: #4b5563; }
        .tgl { font-size: 9.5px; color: #6b7280; white-space: nowrap; }
        .amount {
            font-size: 10.5px;
            font-weight: 600;
            color: #111827;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }
        .amount-bunga { color: #059669; }
        .tenor {
            font-size: 10px;
            font-weight: 600;
            color: #374151;
        }

        /* ── BADGES ── */
        .badge {
            display: inline-block;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.3px;
            padding: 3px 9px;
            border-radius: 20px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .badge.aktif             { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge.lunas             { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge.ditolak           { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge.menunggu_approval { background: #fef9c3; color: #92400e; border: 1px solid #fde68a; }
        .badge.default           { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

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
            <div class="doc-title">Laporan Pinjaman</div>
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
                <div class="card-label">Total Pinjaman</div>
                <div class="card-value large">{{ $pinjaman->count() }}</div>
                <div class="card-sub">entri tercatat</div>
            </td>
            <td class="summary-card" style="background:#1a4080;">
                <div class="card-label">Total Pokok</div>
                <div class="card-value">Rp {{ number_format($totalPokok,0,',','.') }}</div>
                <div class="card-sub">jumlah pinjaman pokok</div>
            </td>
            <td class="summary-card" style="background:#22529a;border-radius:0 10px 10px 0;">
                <div class="card-label">Total Bunga</div>
                <div class="card-value">Rp {{ number_format($totalBunga,0,',','.') }}</div>
                <div class="card-sub">akumulasi imbal jasa</div>
            </td>
        </tr>
    </table>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th style="width:28px">#</th>
                <th>No. Pinjaman</th>
                <th>Nasabah</th>
                <th>Karyawan</th>
                <th>Tgl Pengajuan</th>
                <th class="right">Pokok</th>
                <th class="right">Bunga</th>
                <th class="center">Tenor</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pinjaman as $i => $item)
            <tr>
                <td><span class="row-num">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span></td>
                <td><span class="no-pinjaman">{{ $item->no_pinjaman }}</span></td>
                <td>
                    <div class="nasabah-name">{{ $item->nasabah->nama_lengkap ?? '-' }}</div>
                    <div class="nasabah-ktp">{{ $item->nasabah->no_ktp ?? '' }}</div>
                </td>
                <td><span class="karyawan">{{ $item->karyawan->name ?? '-' }}</span></td>
                <td><span class="tgl">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}</span></td>
                <td class="right"><span class="amount">Rp {{ number_format($item->jumlah_pinjaman,0,',','.') }}</span></td>
                <td class="right"><span class="amount amount-bunga">Rp {{ number_format($item->total_bunga,0,',','.') }}</span></td>
                <td class="center"><span class="tenor">{{ $item->tenor_bulan }} bln</span></td>
                <td class="center">
                    @php $s = $item->status; @endphp
                    <span class="badge {{ in_array($s,['aktif','lunas','ditolak','menunggu_approval']) ? $s : 'default' }}">
                        {{ ucwords(str_replace('_',' ',$s)) }}
                    </span>
                </td>
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
            &nbsp;&middot;&nbsp; Total {{ $pinjaman->count() }} data
        </div>
    </div>

</body>
</html>