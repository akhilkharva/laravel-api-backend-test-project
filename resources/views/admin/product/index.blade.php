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
                        <li class="breadcrumb-item active">Posts</li>
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
                            <h3 class="card-title">Post List</h3>
                            <div class="card-tools">
                                <a href="{{route('posts.create')}}"> <button type="button" class="btn btn-primary btn-sm"><i data-feather="plus"></i> Add Post</button></a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @include('error-message')
                            <table id="postTable" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Content</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
    @include('confirmalert')
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            var initTable = function () {
                var table = $('#postTable');
                // begin the first table
                table.DataTable({
                    lengthMenu: getPageLengthDatatable(),
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true,

                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    // dom: 'lfrtip',
                    order: [],
                    ajax: {
                        url: "{{route('getAllPosts')}}",
                        type: 'post',
                        data: function (data) {
                            console.log(data);
                            data.fromValues = $("#filterData").serialize();
                        },
                    },
                    columns: [
                        {data: 'title', name: 'title'},
                        {data: 'content', name: 'content'},
                        {
                            data: 'created_at', name: 'created_at',
                            render: function (data, type, row, meta) {
                                var dateWithTimezone = moment.utc(data);
                                return dateWithTimezone.format('<?php echo config('const.JsDisplayDateTimeFormatWithAMPM'); ?>');
                            }
                        },
                        {
                            data: 'updated_at', name: 'updated_at',
                            render: function (data, type, row, meta) {
                                var dateWithTimezone = moment.utc(data);
                                return dateWithTimezone.format('<?php echo config('const.JsDisplayDateTimeFormatWithAMPM'); ?>');
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            searchable: false,
                            sortable: false,
                            responsivePriority: -1
                        },

                    ],
                });
            };
            initTable();

            $("#delete-record").on("click", function () {
                var id = $("#id").val();
                $('#delete-modal').modal('hide');
                $.ajax({
                    url: baseUrl + '/admin/posts/' + id,
                    type: "DELETE",
                    dataType: 'json',
                    success: function (data) {
                        if (data == 'Error') {
                            toastr.error("Oops, There is some thing went wrong.Please try after some time.");
                        } else {
                            toastr.success('@lang('admin.recordDelete')', '@lang('admin.success')');
                            $('#postTable').DataTable().clear().destroy();
                            initTable();
                        }
                    },
                    error: function (data) {
                        toastr.error("@lang('admin.oopsError')", "@lang('admin.error')");
                    }
                });
            });

        });
    </script>
@endsection
