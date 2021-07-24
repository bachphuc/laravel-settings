@extends($layout) @section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-md-8">
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
			<form method="POST" action="{{route($prefixName . 'settings.index')}}">
				<div class="card">
					<div class="card-header" data-background-color="purple">
						<h4 class="title">@lang('settings::lang.create_new_setting')</h4>
					</div>
					<div class="card-content">
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />
						<div class="row">
							<div class="col-md-12">
								<div class="form-group label-floating">
									<label class="control-label">Setting Name</label>
									<input type="text" name="title" class="form-control" value="{{old('title')}}" required>
								</div>
							</div>
						</div>
                        <div class="row">
							<div class="col-md-12">
								<div class="form-group label-floating">
									<label class="control-label">Setting Key</label>
									<input type="text" name="setting_key" class="form-control" value="{{old('setting_key')}}">
								</div>
							</div>
						</div>
                        <div class="row">
							<div class="col-md-12">
								<div class="form-group label-floating">
									<label class="control-label">Setting Value</label>
									<input type="text" name="setting_value" class="form-control" value="{{old('setting_value')}}" required>
								</div>
							</div>
						</div>
                        <div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Setting Group</label>
									<select name="group_key" class="form-control" onchange="onGroupChange(this)">
                                        <option value="">General</option>
                                        <option value="new_group" {{old('group_key') == 'new_group' ? 'selected' : ''}}>Create new Group</option>
                                        @foreach($groups as $group)
                                        <option value="{{$group->group_key}}" {{old('group_key') == $group->group_key ? 'selected' : ''}}>{{$group->title}}</option>
                                        @endforeach
                                    </select>
								</div>
							</div>
						</div>
                        <div id="row-group-name" class="hide">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group label-floating">
                                        <label class="control-label">Group Name</label>
                                        <input type="text" name="group_name" class="form-control" value="{{old('group_name')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Group Color</label>
                                        <select name="group_color" class="form-control">
                                            @foreach($colors as $color)
                                                <option value="{{$color}}" {{old('group_color') == $color ? 'selected' : ''}}>{{$color}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Setting Type</label>
									<select name="setting_type" class="form-control" onchange="onSettingTypeChange(this)">
                                        <option value="text" {{old('setting_type') == 'text' ? 'selected' : ''}}>Text</option>
                                        <option value="select" {{old('setting_type') == 'select' ? 'selected' : ''}}>Select</option>
                                        <option value="image" {{old('setting_type') == 'image' ? 'selected' : ''}}>Image</option>
                                        <option value="text_editor" {{old('setting_type') == 'text_editor' ? 'selected' : ''}}>Text Editor</option>
                                    </select>
								</div>
							</div>
						</div>
                        <div class="row hide" id="row-setting-options">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label">Setting Options</label>
									<input placeholder="yes,no" type="text" name="setting_options" class="form-control" value="{{old('setting_options')}}">
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
<script type="text/javascript">
    const rowSettingOptions = document.getElementById('row-setting-options');
    const rowGroupName = document.getElementById('row-group-name');
    function onSettingTypeChange(ele){
        if(rowSettingOptions){
            if(ele.value === 'select'){
                rowSettingOptions.classList.remove('hide');
            }
            else{
                rowSettingOptions.classList.add('hide');
            }
        }
    }

    function onGroupChange(ele){
        if(rowGroupName){
            if(ele.value != 'new_group'){
                rowGroupName.classList.add('hide');
            }
            else{
                rowGroupName.classList.remove('hide');
            }
        }
    }
</script>
@endsection