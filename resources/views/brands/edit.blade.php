@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        {{ __('Brand Details') }}
                    </h3>
                </div>

                <div class="card-actions">
                    <x-action.close route="{{ route('brands.index') }}" />
                </div>
            </div>

            <form action="{{ route('brands.update', $brand) }}" method="POST">
                @csrf
                @method('put')

                <div class="card-body">
                    <x-input
                        label="{{ __('Brand Name') }}"
                        id="name"
                        name="name"
                        :value="old('name', $brand->name)"
                        required
                    />

                    <x-input
                        label="{{ __('Note') }}"
                        id="note"
                        name="note"
                        :value="old('note', $brand->note)"
                        required
                    />
                </div>

                <div class="card-footer text-end">
                    <x-button.save type="submit">
                        {{ __('Update') }}
                    </x-button.save>

                    <x-button.back route="{{ route('brands.index') }}">
                        {{ __('Cancel') }}
                    </x-button.back>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
