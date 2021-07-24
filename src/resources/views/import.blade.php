@extends($layout) 

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="{{route('settings.import')}}" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-header" data-background-color="purple">
                        <h4 class="title">@lang('lang.import_setting')</h4>
                    </div>
                    <div class="card-content">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <label class="control-label">Setting File</label>
                                    <input type="file" name="file" required>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary pull-right">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection