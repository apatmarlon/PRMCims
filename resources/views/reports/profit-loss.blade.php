@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">

        <h2 class="page-title row md-2 text-white">Profit & Loss Report</h2>

        <div class="row row-cards mt-3">

            {{-- Summary Cards --}}
            <div class="col-md-4">
                <div class="card card-md">
                    <div class="card-body text-center">
                        <h3>Total Sales</h3>
                        <h2 class="text-success">{{ number_format($sales, 2) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-md">
                    <div class="card-body text-center">
                        <h3>COGS</h3>
                        <h2 class="text-danger">{{ number_format($cogs, 2) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-md">
                    <div class="card-body text-center">
                        <h3>Net Profit</h3>
                        <h2 class="text-primary">{{ number_format($profit, 2) }}</h2>
                    </div>
                </div>
            </div>

            {{-- Export Buttons --}}
            <div class="col-12 mt-3">
                <a href="{{ route('reports.pl.excel') }}" class="btn btn-success">Download Excel</a>
                <a href="{{ route('reports.pl.pdf') }}" class="btn btn-danger">Download PDF</a>
            </div>

            {{-- Chart --}}
              <div class="col-12 mt-4">
                <div class="card card-md">
                    <div class="card-body text-center">
                    <form method="GET" action="" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label> Filter Type</label>
                                <select name="filter_type" class="form-control" onchange="this.form.submit()">
                                    <option value="monthly" {{ $filterType == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="daily" {{ $filterType == 'daily' ? 'selected' : '' }}>Daily Range</option>
                                    <option value="yearly" {{ $filterType == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                            </div>

                            @if($filterType == 'daily')
                            <div class="col-md-3">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                            </div>

                            <div class="col-md-3">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                            </div>
                            @endif

                            @if($filterType == 'monthly')
                            <div class="col-md-3">
                                <label>Select Month</label>
                                <input type="month" name="month" class="form-control" value="{{ $month }}" onchange="this.form.submit()">
                            </div>
                            @endif

                            @if($filterType == 'yearly')
                            <div class="col-md-3">
                                <label>Select Year</label>
                                <input type="number" name="year" min="2000" max="2100" class="form-control"
                                    value="{{ $year }}" onchange="this.form.submit()">
                            </div>
                            @endif

                            <div class="col-md-3 align-self-end">
                                <button class="btn btn-primary w-100">Apply</button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Monthly Profit Trend</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="profitChart" style="height: 400px;"></canvas>
                    </div>
                </div>
            </div>

        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h3>Per-Product Profit</h3>
            </div>
            <div class="card-body">
                <canvas id="productProfitChart" height="350"></canvas>
            </div>
        </div>

    </div>
</div>

{{-- Chart JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@php
$monthlyLabels = $monthly->pluck('month');
$monthlySales  = $monthly->pluck('sales');
$monthlyCogs   = $monthlyCogs->pluck('cogs');
@endphp
<script>
   const labels = @json($monthlyLabels);
    const sales  = @json($monthlySales);
    const cogs   = @json($monthlyCogs);
    const profit = sales.map((s, i) => s - (cogs[i] ?? 0));

    new Chart(document.getElementById('profitChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Sales",
                    data: sales,
                    borderWidth: 3
                },
                {
                    label: "COGS",
                    data: cogs,
                    borderWidth: 3
                },
                {
                    label: "Profit",
                    data: profit,
                    borderWidth: 3
                }
            ]
        },
    });
</script>
<script>
var productLabels = @json($productProfit->pluck('name'));
var productProfit = @json($productProfit->pluck('profit'));

new Chart(document.getElementById('productProfitChart'), {
    type: 'bar',
    data: {
        labels: productLabels,
        datasets: [{
            label: 'Profit Per Product',
            data: productProfit,
            borderWidth: 1
        }]
    },
});
</script>

@endsection
