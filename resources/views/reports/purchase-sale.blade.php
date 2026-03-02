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

        <h2 class="page-title row md-2 text-white">Purchase & Sale Report</h2>
        <br>

        <form method="GET" action="{{ route('reports.purchase-sale') }}" class="row g-3 mb-3">
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <div class="mb-3">
            <form method="POST" action="{{ route('reports.purchase-sale.excel') }}" class="d-inline">
                @csrf
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <button class="btn btn-success">Export Excel</button>
            </form>
            <form method="POST" action="{{ route('reports.purchase-sale.pdf') }}" class="d-inline">
                @csrf
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <button class="btn btn-danger">Export PDF</button>
            </form>
        </div>
        <div class="mb-3">
            <form method="POST" action="{{ route('reports.purchase-sale.excel-combined') }}" class="d-inline">
                @csrf
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <button class="btn btn-success">Export Excel (Combined)</button>
            </form>

            <form method="POST" action="{{ route('reports.purchase-sale.pdf-combined') }}" class="d-inline">
                @csrf
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <button class="btn btn-danger">Export PDF (Combined)</button>
            </form>
        </div>
        <div class="row row-cards">
            
            <div class="col-md-6 table-responsive white-box">
                <h4>Purchases</h4>
                <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr><th>Product</th><th>Quantity</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($purchases as $p)
                        <tr>
                            <td>{{ $p->product_name }}</td>
                            <td>{{ $p->quantity }}</td>
                            <td>{{ number_format($p->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="col-md-6 table-responsive white-box">
                <h4>Sales</h4>
                <table  class="table table-bordered card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr><th>Product</th><th>Quantity</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $s)
                        <tr>
                            <td>{{ $s->product_name }}</td>
                            <td>{{ $s->quantity }}</td>
                            <td>{{ number_format($s->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            <h4>Sales vs Purchases Chart</h4>
            <canvas id="purchaseSaleChart"></canvas>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('purchaseSaleChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [
            {
                label: 'Sales',
                data: @json($salesData),
            },
            {
                label: 'Purchases',
                data: @json($purchaseData),
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Sales vs Purchases' }
        },
        scales: {
            y: {
                ticks: {
                    color: '#ffffff'   // Y-axis numbers white
                },
                grid: {
                    color: '#ffffff'   // horizontal grid lines white
                }
            },
            x: {
                ticks: {
                    color: '#ffffff',  // X-axis labels white
                    maxRotation: 90,   // optional: to handle long labels if needed
                    minRotation: 45
                },
                grid: {
                    color: 'rgba(255,255,255,0.2)'  // optional: x grid lines subtle white
                }
            }
        }
    }
});
</script>

@endsection
