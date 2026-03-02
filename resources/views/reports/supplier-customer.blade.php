@extends('layouts.tabler')

@section('content')
<style>
    .white-box {
        background: #fff;
        padding: 15px;
        border-radius: 6px;
    }
</style>

<div class="page-body">
    <div class="container-xl">

        <h2 class="page-title row md-2 text-white">Supplier & Customer Report</h2>
<br>

        {{-- Export Buttons --}}
        <div class="mb-3">
            <form method="POST" action="{{ route('reports.supplier-customer.excel') }}" class="d-inline">
                @csrf
                <button class="btn btn-success">Export Excel</button>
            </form>
            <form method="POST" action="{{ route('reports.supplier-customer.pdf') }}" class="d-inline">
                @csrf
                <button class="btn btn-danger">Export PDF</button>
            </form>
        </div>

        <div class="row row-cards">

            {{-- Customers --}}
            <div class="col-md-6 table-responsive white-box">
                <h4>Customers</h4>
                <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->email }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Suppliers --}}
            <div class="col-md-6 table-responsive white-box">
                <h4>Suppliers</h4>
                <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Shop Name</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $s)
                        <tr>
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->email }}</td>
                            <td>{{ $s->shopname }}</td>
                            <td>
                                <span class="badge bg-primary text-white text-uppercase">{{ $s->type }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Chart --}}
        <div class="mt-5">
            <h4>Suppliers vs Customers Chart</h4>
            <canvas id="supplierCustomerChart"></canvas>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('supplierCustomerChart').getContext('2d');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Customers', 'Suppliers'],
        datasets: [{
            data: [{{ $customers->count() }}, {{ $suppliers->count() }}],
            backgroundColor: [
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 99, 132, 0.7)'
            ]
        }]
    },
});
</script>
@endsection
