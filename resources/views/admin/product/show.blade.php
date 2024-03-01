@extends('layouts.backend.master')
@section('title','Posts - Details')
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
                            <h3 class="card-title">Post Details</h3>
                            <div class="card-tools">
                                <a href="{{route('posts.index')}}"> <button type="button" class="btn btn-primary btn-sm"><i data-feather="plus"></i> Back</button></a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @include('error-message')
                            <div class="col-xl-12 col-lg-8 col-md-7">
                                <div class="card user-card">
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-xl-6 col-lg-12 mt-2 mt-xl-0">
                                                <div class="user-info-wrapper">
                                                    <div class="d-flex flex-wrap">
                                                        <div class="user-info-title">
                                                            <!-- <i data-feather="aperture" class="mr-1"></i> -->
                                                            <i class="fa fa-circle mr-1" aria-hidden="true"></i>
                                                            <span class="card-text user-info-title font-weight-bold mb-0">Title:</span>
                                                        </div>
                                                        <p class="card-text mb-0 ml-2">{{isset($data->title) ? $data->title : '' }}</p>
                                                    </div>
                                                    <div class="d-flex flex-wrap my-50">
                                                        <div class="user-info-title">
                                                            <!-- <i data-feather="codesandbox" class="mr-1"></i> -->
                                                            <i class="fa fa-circle mr-1" aria-hidden="true"></i>
                                                            <span class="card-text user-info-title font-weight-bold mb-0">Content</span>
                                                        </div>
                                                        <p class="card-text mb-0 ml-2">{{isset($data->content) ? $data->content : '' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
