@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">

        <h2 class="page-title row md-2 text-white">Gross Profit Report</h2>

        <div class="row row-cards mt-3">

            {{-- Summary Cards --}}
            <div class="col-md-3">
                <div class="card card-md">
                    <div class="card-body text-center">
                        <h3>Total Cost</h3>
                        <h2 class="text-danger">{{ number_format($totalCost, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-md">
                    <div class="card-body text-center">
                        <h3>Total Net Cost</h3>
                        <h2 class="text-warning">{{ number_format($totalNetCost, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-md">
                    <div class="card-body text-center">
                        <h3>Total Sales</h3>
                        <h2 class="text-success">{{ number_format($totalSales, 2) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-md">
                    <div class="card-body text-center">
                        <h3>Gross Profit</h3>
                        <h2 class="text-primary">{{ number_format($totalGrossProfit, 2) }}</h2>
                    </div>
                </div>
            </div>

            {{-- Export Buttons --}}
            <div class="col-12 mt-3">
                <a href="{{ route('reports.gross_profit.excel', [
                    'filter_type' => $filterType,
                    'start_date'  => $startDate,
                    'end_date'    => $endDate,
                    'month'       => $month,
                    'year'        => $year
                ]) }}" class="btn btn-success">Download Excel</a>

                <a href="{{ route('reports.gross_profit.pdf', [
                    'filter_type' => $filterType,
                    'start_date'  => $startDate,
                    'end_date'    => $endDate,
                    'month'       => $month,
                    'year'        => $year
                ]) }}" class="btn btn-danger">Download PDF</a>
            </div>

            {{-- Filter Form --}}
            <div class="col-12 mt-4">
                <div class="card card-md">
                    <div class="card-body text-center">
                        <form method="GET" action="" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label>Filter Type</label>
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
            </div>

            {{-- Monthly Chart --}}
            <div class="col-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Monthly Sales & Gross Profit Trend</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="grossProfitChart" style="height: 400px;"></canvas>
                    </div>
                </div>
            </div>

        </div>

        {{-- Per Product Chart --}}
        <div class="card mt-4">
            <div class="card-header">
                <h3>Per-Product Gross Profit</h3>
            </div>
            <div class="card-body">
                <canvas id="productGrossProfitChart" height="350"></canvas>
            </div>
        </div>

    </div>
</div>

{{-- Chart JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
var labels = @json($monthlyLabels);
var monthlyCost = @json($monthlyCost);
var monthlySales = @json($monthlySales);
var monthlyGrossProfit = @json($monthlyGrossProfit);

new Chart(document.getElementById('grossProfitChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            { label: "Cost", data: monthlyCost, borderWidth: 3, borderColor: "#dc3545", fill: false },
            { label: "Sales", data: monthlySales, borderWidth: 3, borderColor: "#28a745", fill: false },
            { label: "Gross Profit", data: monthlyGrossProfit, borderWidth: 3, borderColor: "#007bff", fill: false }
        ]
    },
});
</script>

<script>
var productLabels = @json($grossProfitReport->pluck('name'));
var productGrossProfit = @json($grossProfitReport->pluck('gross_profit'));

new Chart(document.getElementById('productGrossProfitChart'), {
    type: 'bar',
    data: {
        labels: productLabels,
        datasets: [{
            label: 'Gross Profit Per Product',
            data: productGrossProfit,
            borderWidth: 1
        }]
    },
});
</script>

@endsection
