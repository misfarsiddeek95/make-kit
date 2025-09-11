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
                        <h3 class="m-y-0 d-inline"><?=$type?> Student</h3>
                        <a class="btn btn-outline-warning btn-pill pull-right m-y-0 d-inline" href="<?=base_url()?>ExternalUsers/index/112"><i class="zmdi zmdi-arrow-left"></i></a>
                    </div>
                    <div class="panel-body"> 
                        <div class="row">
                            <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                                <form data-toggle="validator" id="inputmasks">
                                <input type="hidden" name="user_id" id="user_id" value="<?php if(!(empty($user))){echo($user->user_id);}else{echo(0);} ?>" />
                                <input type="hidden" name="add_id" id="add_id" value="<?php if(!(empty($user))){echo($user->add_id);}else{echo(0);} ?>" />
                                <div class="row">
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">Name</label>
                                            <input type="text" value="<?php if(!(empty($user))){echo($user->name);} ?>" placeholder="Name" id="name" name="name" class="form-control" data-minlength="3" data-pattern-error="Invalid name" data-error="Minimum of 3 characters" data-required-error="Name is Required" required autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="roll_number" class="control-label">Roll Number</label>
                                            <input type="text" pattern="^[a-zA-Z0-9. ]+$" value="<?php if(!(empty($user))){echo($user->role_number);} ?>" placeholder="Roll number" id="roll_number" name="roll_number" class="form-control" data-pattern-error="Invalid roll number" autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <div class="form-group">
                                            <label for="institute_id" class="control-label">Institute</label> 
                                            <select id="institute_id" name="institute_id" data-allow-clear="true" style="width:100%;" class="form-control" data-placeholder="Institute" data-plugin="select2" data-required-error="Institute is required" required>
                                                <option></option>
                                                <?php foreach ($loadInstitutes as $row) { ?>
                                                <option value="<?=$row->class_id?>"><?=$row->class_name?></option>
                                                <?php } ?>
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <div class="form-group">
                                            <label for="subject_id" class="control-label">Circle</label> 
                                            <select id="subject_id" name="subject_id" data-allow-clear="true" style="width:100%;" class="form-control" data-placeholder="Circle" data-plugin="select2" data-required-error="Circle is Required" required> 
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <div class="form-group">
                                            <label for="instructor_id" class="control-label">Instructor</label> 
                                            <select id="instructor_id" name="instructor_id" data-allow-clear="true" style="width:100%;" class="form-control" data-placeholder="Instructor" data-plugin="select2" data-required-error="Instructor is Required" required> 
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="" class="control-label">Gender</label>
                                            <div class="btn-group" data-toggle="buttons">
                                                <label class="btn btn-outline-primary">
                                                    <input type="radio" name="p_gender" id="p_gender1" autocomplete="off" value="0"> Female
                                                </label>
                                                <label class="btn btn-outline-success">
                                                    <input type="radio" name="p_gender" id="p_gender2" autocomplete="off" value="1"> Male
                                                </label>
                                                <label class="btn btn-outline-warning">
                                                    <input type="radio" name="p_gender" id="p_gender3" autocomplete="off" value="2"> Other
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="user_pic" class="control-label">Profile Picture</label>
                                            <input type="hidden" value="<?php if(!(empty($user))){if(trim($user->photo_path)!=''&&$user->photo_path!=null){echo('photos/students/'.$user->photo_path.'-std.'.$user->extension);}else{echo('photos/default.jpg');}}else{echo('photos/default.jpg');} ?>" name="user_pic" id="user_pic">
                                            <div class="row gutter-sm">
                                                <div id="imageupdiv"></div>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="parent_name" class="control-label">Parent Name</label>
                                            <input type="text" value="<?php if(!(empty($user))){echo($user->parent_name);} ?>" placeholder="Parent Name" id="parent_name" name="parent_name" class="form-control" data-minlength="3" data-pattern-error="Invalid parent name" data-error="Minimum of 3 characters" data-required-error="Parent name is required" required autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="parent_phone" class="control-label">Parent Phone</label> 
                                            <input type="text" pattern="^[0-9+]+$" value="<?php if(!(empty($user))){echo($user->parent_phone);} ?>" placeholder="Parent Phone"  id="parent_phone" name="parent_phone" class="form-control" data-pattern-error="Invalid phone number format" autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <label for="form-control-4" class="control-label">Address</label>
                                            <textarea name="address" id="address" class="form-control" rows="3" placeholder="Address" data-plugin="autosize" data-error="Address is required." required  style="resize: none; height: 54px; overflow: hidden; overflow-wrap: break-word;"><?php if(!(empty($user))){echo($user->address);}?></textarea>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-12">
                                        <div class="form-group">
                                            <label for="city" class="control-label">City</label> 
                                            <select id="city" name="city" data-allow-clear="true" style="width:100%;" class="form-control" data-placeholder="City" data-plugin="select2" data-required-error="City is Required" required> 
                                                <option></option>
                                                <?php 
                                                    foreach ($cities as $row) {
                                                        $sel = '';
                                                        if(!empty($user)) {
                                                            if($user->city_id == $row->city_id) {
                                                                $sel = "selected";
                                                            }
                                                        }
                                                ?>
                                                    <option value="<?=$row->city_id?>" <?=$sel?>><?=$row->city_name?> [ <?=$row->city_name_hebrew?>  ]</option>
                                                <?php } ?>
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="parent_email" class="control-label">Parent Email</label>
                                            <input type="email" class="form-control" id="parent_email" name="parent_email" <?php if(empty($user)){echo'required';}else{echo "disabled='disabled'";}?> value="<?php if(!(empty($user))){echo($user->parent_email);} ?>" placeholder="Parent Email" data-error="Please enter a valid email address." required data-required-error="Email is required" autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="password" class="control-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" value="" placeholder="Password" <?php if(empty($user)){echo('required');} ?> data-required-error="Password is required" autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
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
        <script src="<?=base_url()?>assets/js/bootstrap-datepicker.js"></script>
        <script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
        <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
        <script src="<?=base_url()?>assets/js/spartan-multi-image-picker.js"></script>
        <script type="text/javascript">

            $(document).ready(function() {

                let image = ``
                <?php if(!(empty($user))) { ?>
                    $('#institute_id').val('<?=$user->class_id?>').trigger('change');
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
                dropFileLabel:   'Drop logo here',
                groupClassName : 'col-md-4 col-sm-4 col-xs-6',
                placeholderImage: {image:'<?=base_url();?>'+$('#user_pic').val() ,width: '60%'},
                onRenderedPreview : function(index){
                },

                onExtensionErr : function(index, file){
                    toastr["error"]('Please only input png or jpg type file');
                },
                onSizeErr : function(index, file){
                    toastr["error"]('This file exceeds the max size(5MB)');
                }
            });

            $('#institute_id').on('change', function() {
                $('#subject_id').html(`<option></option>`);
                const val = this.value;
                $.ajax({
                    url : '<?=base_url()?>load-intitute-circles',
                    type: 'GET',
                    data: {institute_id: val},
                    success: function (result) {
                        const resp = $.parseJSON(result);
                        if (resp.status == 'error') {
                            return toastr.error(resp.message);
                        }
                        const data = resp.data;
                        let opt = `<option></option>`;

                        data.forEach(el => {
                            opt += `<option value="${el.sub_id}">${el.subject_name}</option>`;
                        });
                        $('#subject_id').html(opt);

                        <?php if(!(empty($user))) { ?>
                            $('#subject_id').val('<?=$user->subject_id?>').trigger('change');
                        <?php } ?>
                    },
                    error: function(xhr, status, error) {
                        toastr.error('AJAX Error:', status, error);
                    }
                })
            });

            $('#subject_id').on('change', function() {
                const instituteId = $('#institute_id option:selected').val();
                const val = this.value;

                $('#instructor_id').html(`<option></option>`);
                $.ajax({
                    url : '<?=base_url()?>load-subject-instructor',
                    type: 'GET',
                    data: {institute_id: instituteId, subject_id: val},
                    success: function (result) {
                        const resp = $.parseJSON(result);
                        if (resp.status == 'error') {
                            return toastr.error(resp.message);
                        }
                        const data = resp.data;
                        let opt = `<option></option>`;

                        data.forEach(el => {
                            opt += `<option value="${el.teacher_id}">${el.teacher_name}</option>`;
                        });
                        $('#instructor_id').html(opt);
                        
                        <?php if(!(empty($user))) { ?>
                            $('#instructor_id').val('<?=$user->instructor_id?>').trigger('change');
                        <?php } ?>
                    },
                    error: function(xhr, status, error) {
                        toastr.error('AJAX Error:', status, error);
                    }
                })
            });

            $('#inputmasks').validator().on('submit', function (e) {
                if (!(e.isDefaultPrevented())) {
                    e.preventDefault();
                    run_waitMe('#inputmasks');
                    var formData = new FormData(this);
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>register-student",
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
                url: "<?=base_url()?>remove-student-picture",
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