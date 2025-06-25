<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $this->load->view('includes/head'); ?>
  </head>
  <body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
      <?php $this->load->view('includes/sidebar'); ?>
      
      <div class="site-content">

        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="m-y-0"><?=$type?> User</h3>
          </div>
          <div class="panel-body">

            <div class="row">
              <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <form data-toggle="validator" id="inputmasks">

                  <input type="hidden" name="user_id" id="user_id" value="<?php if(!(empty($user))){echo($user->user_id);}else{echo(0);} ?>">
                  <input type="hidden" name="add_id" id="add_id" value="<?php if(!(empty($user))){echo($user->add_id);}else{echo(0);} ?>">

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">First Name</label>
                    <input type="text" pattern="^[a-zA-Z. ]+$" value="<?php if(!(empty($user))){echo($user->fname);} ?>" placeholder="First Name" id="fName" name="fName" class="form-control" data-minlength="3" data-pattern-error="Invalid First Name" data-error="Minimum of 3 characters" data-required-error="First Name is Required" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Last Name</label>
                    <input type="text" pattern="^[a-zA-Z. ]+$" value="<?php if(!(empty($user))){echo($user->lname);} ?>" placeholder="Last Name" id="lName" name="lName" class="form-control" data-minlength="3" data-pattern-error="Invalid Last Name" data-error="Minimum of 3 characters" data-required-error="Last Name is Required" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Company Name</label>
                    <input type="text" value="<?php if(!(empty($user))){echo($user->company_name);} ?>" placeholder="Company Name" id="companyName" name="companyName" class="form-control" data-minlength="3" data-error="Minimum of 3 characters" data-required-error="Company Name is Required" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-4" class="control-label">Date of Birth</label>
                    <input id="form-control-1" class="form-control" type="text" value="<?php if(!(empty($user))){echo($user->dob);} ?>" data-inputmask="'alias': 'yyyy-mm-dd'" name="dob" id="dob" data-required-error="DOB is Required" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-4" class="control-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php if(!(empty($user))){echo($user->email);} ?>" placeholder="Email" data-error="Please enter a valid email address." required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-4" class="control-label">Mobile</label>
                    <input type="number" placeholder="Mobile number" name="mobile" id="mobile" value="<?php if(!(empty($user))){echo($user->phone);} ?>" class="form-control" data-minlength="9" data-error="Mobile number is invalid" data-required-error="Mobile number is Required" required> 
                  </div>

                  <div class="form-group">
                    <label for="form-control-4" class="control-label">Address</label>
                    <textarea name="address" id="address" class="form-control" rows="3" placeholder="Address" data-plugin="autosize" data-error="Address is required." required  style="resize: none; height: 54px; overflow: hidden; overflow-wrap: break-word;"><?php if(!(empty($user))){echo($user->address);}?></textarea>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Country</label>
                    <select class="form-control" data-plugin="select2" name="country" id="country" data-placeholder="Select User Country" data-required-error="Country is Required" onchange="getRegion();" required>
                      <option></option>
                      <?php foreach ($countries as $row) {
                        $sel = '';
                        if(!(empty($user))){
                          if ($row->country_id==$user->country_id) {
                            $sel = 'selected="selected"';
                          }
                        }
                        
                      ?>
                        <option value="<?=$row->country_id?>" <?=$sel?>><?=$row->nicename?></option>
                      <?php } ?>
                    </select>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Region</label>
                    <select class="form-control" data-plugin="select2" name="region" id="region" data-placeholder="Select User Region" data-required-error="Region is Required" onchange="getCities();" required>
                      <option></option>                     

                    </select>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">City</label>
                    <select class="form-control" data-plugin="select2" name="city" id="city" data-placeholder="Select User City" data-required-error="City is Required" required>
                      <option></option>                     

                    </select>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Username</label>
                    <input type="text" placeholder="Username" value="<?php if(!(empty($user))){echo($user->username);}?>" data-remote="<?=base_url()?><?php if(empty($user)){echo 'checkfields';}else{echo 'checkDBfieldOpt';}?>?data=username&input=username&table=staff_users" data-remote-error="Username already Exist, Try another" data-required-error="Username is Required" class="form-control" name="username" id="username" pattern="[^\s]+" data-minlength="4"  data-error="Minimum of 4 characters without space" <?php if(empty($user)){echo'required';}else{echo "disabled='disabled'";}?>>
                        <div class="help-block with-errors"></div>  
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Password</label>
                      <input type="password" placeholder="Password" class="form-control" name="password" id="password" data-minlength="6" data-error="Minimum of 6 characters" data-required-error="Password is Required" <?php if((empty($user))){echo 'required';}?>>
                      <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">User Role</label>
                    <select class="form-control" name="access_group" id="access_group" data-required-error="User Role is Required" required>
                      <option value="" selected="selected" disabled="disabled">-- User Role --</option>
                      <?php foreach ($role as $row) {
                        $sel = '';
                        if(!(empty($user))){
                          if ($row->group_id==$user->access_group) {
                            $sel = 'selected="selected"';
                          }
                        }
                      ?>
                      <option value="<?=$row->group_id?>" <?=$sel?>><?=$row->group_desc?></option>
                      <?php } ?>
                    </select>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Status</label>
                    <input type="checkbox" class="js-switch" name="status" id="status" value="<?php if(!(empty($user))){echo $user->status;}else{echo 1;}?>" data-size="small" data-color="#34a853" <?php if(!(empty($user))){if($user->status==0){echo 'checked="checked"';}}?> <?php if ($changeStatus){echo 'disabled="disabled"';} else{echo "onchange='updateUserStatus();'"; }?> >
                    <div class="help-block with-errors"></div>
                  </div>

                  <button type="submit" class="btn btn-primary btn-block" id="submitBtn">Submit</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php $this->load->view('includes/footer'); ?>

    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script type="text/javascript">
      $( document ).ready(function() {
        <?php if(!(empty($user))){?>
          getRegion(<?=$user->reg_id?>);
        <?php } ?>
      });

      $("#country,#region,#city,#visibleSites").select2();

      function updateUserStatus() {
        var status = $('#status').val();
        if (status=='1') {
          $('#status').val(0);
        }else{
          $('#status').val(1);
        }
      }

      /*function checkNic() {
        var nic = $('#nic').val();

        if (nic.length==10) {
          var lastChar = nic.substr(nic.length - 1);
          var fpart = nic.slice(0, 9);
          if (!(isNaN(fpart))&&(lastChar=='v'||lastChar=='x')) {
            var year = fpart.slice(0, 2);
            var days = fpart.slice(2, 4);
            $("#nic").parent(".form-group").removeClass( "has-error has-danger" );
            $("#nic").next('.help-block').empty();
            return true;
          }else{
            $("#nic").parent(".form-group").addClass( "has-error has-danger" );
            $("#nic").next('.help-block').append('<ul class="list-unstyled"><li>NIC Not Valid</li></ul>');
            return false;
          }

        }else if(nic.length==12){
          if (isNaN(nic)) {
            alert("12 invalid");
          }else{

          }
        }else{
          alert('invalid');
        }
      }*/

      function getRegion(selectOpt='') {
        var country = $("#country").val();
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getRegion",
          data: 'country='+country,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            $("#region").empty();
            $("#region").append("<option></option>");
            for (var i = 0; i < responsedata.length; i++) {
              if (responsedata[i].region_name!='') {
                $("#region").append($("<option></option>").attr("value",responsedata[i].reg_id).text(responsedata[i].region_name));
              }
            }
            if (selectOpt!='') {
                $("#region").val(selectOpt);
                <?php if(!(empty($user))){?>
                getCities(<?=$user->city_id?>);
                <?php } ?>
            }
            $("#region").select2({
              placeholder: "Select User Region"
            });
          },
          error: function(result) {
            alert('error');
          }
        });
      }

      function getCities(selectOpt='') {
        var region = $("#region").val();
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getCities",
          data: 'region='+region,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            $("#city").empty();
            $("#city").append("<option></option>");
            for (var i = 0; i < responsedata.length; i++) {
              if (responsedata[i].city_name!='') {
                $("#city").append($("<option></option>").attr("value",responsedata[i].city_id).text(responsedata[i].city_name));
              }
            }
            if (selectOpt!='') {
              $("#city").val(selectOpt);
              <?php if(!(empty($user))){?>
              $('#inputmasks').validator('validate');
              <?php } ?>
            }
            $("#city").select2({
              placeholder: "Select User City"
            });
          },
          error: function(result) {
            alert('error');
          }
        });
      }

      $('#inputmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#inputmasks');
          $('#status').removeAttr('disabled');
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>saveUser",
            data: $('#inputmasks').serialize(),
            success: function(result) {
              var responsedata = $.parseJSON(result);
              if(responsedata.status=='success'){
                if (responsedata.message=='update') {
                  toastr.success("User updated successfully.")
                  setTimeout(function(){
                    window.location = "<?=base_url()?>Users/index";
                  }, 500);
                }else{
                  document.getElementById('inputmasks').reset(); 
                  $('#inputmasks').find("input").val("");
                  $('#inputmasks').find("textarea").val("");
                  $('#country,#region,#city').val('').trigger('change');
                  $('#inputmasks').validator('destroy').validator();
                  toastr.success("User Added successfully.")
                }
              }else if(responsedata.status=='error'){
                toastr.error(responsedata.message)
              }else{
                toastr.error("Somthing went wrong :(")
              }
              $('#inputmasks').waitMe('hide');
            },
            error: function(result) {
              $('#inputmasks').waitMe('hide');
              toastr.error('Error :'+result)
            }
        });
      }
    });

      
    </script>

  </body>
</html>