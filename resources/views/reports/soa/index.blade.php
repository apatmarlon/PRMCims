@extends('layouts.tabler')

@section('content')
<div class="container-xl">
</br>
    <h2 class="text-white">Customer Statement of Account</h2>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Current Balance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>
                {{ number_format(
                    $customer->statementAccount?->transactions?->last()?->balance
                    ?? $customer->statementAccount?->beginning_balance
                    ?? 0,
                    2
                ) }}
            </td>
                <td>
                    <a href="{{ route('soa.show', $customer) }}"
                       class="btn btn-primary btn-sm">
                        View SOA
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
