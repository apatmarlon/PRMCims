@props([
    'route',       // required
    'disabled' => false, // optional
    'confirmTitle' => 'Confirm Return?', 
    'confirmText' => 'Returned items will be added back to inventory.',
    'confirmButton' => 'Yes, confirm return',
    'cancelButton' => 'Cancel',
])

@php
    $btnClass = $disabled ? 'btn btn-danger disabled' : 'btn btn-danger';
    $dataConfirm = $disabled ? '' : 'data-return-confirm="true"';
@endphp

<form action="{{ $route }}" method="POST" class="d-inline-block return-form">
    @csrf
    <x-button type="submit" class="{{ $btnClass }}" {!! $dataConfirm !!}>
        {{ $slot ?? 'Confirm Return' }}
    </x-button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.return-form').forEach(function(form) {
        const button = form.querySelector('button[type="submit"]');
        if (!button || button.classList.contains('disabled')) return;

        button.addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: @json($confirmTitle),
                text: @json($confirmText),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: @json($confirmButton),
                cancelButtonText: @json($cancelButton)
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
