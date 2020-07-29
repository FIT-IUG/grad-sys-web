{{-- exports --}}
<div class="row">
    {{-- export users from excel file --}}
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">رفع بيانات الطلبة والمشرفين</h3>
            </div>
            <form role="form" method="POST" action="{{route('admin.exportExcelFile')}}"
                  enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="exampleInputFile"> اختر ملف</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" multiple=""
                                       class="custom-file-input inputFileHidden @error('excelFile') is-invalid @enderror"
                                       id="exampleInputFile" name="excelFile" required>
                                <label class="custom-file-label" for="exampleInputFile">اختر الملف</label>
                            </div>
                        </div>
                        @error('excelFile')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">إرسال</button>
                </div>
            </form>
        </div>
    </div>
</div>
