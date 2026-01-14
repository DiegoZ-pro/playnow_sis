<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - {{ $sale->sale_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; width: 80mm; margin: 0 auto; padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px dashed #000; padding-bottom: 10px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .info { margin-bottom: 10px; font-size: 12px; }
        .info p { margin: 3px 0; }
        .items { border-top: 2px dashed #000; border-bottom: 2px dashed #000; padding: 10px 0; margin: 10px 0; }
        .item { display: flex; justify-content: space-between; margin: 5px 0; font-size: 12px; }
        .item-name { flex: 1; }
        .totals { margin-top: 10px; font-size: 14px; }
        .total-line { display: flex; justify-content: space-between; margin: 5px 0; }
        .total-final { font-size: 18px; font-weight: bold; border-top: 2px solid #000; padding-top: 10px; margin-top: 10px; }
        .footer { text-align: center; margin-top: 20px; font-size: 11px; border-top: 2px dashed #000; padding-top: 10px; }
        @media print {
            body { width: 80mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PLAY NOW</h1>
        <p>Sistema de Ventas</p>
    </div>

    <div class="info">
        <p><strong>Venta:</strong> {{ $sale->sale_number }}</p>
        <p><strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Vendedor:</strong> {{ $sale->user->name }}</p>
        @if($sale->customer)
        <p><strong>Cliente:</strong> {{ $sale->customer->name }}</p>
        @if($sale->customer->phone)
        <p><strong>Teléfono:</strong> {{ $sale->customer->phone }}</p>
        @endif
        @endif
        <p><strong>Pago:</strong> 
            @if($sale->payment_method == 'cash') Efectivo
            @elseif($sale->payment_method == 'card') Tarjeta
            @else Transferencia
            @endif
        </p>
    </div>

    <div class="items">
        @foreach($sale->details as $detail)
        <div class="item">
            <div class="item-name">
                <div><strong>{{ $detail->productVariant->product->name }}</strong></div>
                <div style="font-size: 10px;">{{ $detail->productVariant->size->value }} / {{ $detail->productVariant->color->name }}</div>
                <div>{{ $detail->quantity }} x Bs {{ number_format($detail->unit_price, 2, ',', '.') }}</div>
            </div>
            <div><strong>Bs {{ number_format($detail->unit_price * $detail->quantity, 2, ',', '.') }}</strong></div>
        </div>
        @endforeach
    </div>

    <div class="totals">
        <div class="total-line">
            <span>SUBTOTAL:</span>
            <span>Bs {{ number_format($sale->subtotal, 2, ',', '.') }}</span>
        </div>
        @if($sale->discount > 0)
        <div class="total-line">
            <span>DESCUENTO:</span>
            <span>- Bs {{ number_format($sale->discount, 2, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-line total-final">
            <span>TOTAL:</span>
            <span>Bs {{ number_format($sale->total, 2, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <p>¡Gracias por su compra!</p>
        <p>PLAY NOW - Cochabamba, Bolivia</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
            Imprimir Ticket
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #666; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Cerrar
        </button>
    </div>

    <script>
        // Auto-print cuando carga
        window.onload = function() {
            // window.print();
        }
    </script>
</body>
</html>