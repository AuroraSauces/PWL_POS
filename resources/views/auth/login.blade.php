<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Pengguna</title>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ url('/') }}" class="h1"><b>Admin</b>LTE</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <form action="{{ url('login') }}" method="POST" id="form-login">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Username">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-user"></span></div>
                        </div>
                        <small id="error-username" class="error-text text-danger"></small>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                        <small id="error-password" class="error-text text-danger"></small>
                    </div>

                    <!-- Remember Me -->
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Remember Me</label>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Sign In & Daftar -->
                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-secondary btn-block" id="btn-register">Daftar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL REGISTER -->
<div class="modal fade" id="registerModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Akun Baru</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-register">
                    @csrf
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" id="nama" name="nama" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" id="username" name="username" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Level</label>
                        <select id="level_id" name="level_id" class="form-control">
                            <option value="3">Staff</option>
                            <option value="2">Manager</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <!-- jQuery -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            // FORM LOGIN
            $("#form-login").submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ url('login') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire("Berhasil", response.message, "success")
                                .then(() => window.location = response.redirect);
                        } else {
                            Swal.fire("Error", response.message, "error");
                        }
                    }
                });
            });

            // TOMBOL DAFTAR KLIK
            $("#btn-register").click(function() {
                $("#registerModal").modal("show");
            });

            // FORM REGISTER
            $("#form-register").submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ url('register') }}", // Ganti dengan URL backend
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire("Sukses!", response.message, "success")
                                .then(() => $("#registerModal").modal("hide"));
                        } else {
                            Swal.fire("Gagal!", response.message, "error");
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
