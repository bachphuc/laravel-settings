@extends($layout) @section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<a href="{{route($prefixName . 'settings.create')}}" class="btn btn-default">@lang('settings::lang.create_new_setting')</a>
			{{-- <a href="{{route('settings.import')}}" class="btn btn-success">@lang('lang.import_setting')</a>
            @if($empty_setting)
            <a href="{{route('settings.clone')}}" class="btn btn-success">@lang('lang.clone_setting')</a>
            @endif --}}
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<form method="POST" action="{{url('admin/update-settings')}}" enctype="multipart/form-data">
				@foreach($groups as $group)
				<div class="card">
					<div class="card-header" data-background-color="{{$group['color']}}">
						<h4 class="title">{{$group['title']}}</h4>
					</div>
					<div class="card-content">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="row">
							@foreach($group['settings'] as $item)
							<div class="col-md-12">
                                @if($item->setting_type == 'text')
                                {{-- setting type text --}}
								<div class="form-group">
									<label class="control-label">{{ucfirst($item->title)}} ({{$item->setting_key}})</label>
									<input type="text" name="{{$item->getFormSettingKey()}}" class="form-control" value="{{$item->setting_value}}">
                                </div>
                                @elseif($item->setting_type == 'text_editor')
                                {{-- setting type text --}}
								<div class="form-group">
									<label class="control-label">{{ucfirst($item->title)}} ({{$item->setting_key}})</label>
                                    <div>
                                        <textarea class="tinymce-editor" name="{{$item->getFormSettingKey()}}">{{$item->setting_value}}</textarea>
                                    </div>
								</div>
                                @elseif($item->setting_type == 'select')
                                {{-- setting type select --}}
								<div class="form-group">
									<label class="control-label">{{ucfirst($item->title)}} ({{$item->setting_key}})</label>
									<select class="form-control" name="{{$item->getFormSettingKey()}}">
										@foreach($item->options as $option)
										<option value="{{$option->option_value}}" {{$option->option_value == $item->setting_value ? 'selected' : ''}}>{{$option->option_title}}</option>
										@endforeach
									</select>
                                </div>
                                @elseif($item->setting_type == 'image')
                                {{-- setting type image --}}
                                <div class="form-group">
                                    @php
                                        $name = $item->setting_key;
                                        $value = $item->setting_value;
                                    @endphp
                                    <label class="control-label">{{ucfirst($item->title)}} ({{$item->setting_key}})</label>
                                    <div class="form-upload-input">
                                        <div>
                                            <img id="preview_image{{isset($name) ? '_' . $name : ''}}" src="{{isset($value) && !empty($value) ? asset($value) : asset('images/default_image.svg')}}" />
                                            <input onchange="readURL(this, '#preview_image{{isset($name) ? '_' . $name : ''}}')" accept="image/*" type="file" name="{{isset($name) ? $name : 'image'}}" />
                                        </div>
                                    </div>
                                </div>
								@endif
							</div>
							@endforeach
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				@endforeach
				<div class="card">
					<div class="card-content">
						<button type="submit" class="btn btn-primary pull-right">Submit</button>
						{{-- <a href="{{route('settings.export')}}" class="btn btn-success pull-right">Export Settins</a> --}}
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection