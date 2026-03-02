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

        <h2 class="page-title">Stock Report</h2>
<br>
        {{-- Export Buttons --}}
        <div class="mb-3">
            <form method="POST" action="{{ route('reports.stock.excel') }}" class="d-inline">
                @csrf
                <button class="btn btn-success">Export Excel</button>
            </form>

            <form method="POST" action="{{ route('reports.stock.pdf') }}" class="d-inline">
                @csrf
                <button class="btn btn-danger">Export PDF</button>
            </form>
        </div>

        <div class="white-box table-responsive">
            <table class="table table-bordered card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Category</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Alert</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($products as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->code }}</td>
                        <td>{{ $p->category->name ?? '-' }}</td>
                        <td class="text-center">{{ $p->quantity }}</td>

                        <td class="text-center 
                            @if($p->quantity <= $p->quantity_alert)
                                bg-danger text-white
                            @elseif($p->quantity - $p->quantity_alert <= 2)
                                bg-warning
                            @endif
                        ">
                            {{ $p->quantity_alert }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Chart --}}
        <div class="mt-5">
            <h4>Stock Level Chart</h4>
            <canvas id="stockChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('stockChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($products->pluck('name')) !!},
        datasets: [{
            label: 'Quantity',
            data: {!! json_encode($products->pluck('quantity')) !!},
        }]
    },
});
</script>
@endsection
