<div class="pc-order-item">
  <div><strong>{{ $item->product_name }}</strong><p>{{ $item->variant_name }} · Số lượng: {{ $item->quantity }}</p></div>
  <strong>{{ vnd($item->line_total) }}</strong>
</div>
