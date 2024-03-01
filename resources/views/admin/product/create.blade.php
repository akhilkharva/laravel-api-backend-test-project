@extends('layouts.backend.master')
@section('title','Posts')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Posts</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('posts.index')}}">Posts</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Post Create</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @include('error-message')
                            <div class="tab-content">
                                <!-- Account Tab starts -->
                                <div class="tab-pane active" id="posts" aria-labelledby="posts-tab" role="tabpanel">
                                    <!-- users edit account form start -->
                                    {!! Form::open(['route' => 'posts.store','id'=>'createform','name'=>'createform','enctype'=>'multipart/form-data']) !!}
                                    @include('admin.product.common')
                                    {!! Form::close() !!}
                                    <!-- users edit account form ends -->
                                </div>
                                <!-- Account Tab ends -->
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('js')
    <script>
        $("#createform").validate({
            ignore: [],
            rules: {
                title: {
                    required: true,
                },
                content: {
                    required: true,
                },
            },
            submitHandler: function (form) {
                if ($("#createform").validate().checkForm()) {
                    $(".submitbutton").addClass("disabled");
                    form.submit();
                }
            },
        });
    </script>
@endsection
