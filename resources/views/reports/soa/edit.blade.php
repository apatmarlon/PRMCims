@extends('layouts.tabler')

@section('content')
<div class="container-xl">
</br>
    <h3 class="text-white">Edit SOA Transaction</h3>

    <form method="POST" action="{{ route('soa.update', $transaction) }}" class="bg-white p-4 rounded shadow">
        @csrf
        @method('PUT')

        <div class="mb-2">
            <label>Date</label>
            <input type="date" name="transaction_date"
                   value="{{ $transaction->transaction_date }}"
                   class="form-control">
        </div>

        <div class="mb-2">
            <label>Description</label>
            <input type="text" name="description"
                   value="{{ $transaction->description }}"
                   class="form-control">
        </div>
        <div class="mb-2">
            <label>Due Date (Optional)</label>
            <input type="date"
                name="due_date"
                value="{{ $transaction->due_date }}"
                class="form-control">
        </div>
        <div class="mb-2">
            <label>Debit</label>
            <input type="number" step="0.01" name="debit"
                   value="{{ $transaction->debit }}"
                   class="form-control">
        </div>

        <div class="mb-2">
            <label>Credit</label>
            <input type="number" step="0.01" name="credit"
                   value="{{ $transaction->credit }}"
                   class="form-control">
        </div>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
