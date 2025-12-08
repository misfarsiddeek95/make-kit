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
            <h3 class="m-y-0">פרופיל</h3>
          </div>
          <div class="panel-body">

            <div class="row">
              <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <form data-toggle="validator" id="inputmasks">

                  <input type="hidden" name="user_id" id="user_id" value="<?php if(!(empty($user))){echo($user->user_id);}else{echo(0);} ?>">
                  <input type="hidden" name="add_id" id="add_id" value="<?php if(!(empty($user))){echo($user->add_id);}else{echo(0);} ?>">

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">שם פרטי</label>
                    <input type="text" pattern="^[a-zA-Z. ]+$" value="<?php if(!(empty($user))){echo($user->fname);} ?>" placeholder="שם פרטי" id="fName" name="fName" class="form-control" data-minlength="3" data-pattern-error="שם פרטי לא חוקי" data-error="מינימום 3 תווים" data-required-error="שם פרטי נדרש" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">שם משפחה</label>
                    <input type="text" pattern="^[a-zA-Z. ]+$" value="<?php if(!(empty($user))){echo($user->lname);} ?>" placeholder="שם משפחה" id="lName" name="lName" class="form-control" data-minlength="3" data-pattern-error="שם משפחה לא חוקי" data-error="מינימום 3 תווים" data-required-error="שם משפחה נדרש" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">שם חברה</label>
                    <input type="text" value="<?php if(!(empty($user))){echo($user->company_name);} ?>" placeholder="שם חברה" id="companyName" name="companyName" class="form-control" data-minlength="3" data-error="מינימום 3 תווים" data-required-error="שם חברה נדרש" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-4" class="control-label">תאריך לידה</label>
                    <input id="form-control-1" class="form-control" type="text" value="<?php if(!(empty($user))){echo($user->dob);} ?>" data-inputmask="'alias': 'yyyy-mm-dd'" name="dob" id="dob" data-required-error="תאריך לידה נדרש" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-4" class="control-label">אימייל</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php if(!(empty($user))){echo($user->email);} ?>" placeholder="אימייל" data-error="אנא הזן כתובת אימייל חוקית." disabled="disabled" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-4" class="control-label">נייד</label>
                    <input type="number" placeholder="מספר נייד" name="mobile" id="mobile" value="<?php if(!(empty($user))){echo($user->phone);} ?>" class="form-control" data-minlength="9" data-error="מספר נייד לא חוקי" data-required-error="מספר נייד נדרש" required> 
                  </div>

                  <div class="form-group">
                    <label for="form-control-4" class="control-label">כתובת</label>
                    <textarea name="address" id="address" class="form-control" rows="3" placeholder="כתובת" data-plugin="autosize" data-error="כתובת נדרשת." required  style="resize: none; height: 54px; overflow: hidden; overflow-wrap: break-word;"><?php if(!(empty($user))){echo($user->address);}?></textarea>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">מדינה</label>
                    <select class="form-control" data-plugin="select2" name="country" id="country" data-required-error="מדינה נדרשת" onchange="getRegion();" required>
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
                    <label for="form-control-3" class="control-label">אזור</label>
                    <select class="form-control" data-plugin="select2" name="region" id="region" data-required-error="אזור נדרש" onchange="getCities();" required>
                      <option></option>                     

                    </select>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">עיר</label>
                    <select class="form-control" data-plugin="select2" name="city" id="city" data-required-error="עיר נדרשת" required>
                      <option></option>                     

                    </select>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">שם משתמש</label>
                    <input type="text" placeholder="שם משתמש" value="<?php if(!(empty($user))){echo($user->username);}?>" data-remote="<?=base_url()?><?php if(empty($user)){echo 'checkfields';}else{echo 'checkDBfieldOpt';}?>?data=username&input=username&table=staff_users" data-remote-error="שם משתמש כבר קיים, נסה אחר" data-required-error="שם משתמש נדרש" class="form-control" name="username" id="username" pattern="[^\s]+" data-minlength="4"  data-error="מינימום 4 תווים ללא רווח" <?php if(empty($user)){echo'required';}else{echo "disabled='disabled'";}?>>
                        <div class="help-block with-errors"></div>  
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">סיסמה</label>
                      <input type="password" placeholder="סיסמה" class="form-control" name="password" id="password" data-minlength="6" data-error="מינימום 6 תווים" data-required-error="סיסמה נדרשת" <?php if((empty($user))){echo 'required';}?>>
                      <div class="help-block with-errors"></div>
                  </div>

                  <button type="submit" class="btn btn-primary btn-block" id="submitBtn">שלח</button>
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


      $("#country").select2({
        placeholder: "בחר מדינת משתמש"
      });

      $("#region").select2({
        placeholder: "בחר אזור משתמש"
      });

      $("#city").select2({
        placeholder: "בחר עיר משתמש"
      });

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
            $("#region").selectpicker('refresh');
            $("#region").select2({
              placeholder: "בחר אזור משתמש"
            });
          },
          error: function(result) {
            alert('שגיאה');
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
            $("#city").selectpicker('refresh');
            $("#city").select2({
              placeholder: "בחר עיר משתמש"
            });
          },
          error: function(result) {
            alert('שגיאה');
          }
        });
      }

      $('#inputmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#inputmasks');
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>UpdateProfile",
            data: $('#inputmasks').serialize(),
            success: function(result) {
              var responsedata = $.parseJSON(result);
              if(responsedata.status=='success'){
                toastr.success("פרופיל עודכן בהצלחה.")
                setTimeout(function(){
                  location.reload();
                }, 500);
              }else if(responsedata.status=='error'){
                toastr.error(responsedata.message)
              }else{
                toastr.error("משהו השתבש :(")
              }
              $('#inputmasks').waitMe('hide');
            },
            error: function(result) {
              $('#inputmasks').waitMe('hide');
              toastr.error('שגיאה: '+result)
            }
        });
      }
    });

      
    </script>

  </body>
</html>