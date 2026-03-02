<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">
                {{ __('List Purchases') }}
            </h3>
        </div>

        <div class="card-actions">
                <x-button.excel 
                    class="btn-icon"
                    route="{{ route('purchases.export') }}"
                    title="Download Excel"
                />
            <x-action.create route="{{ route('purchases.create') }}" />
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
                    <th class="align-middle text-center w-1">
                        {{ __('No.') }}
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('purchase_no')" href="#" role="button">
                            {{ __('Purchase No.') }}
                            @include('inclues._sort-icon', ['field' => 'purchase_no'])
                        </a>
                    </th>
                     <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('date')" href="#" role="button">
                            {{ __('Date') }}
                            @include('inclues._sort-icon', ['field' => 'date'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('supplier_id')" href="#" role="button">
                            {{ __('Supplier') }}
                            @include('inclues._sort-icon', ['field' => 'supplier_id'])
                        </a>
                    </th>
                   <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('supplier_id')" href="#" role="button">
                            {{ __('Supplier Address') }}
                            @include('inclues._sort-icon', ['field' => 'supplier_id'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('total_amount')" href="#" role="button">
                            {{ __('Total') }}
                            @include('inclues._sort-icon', ['field' => 'total_amount'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('payment_status')" href="#" role="button">
                            {{ __('Payment Status') }}
                            @include('inclues._sort-icon', ['field' => 'payment_status'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('purchase_status')" href="#" role="button">
                            {{ __('Purchase Status') }}
                            @include('inclues._sort-icon', ['field' => 'purchase_status'])
                        </a>
                    </th>
                      <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('created_by')" href="#" role="button">
                            {{ __('Added By') }}
                            @include('inclues._sort-icon', ['field' => 'created_by'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        {{ __('Action') }}
                    </th>
                </tr>
            </thead>
            <tbody>
            @forelse ($purchases as $purchase)
                <tr>
                    <td class="align-middle text-center">
                        {{ $loop->iteration }}
                    </td>
                    <td class="align-middle text-center">
                        {{ $purchase->purchase_no }}
                    </td>
                    <td class="align-middle text-center">
                        {{ $purchase->date->format('d-m-Y') }}
                    </td>
                    <td class="align-middle">
                        {{ $purchase->supplier?->name ?? '-' }}
                    </td>
                    <td class="align-middle">
                        {{ $purchase->supplier?->address ?? '-' }}
                    </td>
                    <td class="align-middle text-center">
                        {{ Number::currency($purchase->total_amount, 'PHP') }}
                    </td>
                    @php
                        // compute due date based on payterm (in days)
                        $dueDate = \Carbon\Carbon::parse($purchase->date)->addDays($purchase->payterm);
                        $today = \Carbon\Carbon::today();
                    @endphp

                    <td class="align-middle text-center">

                        {{-- PAID --}}
                        @if ($purchase->payment_status == 0)
                            <span class="badge bg-green text-white text-uppercase">
                                PAID
                            </span>

                        {{-- UNPAID (Check if DUE or OVERDUE) --}}
                        @elseif ($purchase->payment_status == 1)

                            {{-- DUE: due date is today --}}
                            @if ($dueDate->isToday())
                                <span class="badge bg-orange text-white text-uppercase">
                                    DUE
                                </span>

                            {{-- OVERDUE: due date is earlier than today --}}
                            @elseif ($dueDate->isPast())
                                <span class="badge bg-red text-white text-uppercase">
                                    OVERDUE
                                </span>

                            {{-- Still on time (not due yet) --}}
                            @else
                                <span class="badge bg-blue text-white text-uppercase">
                                   UNPAID
                                </span>
                            @endif

                        @endif
                    </td>
                    @if ($purchase->purchase_status === 1)
                        <td class="align-middle text-center">
                            <span class="badge bg-green text-white text-uppercase">
                                {{ __('RECEIVED') }}
                            </span>
                        </td>
                       
                    @else
                        <td class="align-middle text-center">
                            <span class="badge bg-secondary text-white text-uppercase">
                                {{ __('UNRECEIVED') }}
                            </span>
                        </td>
                       
                    @endif
                    <td class="align-middle">
                        {{ $purchase->createdBy->name ?? '-' }}
                    </td>
                    @if ($purchase->purchase_status === 1 AND $purchase->payment_status === 0)
                        <td class="align-middle text-center">
                            <x-button.show class="btn-icon" route="{{ route('purchases.show', $purchase) }}"/>
                        </td>
                    @else
                        <td class="align-middle text-center" style="width: 5%">
                            <x-button.show class="btn-icon" route="{{ route('purchases.show', $purchase) }}"/>
                             <x-button.edit class="btn-icon" route="{{ route('purchases.edit', $purchase) }}"/>
                        </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td class="align-middle text-center" colspan="7">
                        No results found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex align-items-center">
        @if ($purchases->isEmpty())
            <p class="m-0 text-secondary">
                Showing 0 to 0 of 0 entries
            </p>
        @endif
        <p class="m-0 text-secondary">
            Showing <span>{{ $purchases->firstItem() }}</span>
            to <span>{{ $purchases->lastItem() }}</span> of <span>{{ $purchases->total() }}</span> entries
        </p>

        <ul class="pagination m-0 ms-auto">
        {{ $purchases->links() }}
        </ul>
    </div>
</div>
