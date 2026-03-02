@props([
    'route'
])

<form action="{{ $route }}" method="POST" class="d-inline-block delete-form">
    @csrf
    @method('delete')
    <x-button type="submit" {{ $attributes->class(['btn btn-danger']) }}>
        <x-icon.trash/>
        {{ $slot }}
    </x-button>
</form>

<script>
document.querySelectorAll('.delete-form').forEach(form => {
    const button = form.querySelector('button'); // the button inside the form
    button.addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // submit the form if confirmed
            }
        });
    });
});
</script>
