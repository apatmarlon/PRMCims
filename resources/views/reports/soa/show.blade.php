@extends('layouts.tabler')

@section('content')
<div class="container-xl">
</br>
    <h3 class="text-center text-white">Statement of Account</h3>
    <div class="col-12 mt-4 bg-white p-3 rounded shadow d-flex justify-content-between align-items-center">
        <p class="text-dark mb-0 fs-3">Customer: <strong>{{ $customer->name }}</strong></p>
        
        <div>
            <a href="{{ route('soa.create', $customer) }}" class="btn btn-success btn-sm">
                Add Transaction
            </a>
            <a href="{{ route('soa.pdf', $customer) }}" class="btn btn-danger btn-sm">
                Export PDF
            </a>

            <a href="{{ route('soa.excel', $customer) }}" class="btn btn-info btn-sm">
                Export Excel
            </a>
            <a href="{{ route('soa.index') }}" class="btn btn-primary btn-sm">
                Back
            </a>
        </div>
    </div>

    <table class="table table-bordered table-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Ref #</th>
                <th>Due Date</th>
                <th>Description</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
                <th class="text-end">Balance</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td colspan="6"><strong>Beginning Balance</strong></td>
                <td class="text-end">
                    {{ number_format($soa->beginning_balance, 2) }}
                </td>
                <td></td>
            </tr>

            @foreach($soa->transactions as $row)
            <tr>
                <td>{{ $row->transaction_date }}</td>
                <td>{{ $row->ref_no }}</td>
                <td>{{ $row->due_date ?? '-' }}</td>
                <td>{{ $row->description }}</td>
                <td class="text-end">{{ number_format($row->debit, 2) }}</td>
                <td class="text-end">{{ number_format($row->credit, 2) }}</td>
                <td class="text-end">{{ number_format($row->balance, 2) }}</td>
                <td class="text-end">
                    <x-button.edit class="btn-icon" route="{{ route('soa.edit', $row) }}"/>
                   
                    <x-button.delete class="btn-icon" route="{{ route('soa.destroy', $row) }}"/>
                    
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
