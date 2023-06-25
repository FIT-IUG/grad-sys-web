@if(session()->has('success'))
    <input id="message" value="{{session()->get('success')}}" style="display: none">
    <script type="text/javascript">
        const message = document.getElementById('message').value;
        $.notify({
            message: message,
            target: '_blank'
        }, {
            type: "success",
            allow_dismiss: false,
            offset: 20,
            spacing: 10,
            z_index: 999999,
            delay: 5000,
            timer: 1000,
            animate: {
                enter: 'animated fadeInDown',
                exit: 'animated fadeOutUp'
            },
        });
    </script>
@elseif(session()->has('error'))
    <input id="message" value="{{session()->get('error')}}" style="display: none">
    <script type="text/javascript">
        const message = document.getElementById('message').value;
        $.notify({
            message: message,
            target: '_blank'
        }, {
            type: "danger",
            allow_dismiss: false,
            offset: 20,
            spacing: 10,
            z_index: 999999,
            delay: 5000,
            timer: 1000,
            animate: {
                enter: 'animated fadeInDown',
                exit: 'animated fadeOutUp'
            },
        });
    </script>
@endif
