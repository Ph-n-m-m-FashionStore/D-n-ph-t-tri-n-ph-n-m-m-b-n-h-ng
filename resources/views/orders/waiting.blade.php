@extends('layouts.app')

@section('title', 'Đang chờ xác nhận thanh toán')

@section('content')
<div class="container py-4">
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto text-center">
        <h1 class="h4 mb-3">Đang chờ xác nhận thanh toán</h1>
        <p class="text-muted">Đơn hàng #{{ $order->id }} - Vui lòng đợi trong <span id="countdown">120</span>s</p>

        <div class="my-4">
            <img src="{{ url('/images/undraw_rocket.svg') }}" alt="waiting" style="width:180px;opacity:.9;">
        </div>

        <div class="progress" style="height: 8px;">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
        </div>

        <div class="mt-4">
            <div>Trạng thái thanh toán: <b id="payStatus">{{ optional($order->payment)->status ?? 'pending' }}</b></div>
            <div id="successAlert" class="alert alert-success mt-3" style="display:none;">Thanh toán đã được xác nhận! Đang chuyển đến chi tiết đơn hàng...</div>
        </div>

        <div class="mt-4" id="actions" style="display:none;">
            <a id="successLink" href="{{ route('orders.show', $order->id) }}" class="btn btn-success">Xem chi tiết đơn hàng</a>
            <a id="failLink" href="{{ route('home') }}" class="btn btn-outline-secondary">Về trang chủ</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let seconds = 120;
    const countdownEl = document.getElementById('countdown');
    const progressBar = document.getElementById('progressBar');
    const payStatusEl = document.getElementById('payStatus');
    const actions = document.getElementById('actions');
    const successLink = document.getElementById('successLink');
    const failLink = document.getElementById('failLink');

    const poll = async () => {
        try {
            const res = await fetch('{{ route('orders.status', $order->id) }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const data = await res.json();
            if (data.payment_status) {
                payStatusEl.textContent = data.payment_status;
                if (data.payment_status === 'paid') {
                    actions.style.display = 'block';
                    successLink.style.display = 'inline-block';
                    failLink.style.display = 'none';
                    clearInterval(timer);
                    // Cập nhật giao diện khi đã thanh toán
                    document.getElementById('successAlert').style.display = 'block';
                    progressBar.style.width = '100%';
                    countdownEl.textContent = '✓';
                    // Tự động chuyển sang chi tiết đơn sau 1.2s
                    setTimeout(() => { window.location.href = successLink.getAttribute('href'); }, 1200);
                }
            }
        } catch (e) {}
    };

    const timer = setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;
        progressBar.style.width = ((120 - seconds) / 120 * 100) + '%';
        poll();
        if (seconds <= 0) {
            clearInterval(timer);
            // Hết thời gian: nếu chưa paid, đưa về home với thông báo
            if (payStatusEl.textContent !== 'paid') {
                actions.style.display = 'block';
                successLink.style.display = 'none';
                failLink.style.display = 'inline-block';
                // Chuyển hướng sau 1.5s kèm thông báo qua querystring
                setTimeout(() => { window.location.href = '{{ route('home') }}?payment=failed&order={{ $order->id }}'; }, 1500);
            }
        }
    }, 1000);
});
</script>
@endsection


