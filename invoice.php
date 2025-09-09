<?php
if (session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__.'/functions.php';

$no = $_GET['no'] ?? null;
$mode = $_GET['mode'] ?? null;
$order = $_SESSION['last_order'] ?? null;
if(!$order || ($no && $order['order_no'] !== $no)){
  http_response_code(404);
  echo '<!doctype html><html><head><meta charset="utf-8"><title>Invoice</title></head><body><div style="font-family:Arial,sans-serif;padding:24px">'
      .'Invoice tidak ditemukan. Silakan buat pesanan terlebih dahulu.'
      .'</div></body></html>';
  exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invoice <?php echo esc($order['order_no']); ?> — Rebelstuff Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root{--ink:#111827;--muted:#6b7280}
body{background:#f8f9fa; color:var(--ink)}
.invoice-page{max-width:900px; margin:24px auto}
.logo{display:flex; align-items:center; gap:10px}
.logo img{height:28px}
.badge-order{font-size:.85rem}
.table th, .table td{vertical-align:middle}
@media print{
  body{background:#fff}
  .no-print{display:none!important}
  .card{box-shadow:none; border:1px solid #ddd}
}
</style>
</head>
<body>
<div class="invoice-page">
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div class="logo">
      <img src="favicon.svg" alt="Logo">
      <div>
        <div class="fw-bold">Rebelstuff Store</div>
        <div class="text-muted small">Kp. Pitara Rangkapanjaya, Depok<br>Telp/WA: 087874872257</div>
      </div>
    </div>
    <div class="text-end">
      <div class="badge bg-primary badge-order">Invoice</div>
      <div class="fw-bold">#<?php echo esc($order['order_no']); ?></div>
      <div class="text-muted small"><?php echo esc($order['created_at']); ?></div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <div class="card p-3">
        <div class="fw-semibold mb-1">Kepada:</div>
        <div><?php echo esc($order['customer']['full_name']); ?></div>
        <div class="text-muted small"><?php echo esc($order['customer']['phone']); ?></div>
        <div class="text-muted small"><?php echo nl2br(esc($order['customer']['address'])); ?></div>
        <div class="text-muted small"><?php echo esc($order['customer']['city']); ?>, <?php echo esc($order['customer']['province']); ?> <?php echo esc($order['customer']['postal_code']); ?></div>
        <?php if(!empty($order['customer']['notes'])): ?>
        <div class="text-muted small">Catatan: <?php echo esc($order['customer']['notes']); ?></div>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3">
        <div class="fw-semibold mb-1">Pengiriman:</div>
        <div class="text-muted small">Kurir: <?php echo esc($order['courier']); ?> (<?php echo esc($order['service']); ?>)</div>
        <div class="text-muted small">Ongkir: <?php echo formatRupiah($order['shipping']); ?></div>
        <div class="text-muted small">Subtotal: <?php echo formatRupiah($order['subtotal']); ?></div>
        <div class="fw-bold">Total: <?php echo formatRupiah($order['total']); ?></div>
      </div>
    </div>
  </div>

  <div class="card p-3">
    <div class="table-responsive">
      <table class="table">
        <thead><tr><th>Produk</th><th class="text-center">Ukuran</th><th class="text-end">Harga</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr></thead>
        <tbody>
        <?php foreach($order['items'] as $it): $st = $it['price'] * $it['qty']; ?>
          <tr>
            <td><?php echo esc($it['name']); ?></td>
            <td class="text-center"><?php echo esc($it['size']); ?></td>
            <td class="text-end"><?php echo formatRupiah($it['price']); ?></td>
            <td class="text-center"><?php echo (int)$it['qty']; ?></td>
            <td class="text-end"><?php echo formatRupiah($st); ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><th colspan="4" class="text-end">Subtotal</th><th class="text-end"><?php echo formatRupiah($order['subtotal']); ?></th></tr>
          <tr><th colspan="4" class="text-end">Ongkir (<?php echo esc($order['courier'].' - '.$order['service']); ?>)</th><th class="text-end"><?php echo formatRupiah($order['shipping']); ?></th></tr>
          <tr><th colspan="4" class="text-end">Total</th><th class="text-end"><?php echo formatRupiah($order['total']); ?></th></tr>
        </tfoot>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2 no-print">
      <div class="text-muted small">* Simpan sebagai PDF via dialog print browser.</div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.close()">Tutup</button>
        <button class="btn btn-primary" onclick="window.print()">Cetak</button>
      </div>
    </div>
  </div>
</div>

<?php if($mode==='print' || $mode==='pdf'): ?>
<script>
window.addEventListener('load', function(){ window.print(); });
</script>
<?php endif; ?>
</body>
</html>
