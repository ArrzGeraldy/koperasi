<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekapitulasi Anggota</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size:12px; }
        .container { padding: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background:#f3f4f6; }

        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { margin: 0; }
            .no-print { display: none !important; visibility: hidden !important; }
        }
        .right { text-align: right }
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <div>
                <h2 style="margin:0">{{ config('app.name') }} - Rekapitulasi Anggota</h2>
                <div style="color:#555">Periode: {{ sprintf('%02d', $selectedMonth ?? now()->month) }} / {{ $selectedYear ?? now()->year }}</div>
            </div>
            <div class="no-print" style="display:flex;gap:8px;align-items:center">
                <form method="get" action="{{ route('admin.print.members') }}">
                    <label for="month">Bulan</label>
                    <select id="month" name="month">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ (isset($selectedMonth) && $selectedMonth == $m) ? 'selected' : ( (!isset($selectedMonth) && $m==now()->month) ? 'selected' : '') }}>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
                        @endforeach
                    </select>
                    <label for="year">Tahun</label>
                    <select id="year" name="year">
                        @php
                            $current = now()->year;
                            $start = $current - 5;
                            $end = $current + 1;
                        @endphp
                        @foreach(range($start, $end) as $y)
                            <option value="{{ $y }}" {{ (isset($selectedYear) && $selectedYear == $y) ? 'selected' : ( (!isset($selectedYear) && $y==now()->year) ? 'selected' : '') }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </form>
                <button onclick="window.print()" style="padding:8px 12px;margin-bottom:8px">Cetak</button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:30px">No.</th>
                    <th>Nama</th>
                    <th>Surel</th>
                    <th>Telepon</th>
                    <th class="right">Simpanan Pokok</th>
                    <th class="right">Simpanan Wajib</th>
                    <th class="right">Simpanan Sukarela</th>
                    <th class="right">Total Simpanan</th>
                    <th class="right">Jumlah Pinjaman</th>
                    <th class="right">Total Pinjaman</th>
                    <th class="right">Tunggakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $i => $m)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $m->user->name }}</td>
                        <td>{{ $m->user->email }}</td>
                        <td>{{ $m->user->phone }}</td>
                        <td class="right">{{ number_format($m->simpanan_pokok,2,',','.') }}</td>
                        <td class="right">{{ number_format($m->simpanan_wajib,2,',','.') }}</td>
                        <td class="right">{{ number_format($m->simpanan_sukarela,2,',','.') }}</td>
                        <td class="right">{{ number_format($m->total_simpanan,2,',','.') }}</td>
                        <td class="right">{{ $m->pinjaman_count }}</td>
                        <td class="right">{{ number_format($m->total_pinjaman,2,',','.') }}</td>
                        <td class="right">{{ number_format($m->outstanding,2,',','.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
