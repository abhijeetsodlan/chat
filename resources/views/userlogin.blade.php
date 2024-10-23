<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatApp - Login & Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href={{asset('css/style.css') }}>
</head>
<body>

<div class="container-fluid">
    <!-- Left Section -->
    <div class="left-section">
        <h1>Application Login Page</h1>
        <p>Login or register from here to access.</p>
    </div>

    <!-- Right Section -->
    <div class="right-section">
        <div class="form-container">
            <!-- Toggle Buttons -->
            <div class="toggle-buttons">
                <button id="toggleLogin" class="btn-toggle active-toggle">Login</button>
                <button id="toggleRegister" class="btn-toggle">Register</button>
            </div>

            <!-- Login Form -->
            <div id="loginForm">
                <h2>Login</h2>
                <form method="POST" action="{{asset('userlogging')}}" id="loginform">
                    @csrf
                    <div class="form-group">
                        <label for="login-username">User Name</label>
                        <input type="text" id="login-username" name="username" placeholder="User Name">
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" placeholder="Password">
                    </div>
                    <button type="submit" class="btn-submit">Login</button>
                </form>
            </div>

            <!-- Register Form -->
            <div id="registerForm">
                <h2>Register</h2>
                <form method="POST" id="registerform" action="{{asset('userRegister')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="register-profile">Profile Picture</label>
                        <input type="file" id="register-profile" name="profile" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="register-username">User Name</label>
                        <input type="text" id="register-username" name="username" placeholder="User Name">
                    </div>
                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="register-confirm-password">Confirm Password</label>
                        <input type="password" id="register-confirm-password" name="password_confirmation" placeholder="Confirm Password">
                    </div>
                    <button type="submit" class="btn-submit">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
       $(document).ready(function(){
        $("#registerform").submit(function(e){
            e.preventDefault();
            let form = $("#registerform")[0];
            let formdata = new FormData(form);
            $.ajax({
                url:"{{asset('userRegister')}}",
                type:"POST",
                data:formdata,
                processData: false, 
                contentType: false,
                success:function(data){
                    alert(data.res);
                },
                error:function(err){
                    alert(err.responseText);
                }
            })
        })
       })
    </script>

    <script>
    document.getElementById('toggleRegister').addEventListener('click', function() {
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('registerForm').style.display = 'block';
        document.getElementById('toggleLogin').classList.remove('active-toggle');
        document.getElementById('toggleRegister').classList.add('active-toggle');
    });

    document.getElementById('toggleLogin').addEventListener('click', function() {
        document.getElementById('registerForm').style.display = 'none';
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('toggleRegister').classList.remove('active-toggle');
        document.getElementById('toggleLogin').classList.add('active-toggle');
    });
     </script>

     <script>
        $("#loginform").submit(function(e){
            e.preventDefault();
            let form = $("#loginform")[0];
            let formdata = new FormData(form);
            $.ajax({
                url:"{{asset('userlogging')}}",
                type:"POST",
                data:formdata,
                processData: false, 
                contentType: false,
                success:function(data){
                    // alert(data.res);
                    window.location.href = '/';
                },
                error:function(err){
                    alert(err.responseText);
                }
            });

        })
     </script>

</body>
</html>
