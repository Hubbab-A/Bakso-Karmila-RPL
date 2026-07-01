<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota {{ $transaksi->no_nota }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; background: #f5f5f5; }

        .wrapper { display: flex; justify-content: center; padding: 20px; }
        .nota {
            background: white;
            width: 300px;
            padding: 20px 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header { text-align: center; margin-bottom: 12px; }
        .header h1 { font-size: 14px; font-weight: bold; }
        .header p { font-size: 11px; color: #555; margin-top: 2px; }

        .divider { border-top: 1px dashed #333; margin: 10px 0; }
        .divider-solid { border-top: 1px solid #333; margin: 10px 0; }

        .info-row { display: flex; justify-content: space-between; margin-bottom: 3px; font-size: 11px; }

        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        table thead th { text-align: left; font-size: 11px; padding: 3px 0; border-bottom: 1px dashed #333; }
        table tbody td { padding: 4px 0; font-size: 11px; vertical-align: top; }
        table tbody td:last-child { text-align: right; }

        .total-section { margin-top: 8px; }
        .total-row { display: flex; justify-content: space-between; font-size: 11px; padding: 2px 0; }
        .total-row.grand { font-weight: bold; font-size: 13px; margin-top: 4px; }
        .total-row.kembalian { color: #16a34a; font-weight: bold; }

        .footer { text-align: center; margin-top: 14px; font-size: 10px; color: #666; }

        .btn-group { display: flex; gap: 10px; justify-content: center; margin-top: 20px; }
        .btn { padding: 10px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: bold; }
        .btn-print { background: #dc2626; color: white; }
        .btn-close { background: #e5e7eb; color: #374151; }

        @media print {
            body { background: white; }
            .wrapper { padding: 0; }
            .nota { box-shadow: none; }
            .btn-group { display: none; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div>
        <div class="nota" id="nota-print">
            {{-- Header --}}
            <div class="header">
                <h1>{{ $setting['nama_warung'] ?? 'Warung Bakso' }}</h1>
                <p>{{ $setting['alamat'] ?? '' }}</p>
                <p>{{ $setting['telp'] ?? '' }}</p>
            </div>

            <div class="divider-solid"></div>

            {{-- Info Transaksi --}}
            <div class="info-row"><span>No. Nota</span><span>{{ $transaksi->no_nota }}</span></div>
            <div class="info-row"><span>Tanggal</span><span>{{ $transaksi->tanggal->format('d/m/Y H:i') }}</span></div>
            <div class="info-row"><span>Kasir</span><span>{{ $transaksi->user->name }}</span></div>
            <div class="info-row"><span>Metode</span><span class="capitalize">{{ $transaksi->metode_bayar }}</span></div>

            <div class="divider"></div>

            {{-- Item Pesanan --}}
            <table>
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th style="text-align:center">Qty</th>
                        <th style="text-align:right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi->details as $detail)
                    <tr>
                        <td>
                            <div>{{ $detail->nama_menu }}</div>
                            <div style="color:#888;font-size:10px">@ Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</div>
                        </td>
                        <td style="text-align:center">{{ $detail->qty }}</td>
                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="divider"></div>

            {{-- Total --}}
            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($transaksi->diskon > 0)
                <div class="total-row">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="total-row grand">
                    <span>TOTAL</span>
                    <span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                </div>
                @if($transaksi->metode_bayar === 'tunai')
                <div class="total-row">
                    <span>Bayar</span>
                    <span>Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
                </div>
                <div class="total-row kembalian">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>

            @if($transaksi->catatan)
            <div class="divider"></div>
            <p style="font-size:10px;color:#555">Catatan: {{ $transaksi->catatan }}</p>
            @endif

            <div class="divider-solid"></div>

            <div class="footer">
                <p>{{ $setting['footer_nota'] ?? 'Terima kasih telah berkunjung!' }}</p>
                <p style="margin-top:4px;color:#999">—— Simpan struk ini ——</p>
            </div>
        </div>

        {{-- Tombol print (tidak muncul saat print) --}}
        <div class="btn-group">
            <button class="btn btn-print" onclick="window.print()">
                🖨️ Cetak Nota
            </button>
            <button class="btn btn-close" onclick="window.close()">
                ✕ Tutup
            </button>
        </div>
    </div>
</div>

<script>
    // Auto print jika URL mengandung ?print=1
    if (new URLSearchParams(window.location.search).get('print') === '1') {
        window.onload = () => setTimeout(() => window.print(), 500);
    }
</script>
</body>
</html>
