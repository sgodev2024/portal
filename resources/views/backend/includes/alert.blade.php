@if (session()->has('success') || session()->has('error') )
    <script>
        //Notify
        $.notify({
            icon: '{{ session()->has('success') ? 'fa fa-check' : 'fa fa-times' }}',
            title: '{{ isset($title) ? $title : "Thông báo" }}',
            message: '{{ session()->has('success') ? session('success') : session('error') }}',
        }, {
            type: 'secondary',
            placement: {
                from: "top",
                align: "right"
            },
            time: 1000,
        });
    </script>
@endif
