<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">
                {{ __('Brands') }}
            </h3>
        </div>

        <div class="card-actions">
            <x-action.create route="{{ route('brands.create') }}" />
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <div class="d-flex">
            <div class="text-secondary">
                Show
                <div class="mx-2 d-inline-block">
                    <select wire:model.live="perPage"
                            class="form-select form-select-sm"
                            aria-label="result per page">
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
                    <input type="text"
                           wire:model.live="search"
                           class="form-control form-control-sm"
                           aria-label="Search brand">
                </div>
            </div>
        </div>
    </div>

    <x-spinner.loading-spinner/>

    <div class="table-responsive">
        <table wire:loading.remove
               class="table table-bordered card-table table-vcenter text-nowrap datatable">
            <thead class="thead-light">
                <tr>
                    <th class="align-middle text-center w-1">
                        {{ __('No.') }}
                    </th>

                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('name')" href="#" role="button">
                            {{ __('Name') }}
                            @include('inclues._sort-icon', ['field' => 'name'])
                        </a>
                    </th>

                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('note')" href="#" role="button">
                            {{ __('Note') }}
                            @include('inclues._sort-icon', ['field' => 'note'])
                        </a>
                    </th>

                    <th scope="col" class="align-middle text-center">
                        {{ __('Action') }}
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($brands as $brand)
                    <tr>
                        <td class="align-middle text-center">
                            {{ ($brands->currentPage() - 1) * $brands->perPage() + $loop->iteration }}
                        </td>

                        <td class="align-middle">
                            {{ $brand->name }}
                        </td>

                        <td class="align-middle">
                            {{ $brand->note ?? '-' }}
                        </td>

                        <td class="align-middle text-center" style="width: 10%">
                            <x-button.show class="btn-icon"
                                           route="{{ route('brands.show', $brand) }}"/>

                            <x-button.edit class="btn-icon"
                                           route="{{ route('brands.edit', $brand) }}"/>

                            <x-button.delete class="btn-icon"
                                             route="{{ route('brands.destroy', $brand) }}"/>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="align-middle text-center" colspan="4">
                            No results found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex align-items-center">
        @if($perPage != -1)
        <p class="m-0 text-secondary d-none d-sm-block">
            Showing <span>{{ $brands->firstItem() }}</span>
            to <span>{{ $brands->lastItem() }}</span>
            of <span>{{ $brands->total() }}</span> entries
        </p>
        @endif
        <ul class="pagination m-0 ms-auto">
            {{ $brands->links() }}
        </ul>
    </div>
</div>
