@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Edit Supplier') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $supplier])
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">
                            {{ __('Profile Image') }}
                        </h3>

                        <img
                            class="img-account-profile mb-2"
                            src="{{ $supplier->photo ? asset('storage/suppliers/' . $supplier->photo) : asset('assets/img/demo/user-placeholder.svg') }}"
                            id="image-preview"
                        />
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">
                                {{ __('Supplier Details') }}
                            </h3>
                        </div>

                        <div class="card-actions">
                            <x-action.close route="{{ route('suppliers.index') }}" />
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td><strong>{{ $supplier->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Email address</td>
                                    <td><strong>{{ $supplier->email }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Phone number</td>
                                    <td><strong>{{ $supplier->phone }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td><strong>{{ $supplier->address }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Shop name</td>
                                    <td><strong>{{ $supplier->shopname }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Type</td>
                                    <td><strong>{{ $supplier->type?->label() ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Account holder</td>
                                    <td><strong>{{ $supplier->account_holder }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Account number</td>
                                    <td><strong>{{ $supplier->account_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Bank name</td>
                                    <td><strong>{{ $supplier->bank_name }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer text-end">
                        <x-button.edit route="{{ route('suppliers.edit', $supplier) }}">
                            {{ __('Edit') }}
                        </x-button.edit>
                        <x-button.back route="{{ route('suppliers.index') }}">
                            {{ __('Back') }}
                        </x-button.back>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
