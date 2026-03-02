<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">{{ __('Sales') }}</h3>
        </div>

        <div class="card-actions">
            <x-action.create route="{{ route('orders.create') }}" />
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <div class="d-flex">
            <div class="text-secondary">
                Show
                <div class="mx-2 d-inline-block">
                    <select wire:model.live="perPage" class="form-select form-select-sm" aria-label="result per page">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="-1">All</option>
                    </select>
                </div>
                entries
            </div>
            <div class="ms-auto text-secondary">
                Search:
                <div class="ms-2 d-inline-block">
                    <input type="text" wire:model.live="search" class="form-control form-control-sm" aria-label="Search invoice">
                </div>
            </div>
        </div>
    </div>

    <x-spinner.loading-spinner/>

    <div class="table-responsive">
        <table wire:loading.remove class="table table-bordered card-table table-vcenter text-nowrap datatable">
            <thead class="thead-light">
                <tr>
                    <th class="align-middle text-center w-1">{{ __('No.') }}</th>
                    <th class="align-middle text-center">
                        <a wire:click.prevent="sortBy('invoice_no')" href="#" role="button">
                            {{ __('Invoice No.') }}
                            @include('inclues._sort-icon', ['field' => 'invoice_no'])
                        </a>
                    </th>
                    <th class="align-middle text-center">
                        <a wire:click.prevent="sortBy('customer_id')" href="#" role="button">
                            {{ __('Customer') }}
                            @include('inclues._sort-icon', ['field' => 'customer_id'])
                        </a>
                    </th>
                    <th class="align-middle text-center">{{ __('Contact') }}</th>
                    <th class="align-middle text-center">{{ __('Location') }}</th>
                    <th class="align-middle text-center">
                        <a wire:click.prevent="sortBy('order_date')" href="#" role="button">
                            {{ __('Date') }}
                            @include('inclues._sort-icon', ['field' => 'order_date'])
                        </a>
                    </th>
                    <th class="align-middle text-center">
                        <a wire:click.prevent="sortBy('payment_type')" href="#" role="button">
                            {{ __('Payment Type') }}
                            @include('inclues._sort-icon', ['field' => 'payment_type'])
                        </a>
                    </th>
                    <th class="align-middle text-center">{{ __('Total') }}</th>
                    <th class="align-middle text-center">{{ __('Payed Amount') }}</th>
                    <th class="align-middle text-center">{{ __('Due') }}</th>
                    <th class="align-middle text-center">{{ __('Quantity') }}</th>
                    <th class="align-middle text-center">{{ __('Added By') }}</th>
                    <th class="align-middle text-center">{{ __('Note') }}</th>
                    <th class="align-middle text-center">
                        <a wire:click.prevent="sortBy('order_status')" href="#" role="button">
                            {{ __('Status') }}
                            @include('inclues._sort-icon', ['field' => 'order_status'])
                        </a>
                    </th>
                    <th class="align-middle text-center">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td class="align-middle text-center">{{ $loop->iteration }}</td>
                    <td class="align-middle text-center">{{ $order->invoice_no }}</td>
                    <td class="align-middle">{{ $order->customer->name }}</td>
                    <td class="align-middle text-center">{{ $order->customer->phone }}</td>
                    <td class="align-middle text-center">{{ $order->customer->address }}</td>
                    <td class="align-middle text-center"> {{ \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}</td>
                    <td class="align-middle text-center">{{ $order->payment_type }}</td>
                    <td class="align-middle text-center">{{ Number::currency($order->total, 'PHP') }}</td>
                    <td class="align-middle text-center">{{ Number::currency($order->total_paid, 'PHP') }}</td>
                    <td class="align-middle text-center">{{ Number::currency($order->due, 'PHP') }}</td>
                    <td class="align-middle text-center">{{ $order->total_items }}</td>
                    <td class="align-middle text-center">{{ $order->added_by }}</td>
                    <td class="align-middle text-center">{{ $order->note }}</td>
                    <td class="align-middle text-center">
                        <x-status dot color="{{ $order->order_status === \App\Enums\OrderStatus::COMPLETE ? 'green' : 'orange' }}" class="text-uppercase">
                            {{ $order->order_status->label() }}
                        </x-status>
                    </td>
                    <td class="align-middle text-center" style="width: 5%">
                        <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                        <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                         @if($order->order_status === \App\Enums\OrderStatus::COMPLETE)
                            <a href="{{ route('orders.return.create', $order) }}"
                            class="btn btn-danger"><x-icon.return />
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="align-middle text-center" colspan="15">No results found</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex align-items-center">
        @if ($orders->isEmpty())
            <p class="m-0 text-secondary">
                Showing 0 to 0 of 0 entries
            </p>
        @endif
        <p class="m-0 text-secondary">
            Showing <span>{{ $orders->firstItem() }}</span> to <span>{{ $orders->lastItem() }}</span> of <span>{{ $orders->total() }}</span> entries
        </p>

        <ul class="pagination m-0 ms-auto">
            {{ $orders->links() }}
        </ul>
    </div>
</div>
