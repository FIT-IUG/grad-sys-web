<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="{{route('firebase.store')}}" method="post">
    @csrf
    <label>Name
        <input type="text" name="name">
    </label>
{{--    <label for="">--}}
{{--        age--}}
{{--        <input type="text" name="age">--}}
{{--    </label>--}}
    <input type="submit" value="Submit">
</form>
</body>
</html>
