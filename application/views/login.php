<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="">
  <title>Fancy Point | Staff Portal</title>
  <link rel="icon" type="image/png" href="<?=base_url()?>assets/img/favicon.png">
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500,700" rel="stylesheet">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" href="<?=base_url()?>assets/css/vendor.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/css/cosmos.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/css/application.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/css/waitMe.css">
</head>
  <body class="authentication-body">
    <div class="container-fluid">
      <div class="authentication-header m-b-30">
        <div class="clearfix">
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4 col-sm-offset-4">
          <div class="authentication-content m-b-30">
            <h3 class="m-t-0 m-b-30 text-center">Back Office Login!</h3>
            <form id="loginForm">
              <div class="form-group">
                <label for="form-control-1">User Name</label>
                <input class="form-control" type="text" placeholder="Username" name="username" id="username" required="required">
                <span id="usernameError" class="error"></span>
              </div>
              <div class="form-group">
                <label for="form-control-2">Password</label>
                <input class="form-control" type="password" placeholder="Password" name="password" id="password" required="required">
                <span id="passwordError" class="error"></span>
              </div>
              <button type="submit" class="btn btn-info btn-block">Submit</button>
            </form>
          </div>
        </div>
      </div>
      <div class="authentication-footer">
        <span class="text-muted">Need help? Contact us info@fancypoint.com</span>
      </div>
    </div>
    <script src="<?=base_url()?>assets/js/vendor.js"></script>
    <script src="<?=base_url()?>assets/js/cosmos.js"></script>
    <script src="<?=base_url()?>assets/js/application.js"></script>  
    <script src="<?=base_url()?>assets/js/waitMe.js"></script>
    <script type="text/javascript">                  
      $('#loginForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "<?=base_url()?>sign-in",
            data: $('#loginForm').serialize(),
            success: function(result) {
                var responsedata = $.parseJSON(result);
                if(responsedata.status=='success'){
                    window.location.href = "<?=base_url()?>"+responsedata.message;
                }else{
                    document.getElementById('loginForm').reset(); 
                    $('#loginForm').find("input").val("");
                    toastr["error"](responsedata.message);
                }
            },
            error: function(result) {
                toastr["error"](result);
            }
        });
      });
    </script>
  </body>
</html>
