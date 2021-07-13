@extends('app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <button type="button" id="logout" class="btn btn-danger mt-2">Logout</button>
            </div>
        </div>
        <div class="row">
            <form class="p-5">
                @csrf
                @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="text" id="id" value="{{$id}}" hidden>
                    <input type="email" class="form-control" id="email" name="email" value="{{$email}}"
                           placeholder="Enter email" required>
                    <div class="alert alert-danger mt-1 mb-1" id="alert-danger" style="display: none"></div>
                    <div class="alert alert-success mt-1 mb-1" id="alert-success" style="display: none"></div>
                </div>
                <button type="button" id="submitForm" class="btn btn-primary mt-2">Update Email</button>
            </form>

        </div>
    </div>
    {{--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>--}}

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });
            $('#submitForm').click(function () {

                let id    = $('#id').val();
                let email = $('#email').val();
                $.ajax({
                    type: "POST",
                    url: "/update",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        "email":email,
                        'id':id
                    },
                    success: function (response) {
                        if (response.code === 200) {
                            $('#alert-success').text(' email successfully updated').css('display', 'block');
                            $('#alert-danger').css('display', 'none');

                        }
                        if (response.code === 500) {
                            $('#alert-success').css('display', 'none');
                            $('#alert-danger').text(response.message).css('display', 'block');

                        }
                    },
                    error: function (errors) {
                    if (errors.status === 422) {
                        const obj = JSON.parse(errors.responseText);
                            $('#alert-danger').text(obj.errors.email[0]).css('display', 'block');
                        $('#alert-success').css('display', 'none');

                    }

                    }
                });
            });
            $('#logout').click(function () {

                let id    = $('#id').val();
                $.ajax({
                    type: "POST",
                    url: "/logout",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        'id':id
                    },
                    success: function (response) {
                        if (response.code === 200) {
                            window.location = '/login'
                        }
                    },
                    error: function (errors) {
                    }
                });
            });
        });
    </script>
@endsection