<div class="card">

    <!-- HEADER -->
    <div class="card-header">
        <h3 class="card-title">Sales Returns</h3>
        <div class="card-actions">
            <a href="{{ route('orders.index') }}" class="btn btn-primary">
                Back to Sales
            </a>
        </div>
    </div>

    <!-- SEARCH + PER PAGE -->
    <div class="card-body border-bottom py-3">
        <div class="d-flex">
            <div class="text-secondary">
                Show
                <div class="mx-2 d-inline-block">
                    <select class="form-select form-select-sm"
                            wire:model.live="perPage">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                    </select>
                </div>
                entries
            </div>

            <div class="ms-auto text-secondary">
                Search:
                <div class="ms-2 d-inline-block">
                    <input type="text"
                           class="form-control form-control-sm"
                           placeholder="Search..."
                           wire:model.live.debounce.500ms="search">
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table table-bordered card-table table-vcenter text-nowrap">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Return No.</th>
                    <th class="text-center">Invoice No.</th>
                    <th>Customer</th>
                    <th class="text-center">Contact</th>
                    <th class="text-center">Return Date</th>
                    <th>Reason</th>
                    <th class="text-center">Total Refund</th>
                    <th class="text-center">Processed By</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $return->id }}</td>
                        <td class="text-center">{{ $return->order->invoice_no }}</td>
                        <td>{{ $return->customer->name }}</td>
                        <td class="text-center">{{ $return->customer->phone }}</td>
                        <td class="text-center">
                            {{ $return->created_at->format('d-m-Y') }}
                        </td>
                        <td>{{ $return->reason }}</td>
                        <td class="text-center">
                            {{ number_format($return->total_refund, 2) }}
                        </td>
                        <td class="text-center">{{ $return->processed_by }}</td>
                        <td class="text-center">
                            <a href="{{ route('orders.return.show', $return) }}"
                               class="btn btn-info btn-sm">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">
                            No results found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Showing {{ $returns->firstItem() ?? 0 }}
            to {{ $returns->lastItem() ?? 0 }}
            of {{ $returns->total() }} entries
        </p>

        <div class="ms-auto">
            {{ $returns->links() }}
        </div>
    </div>

</div>
