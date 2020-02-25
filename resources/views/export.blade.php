<form action="{{route('exportStudent')}}" method="post" enctype="multipart/form-data">
    @csrf
    <label for="">
        Select file
        <input type="file" name="students" id="">
    </label><br>
    <input type="submit" value="Export">
</form>
