@extends('layouts.tabler')

@section('content')
<div class="container-xl">
</br>
    <h3 class="text-white">Add SOA Transaction for <strong>{{ $customer->name }}</strong></h3>

    <form method="POST" id="soaForm" action="{{ route('soa.store', $customer) }}" class="bg-white p-4 rounded shadow">
        @csrf

        <div class="mb-2">
            <label>Date</label>
            <input
                type="date"
                name="transaction_date"
                class="form-control"
                value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
            >
        </div>
       <div class="mb-2">
            <label class="form-label">Reference (Optional)</label>

            <div class="row g-2 align-items-start">
                <!-- LEFT COLUMN -->
                <div class="col-md-6">
                    <div class="row g-2 align-items-center">
                        <!-- Checkbox -->
                        <div class="col-auto">
                            <div class="form-check m-0">
                                <input class="form-check-input" type="checkbox" id="fromOrders">
                                <label class="form-check-label" for="fromOrders">
                                    From sales?
                                </label>
                            </div>
                        </div>

                        <!-- Reference input -->
                        <div class="col">
                            <input type="text" name="ref_no" id="ref_no" class="form-control" 
                                placeholder="Type check number or reference here" >
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="col-md-6 d-none" id="orderSearchBox">
                    <input
                        type="text"
                        id="orderSearch"
                        class="form-control"
                        placeholder="Search invoice no..."
                        autocomplete="off"
                    >

                    <div class="list-group mt-1" id="orderResults"></div>
                </div>
            </div>
        </div>
        <div class="mb-2">
            <label>Due Date (Optional)</label>
            <input type="date" name="due_date" class="form-control">
        </div>
        <div class="mb-2">
            <label>Description</label>
            <input type="text" name="description" class="form-control">
        </div>
        <div class="mb-2">
            <label>Debit</label>
            <input type="number" step="0.01" name="debit" class="form-control" value="0">
        </div>

        <div class="mb-2">
            <label>Credit</label>
            <input type="number" step="0.01" name="credit" class="form-control" value="0">
        </div>

        
        <x-button.back  route="{{ route('soa.show', $customer) }}">Back</x-button.back>
        <button  type="button" id="submitBtn" class="btn btn-success">Add Transaction</button>
    </form>
</div>

<script>
document.getElementById('submitBtn').addEventListener('click', function () {

    const transactionDate = document.querySelector('input[name="transaction_date"]').value;
    const description     = document.querySelector('input[name="description"]').value.trim();
    const debit  = parseFloat(document.querySelector('input[name="debit"]').value) || 0;
    const credit = parseFloat(document.querySelector('input[name="credit"]').value) || 0;

    // ❗ Required fields
    if (!transactionDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Date',
            text: 'Transaction date is required.',
            confirmButtonColor: '#206bc4'
        });
        return;
    }

    if (!description) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Description',
            text: 'Description is required.',
            confirmButtonColor: '#206bc4'
        });
        return;
    }

    // ❗ Accounting rule: only one allowed
    if ((debit > 0 && credit > 0) || (debit === 0 && credit === 0)) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Amount',
            text: 'Please enter a value in either Debit OR Credit only.',
            confirmButtonColor: '#206bc4'
        });
        return;
    }

    Swal.fire({
        title: 'Confirm Transaction',
        text: 'Do you want to save this SOA transaction?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, save it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#206bc4',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('soaForm').submit();
        }
    });
});

const fromOrdersCheckbox = document.getElementById('fromOrders');
const orderSearchBox     = document.getElementById('orderSearchBox');
const orderSearchInput  = document.getElementById('orderSearch');
const orderResults      = document.getElementById('orderResults');

fromOrdersCheckbox.addEventListener('change', function () {
    orderSearchBox.classList.toggle('d-none', !this.checked);

      const refInput = document.getElementById('ref_no');

    if (this.checked) {
        refInput.value = '';
        refInput.readOnly = true; // ✅ readonly instead of disabled
    } else {
        refInput.readOnly = false;
        refInput.value = ''; // server will auto-generate
    }

    orderResults.innerHTML = '';
});

orderSearchInput.addEventListener('input', function () {
    const query = this.value.trim();

    if (query.length < 2) {
        orderResults.innerHTML = '';
        return;
    }

    fetch(`{{ route('soa.orders.search', $customer) }}?q=${query}`)
        .then(res => res.json())
        .then(data => {
            orderResults.innerHTML = '';

            data.forEach(order => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action';
                item.innerHTML = `
                    <strong>${order.invoice_no}</strong>
                    <span class="float-end">₱ ${parseFloat(order.total).toFixed(2)}</span>
                `;

                item.addEventListener('click', e => {
                    e.preventDefault();

                    document.getElementById('ref_no').value = order.invoice_no;
                    document.querySelector('input[name="debit"]').value = order.total;
                    document.querySelector('input[name="credit"]').value = 0;

                    orderSearchBox.classList.add('d-none');
                    fromOrdersCheckbox.checked = false;
                    orderResults.innerHTML = '';
                });

                orderResults.appendChild(item);
            });
        });
});
</script>

       
@endsection
