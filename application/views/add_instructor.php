<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view('includes/head'); ?> 
        <link rel="stylesheet" href="<?=base_url()?>assets/css/bootstrap-datepicker.css">
    </head>
    <body class="layout layout-header-fixed layout-left-sidebar-fixed">
        <?php $this->load->view('includes/topbar'); ?>
        <div class="site-main">
            <?php $this->load->view('includes/sidebar'); ?>
            <div class="site-content"> 
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="m-y-0 d-inline"><?=$type?> Instructor</h3>
                        <a class="btn btn-outline-warning btn-pill pull-right m-y-0 d-inline" href="<?=base_url()?>ExternalUsers/instructors/128"><i class="zmdi zmdi-arrow-left"></i></a>
                    </div>
                    <div class="panel-body"> 
                        <div class="row">
                            <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                                <form data-toggle="validator" id="inputmasks">
                                <input type="hidden" name="user_id" id="user_id" value="<?php if(!(empty($user))){echo($user->user_id);}else{echo(0);} ?>" />
                                <input type="hidden" id="country" name="country" value="">
                                <input type="hidden" id="region" name="region" value="">
                                <input type="hidden" id="add_id" name="add_id" value="<?php if(!(empty($user))){echo($user->add_id);}else{echo(0);} ?>">
                                <div class="row">
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="fname" class="control-label">שם פרטי</label>
                                            <input type="text" pattern="^[a-zA-Z. ]+$" value="<?php if(!(empty($user))){echo($user->fname);} ?>" placeholder="שם פרטי" id="fname" name="fname" class="form-control" data-minlength="3" data-pattern-error="שם פרטי לא חוקי" data-error="מינימום 3 תווים" data-required-error="שם פרטי נדרש" required autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="lname" class="control-label">שם משפחה</label>
                                            <input type="text" pattern="^[a-zA-Z. ]+$" value="<?php if(!(empty($user))){echo($user->lname);} ?>" placeholder="שם משפחה" id="lname" name="lname" class="form-control"  data-pattern-error="שם משפחה לא חוקי" autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="phone" class="control-label">טלפון</label> 
                                            <input type="text" pattern="^[0-9+]+$" value="<?php if(!(empty($user))){echo($user->phone);} ?>" placeholder="טלפון"  id="phone" name="phone" class="form-control" data-pattern-error="פורמט מספר טלפון לא חוקי" required data-required-error="מספר טלפון נדרש." autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="control-label">דוא"ל</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php if(!(empty($user))){echo($user->email);} ?>" placeholder="דוא"ל" data-error="אנא הכנס כתובת דוא"ל חוקית." required data-required-error="דוא"ל נדרש" autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="form-control-4" class="control-label">כתובת</label>
                                            <textarea name="address" id="address" class="form-control" rows="3" placeholder="כתובת" data-plugin="autosize" data-error="כתובת נדרשת." required  style="resize: none; height: 54px; overflow: hidden; overflow-wrap: break-word;"><?php if(!(empty($user))){echo($user->address);}?></textarea>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="city" class="control-label">עיר</label> 
                                            <select id="city" name="city" class="form-control" style="width:100%;" data-placeholder="עיר" data-plugin="select2" data-required-error="עיר נדרשת" required>
                                                <option></option>
                                                <?php foreach ($cities as $row) { ?>
                                                <option value="<?=$row->city_id?>" country-id="<?=$row->country_id?>" region-id="<?=$row->reg_id?>"><?=$row->city_name?> [ <?=$row->city_name_hebrew?> ]</option>
                                                <?php } ?>
                                            </select> 
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="" class="control-label">מגדר</label>
                                            <div class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-outline-primary">
                                                    <input type="radio" name="p_gender" id="p_gender1" autocomplete="off" value="0"> נקבה
                                                </label>
                                                <label class="btn btn-outline-success">
                                                    <input type="radio" name="p_gender" id="p_gender2" autocomplete="off" value="1"> זכר
                                                </label>
                                                <label class="btn btn-outline-warning">
                                                    <input type="radio" name="p_gender" id="p_gender3" autocomplete="off" value="2"> אחר
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="user_pic" class="control-label">תמונת פרופיל</label>
                                            <input type="hidden" value="<?php if(!(empty($user))){if(trim($user->photo_path)!=''&&$user->photo_path!=null){echo('photos/staff/'.$user->photo_path.'-std.'.$user->extension);}else{echo('photos/default.jpg');}}else{echo('photos/default.jpg');} ?>" name="user_pic" id="user_pic">
                                            <div class="row gutter-sm">
                                                <div id="imageupdiv"></div>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <?php $exploded = explode('-',$this->uri->segment(1)); ?>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="userName" class="control-label">שם משתמש</label>
                                            <div class="row">
                                                <?php 
                                                    $disAttr = '';
                                                    $divCls = 'col-md-12';
                                                    if ($exploded[0] == 'edit') {
                                                    $disAttr = 'disabled';
                                                    $divCls = 'col-md-10';
                                                    }
                                                ?>
                                                <div class="col-sm-6 <?=$divCls?>">
                                                <input class="form-control" type="text"
                                                value="<?php if(!(empty($user))){echo($user->username);}?>"
                                                placeholder="שם משתמש" id="userName" name="username" required
                                                data-required-error="שם משתמש נדרש." <?=$disAttr?>
                                                pattern="[^\s]+" data-minlength="4"  data-error="מינימום 4 תווים ללא רווח"
                                                <?php if(empty($user)){echo'required';}else{echo "disabled='disabled'";}?> autocomplete="off"
                                                />
                                                </div>
                                            <?php if ($exploded[0] == 'edit') { ?>
                                            <div class="col-sm-6 col-md-2">
                                                <button type="button" class="btn btn-info btn-block" id="unameEdit" onclick="enableFieldEdit('1','userName','unameEdit');"><i class="zmdi zmdi-edit"></i></button>
                                            </div>
                                            <?php } ?>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                            <div id="ex_uname"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="password" class="control-label">סיסמה</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="סיסמה" data-required-error="סיסמה נדרשת" <?php if((empty($user))){echo 'required';}?>  autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block" id="submitBtn">שליחה</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->load->view('includes/footer'); ?>
        </div>
        <?php $this->load->view('includes/javascripts'); ?>
        <script src="<?=base_url()?>assets/js/bootstrap-datepicker.js"></script>
        <script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
        <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
        <script src="<?=base_url()?>assets/js/spartan-multi-image-picker.js"></script>
        <script type="text/javascript">
            $(function() {
                $('#city').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const country = selectedOption.attr('country-id');
                    const region = selectedOption.attr('region-id');

                    $('#country').val(country);
                    $('#region').val(region);
                });

                let image = ``
                <?php if(!empty($user)) { ?>
                    $('#city').val('<?=$user->city_id?>').trigger('change');
                    image = '<?=$user->photo_path?>';

                    const gender = '<?=$user->gender?>';
                    const genderInput = $(`input[name="p_gender"][value="${gender}"]`);
                    genderInput.prop('checked', true);
                    genderInput.closest('label').addClass('active');
                <?php } ?>

                if (image != null && image != '') {
                    $('label.file_upload').find('img:eq(0)').before(`<a href="javascript:removeLogo('<?=$user->user_id?>')" data-spartanindexremove="4" style="right: 0px;top: 0px;background: rgb(254, 215, 0);border-radius: 3px;width: 20px;height: 20px;line-height: 20px;text-align: center;text-decoration: none;color: rgb(49, 62, 70);position: absolute !important;" id="edit-img" class="spartan_remove_row"><i class="fa fa-times"></i></a>`)
                }
            });

            $("#imageupdiv").spartanMultiImagePicker({
                fieldName:'fileUpload',
                maxCount:1,
                rowHeight:'120px',
                maxFileSize:5500000,
                allowedExt:'jpg|jpeg|png',
                dropFileLabel:   'גרור לוגו לכאן',
                groupClassName : 'col-md-4 col-sm-4 col-xs-6',
                placeholderImage: {image:'<?=base_url();?>'+$('#user_pic').val() ,width: '60%'},
                onRenderedPreview : function(index){
                },

                onExtensionErr : function(index, file){
                    toastr["error"]('אנא הכנס רק קובץ מסוג png או jpg');
                },
                onSizeErr : function(index, file){
                    toastr["error"]('קובץ זה חורג מהגודל המקסימלי(5MB)');
                }
            });

            function enableFieldEdit(action,fieldId,myId) {
                if (action == 1) {
                    $('#'+fieldId).removeAttr('disabled');
                    $('#'+myId).attr('onclick','enableFieldEdit("0","'+fieldId+'","'+myId+'")');
                    $('#'+myId).html('<i class="zmdi zmdi-close"></i>');
                    $('#'+myId).removeClass('btn-info').addClass('btn-danger');
                }else{
                    $('#'+fieldId).attr('disabled','disabled');
                    $('#'+myId).attr('onclick','enableFieldEdit("1","'+fieldId+'","'+myId+'")');
                    $('#'+myId).html('<i class="zmdi zmdi-edit"></i>');
                    $('#'+myId).removeClass('btn-danger').addClass('btn-info');
                }
            }

            $('#userName').change(function() {
                $.post( "<?=base_url()?>check-username-exists", { username: $("#userName").val()}, function (data){
                var resp = $.parseJSON(data);  
                if (resp == true) {
                    $('#ex_uname').text('שם משתמש כבר קיים. נסה אחר.');
                    $('#ex_uname').css({"display": "inline", "color": "red"});
                }else{
                    $('#ex_uname').text('');
                    $('#ex_uname').css({"display": "none"});
                }
                });
            });


            $('#inputmasks').validator().on('submit', function (e) {
                if (!(e.isDefaultPrevented())) {
                    e.preventDefault();
                    run_waitMe('#inputmasks');
                    var formData = new FormData(this);
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>save-teacher",
                        data: formData,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success: function(result) {
                            const resp = $.parseJSON(result);
                            if (resp.status == 'success') {
                                toastr.success(resp.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            } else {
                                toastr.error(resp.message);
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

            function removeLogo(id) {
                $.ajax({
                type: "POST",
                url: "<?=base_url()?>remove-user-picture",
                data: 'user_id='+id,
                success: function(result) {
                    var resp = $.parseJSON(result);
                    if (resp.status == 'success') {
                        $('#user_pic').val('<?=base_url()?>photos/default.jpg');
                        $('#imageupdiv img').attr('src','<?=base_url()?>photos/default.jpg');
                        $('#edit-logo').remove();
                    }
                },
                error: function(result) {
                }
                });
            }
        </script>
    </body>
</html>