@extends('layouts.app')

@section('title', 'Theo dõi đơn hàng')

@section('content')
<div class="container py-4">
    <div class="bg-white rounded-lg shadow p-6 mx-auto" style="max-width:1000px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 m-0">Theo dõi đơn hàng</h1>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">Tiếp tục mua sắm</a>
        </div>

        @if(isset($orders) && $orders->count())
            <div id="ordersContainer">
                @foreach($orders as $order)
                    <div class="border rounded p-3 mb-3" data-order-id="{{ $order->id }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">Đơn hàng #{{ $order->id }}</div>
                                <div class="text-muted" style="font-size: 12px;">Ngày đặt: {{ optional($order->created_at)->format('d/m/Y H:i') }}</div>
                            </div>
                            <div>
                                <span class="badge bg-secondary me-2" data-status-label>{{ ucfirst($order->status) }}</span>
                                <a class="btn btn-sm btn-link" href="{{ route('orders.show', $order->id) }}">Chi tiết</a>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="d-flex align-items-center" style="gap:12px;">
                                @php
                                    $steps = [
                                        'pending' => 'Chờ xác nhận',
                                        'confirmed' => 'Đã xác nhận',
                                        'shipping' => 'Đang giao',
                                        'completed' => 'Hoàn tất',
                                        'canceled' => 'Đã hủy',
                                    ];
                                    $status = $order->status;
                                    $keys = array_keys($steps);
                                    $currentIndex = array_search($status, $keys);
                                @endphp
                                @foreach($steps as $key => $label)
                                    @php
                                        $index = array_search($key, $keys);
                                        $active = $index <= $currentIndex && $status !== 'canceled';
                                        $isCanceled = $status === 'canceled' && $key === 'canceled';
                                        $bg = $isCanceled ? '#ef4444' : ($active ? '#22c55e' : '#d1d5db');
                                        $text = $active || $isCanceled ? '#fff' : '#6b7280';
                                    @endphp
                                    <div class="text-center" style="min-width:70px;">
                                        <div style="width:28px;height:28px;border-radius:50%;background:{{ $bg }};color:{{ $text }};display:flex;align-items:center;justify-content:center;font-weight:bold;margin:0 auto;">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div style="font-size:12px;margin-top:4px;white-space:nowrap;">{{ $label }}</div>
                                    </div>
                                    @if(!$loop->last)
                                        <div style="flex:1;height:4px;background:{{ ($index < $currentIndex && $status !== 'canceled') ? '#22c55e' : '#d1d5db' }};border-radius:2px;"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-3 d-flex" style="gap:8px;flex-wrap:wrap;">
                            @php $items = $order->orderItems ?? collect(); @endphp
                            @foreach($items as $item)
                                <img src="{{ asset('images/' . basename($item->product->image_url)) }}" alt="{{ $item->product->name }}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const NOTIFY_KEY = 'fs_orders_last_statuses';
    const container = document.getElementById('ordersContainer');
    if (!container) return;

    function loadCache() {
        try { return JSON.parse(localStorage.getItem(NOTIFY_KEY) || '{}'); } catch { return {}; }
    }
    function saveCache(data) { localStorage.setItem(NOTIFY_KEY, JSON.stringify(data)); }

    const cache = loadCache();

    async function pollStatuses() {
        try {
            const res = await fetch('{{ route('orders.statuses') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const data = await res.json();
            if (!data.orders) return;

            const newCache = {};
            data.orders.forEach(o => {
                newCache[o.id] = o.status;
                const el = container.querySelector(`[data-order-id="${o.id}"] [data-status-label]`);
                if (el) {
                    const old = cache[o.id];
                    if (old && old !== o.status) {
                        el.textContent = capitalize(o.status);
                        toast(`Đơn #${o.id} cập nhật trạng thái: ${labelStatus(o.status)}`);
                        flashRow(o.id);
                    } else {
                        el.textContent = capitalize(o.status);
                    }
                }
            });

            saveCache(newCache);
        } catch (e) {
            // im lặng khi lỗi
        }
    }

    function labelStatus(s) {
        const map = { pending: 'Chờ xác nhận', confirmed: 'Đã xác nhận', shipping: 'Đang giao', completed: 'Hoàn tất', canceled: 'Đã hủy' };
        return map[s] || s;
    }
    function capitalize(s){ return (s||'').charAt(0).toUpperCase() + (s||'').slice(1); }

    function toast(message) {
        const t = document.createElement('div');
        t.textContent = message;
        t.style.position = 'fixed';
        t.style.right = '16px';
        t.style.bottom = '16px';
        t.style.background = 'rgba(0,0,0,.8)';
        t.style.color = '#fff';
        t.style.padding = '10px 12px';
        t.style.borderRadius = '8px';
        t.style.zIndex = '9999';
        t.style.fontSize = '14px';
        document.body.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .4s'; }, 1800);
        setTimeout(() => { t.remove(); }, 2300);
    }

    function flashRow(orderId) {
        const card = container.querySelector(`[data-order-id="${orderId}"]`);
        if (!card) return;
        const orig = card.style.boxShadow;
        card.style.boxShadow = '0 0 0 3px #22c55e inset';
        setTimeout(() => { card.style.boxShadow = orig; }, 1000);
    }

    // Bắt đầu polling
    pollStatuses();
    setInterval(pollStatuses, 5000);
});
</script>
@endsection













