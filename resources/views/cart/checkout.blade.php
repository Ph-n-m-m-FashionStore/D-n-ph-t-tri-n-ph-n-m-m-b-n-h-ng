@extends('layouts.app')
@section('title', 'Thanh toán')
@section('content')
<div class="container py-4">
    <div class="bg-white rounded-lg shadow p-4 max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Thanh toán</h1>
        <form method="POST" action="{{ route('cart.checkout') }}">
            @csrf
            <div class="row gx-4">
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" required value="{{ old('phone', $user->phone ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ giao hàng</label>
                        <input type="text" name="address" class="form-control" required value="{{ old('address', $user->address ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block mb-2">Phương thức thanh toán</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_type" id="pay-ewallet" value="ewallet">
                                <label class="form-check-label" for="pay-ewallet">Ví điện tử</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_type" id="pay-bank" value="bank">
                                <label class="form-check-label" for="pay-bank">Chuyển khoản/Thẻ</label>
                            </div>
                        </div>
                    </div>

                    <div id="ewallet-input" class="mb-3 d-none">
                        <label class="form-label">Mã tham chiếu (ví điện tử)</label>
                        <input type="text" name="bank_ref" class="form-control" placeholder="VD: VCB1234567">
                    </div>

                    <div id="bank-qr" class="mb-3 d-none text-center">
                        <label class="form-label d-block mb-2">Quét mã QR để thanh toán</label>
                        <img src="{{ asset('images/qr-bank.png') }}" alt="QR Thanh toán" class="img-fluid" style="max-width:200px">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ghi chú (tuỳ chọn)</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                    </div>

                    <div class="d-grid d-md-block">
                        <button type="submit" class="btn btn-success px-4 py-2">Xác nhận đặt hàng</button>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tóm tắt đơn hàng</h5>
                            <ul class="list-group mb-3">
                                @foreach($cartItems as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="small">{{ $item->product->name }} x {{ $item->quantity }}</div>
                                        <div class="fw-bold">{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}₫</div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>Tổng</div>
                                <div class="fw-bold">{{ number_format($total, 0, ',', '.') }}₫</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="payment_type"]');
    const ewalletDiv = document.getElementById('ewallet-input');
    const bankQrDiv = document.getElementById('bank-qr');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'ewallet') {
                ewalletDiv.classList.remove('d-none');
                bankQrDiv.classList.add('d-none');
            } else if (this.value === 'bank') {
                bankQrDiv.classList.remove('d-none');
                ewalletDiv.classList.add('d-none');
            }
        });
    });
});
</script>
@endsection
