@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h3>Customer Orders</h3>
    <div class="card shadow-sm border-0 mt-4">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Order ID</th>
                    <th>Buyer</th>
                    <th>Grave Info</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>#{{ $order->order_id }}</td>
                    <td>
                        {{ $order->buyer_name }}<br>
                        <small class="text-muted">{{ $order->buyer_phone }}</small>
                    </td>
                    <td>Plot {{ $order->grave_id }}</td>
                    <td>{{ $order->ledger->name }}</td>
                    <td>
                        <span class="badge bg-{{ $order->status == 'Installed' ? 'success' : 'warning' }}">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.orders.update', $order->order_id) }}" method="POST">
                            @csrf
                            <div class="input-group input-group-sm">
                                <select name="status" class="form-select">
                                    <option value="Pending">Pending</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Installed">Installed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                                <button class="btn btn-outline-primary">Update</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection