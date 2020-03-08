@if(session()->has('success'))
    <input id="message" value="{{session()->get('success')}}" style="display: none">
    <script type="text/javascript">
        const message = document.getElementById('message').value;
        $.notify(message, "success");
    </script>
@elseif(session()->has('error'))
    <input id="message" value="{{session()->get('error')}}" style="display: none">
    <script type="text/javascript">
        const message = document.getElementById('message').value;
        $.notify(message, "error");
    </script>
@endif
