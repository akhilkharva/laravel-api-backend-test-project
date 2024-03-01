<?php use Request as Input; ?>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="form-label" for="basic-default-name">Title</label>
            {!! Form::text('title',Input::old('title'), ['class' => 'form-control','id'=>"name",'placeholder'=>'Enter post title']) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="form-label" for="basic-default-name">Content</label>
            {!! Form::text('content',Input::old('content'), ['class' => 'form-control','id'=>"content",'placeholder'=>'Enter content']) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 d-flex flex-sm-row flex-column mt-2">
        @if(isset($data->id))
            <button type="submit" class="btn btn-primary btn-sm mb-1 mb-sm-0 mr-0 mr-sm-1 submitbutton" name="submit" value="Submit">Update</button>&nbsp;
        @else
            <button type="submit" class="btn btn-primary btn-sm mb-1 mb-sm-0 mr-0 mr-sm-1 submitbutton" name="submit" value="Submit">Save</button>&nbsp;
        @endif
        <a href="{{ route('posts.index') }}"><button type="button" class="btn btn-sm btn-outline-secondary">Cancel</button></a>
    </div>
</div>
