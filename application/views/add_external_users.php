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
                                <div class="row">
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">Name</label>
                                            <input type="text" pattern="^[a-zA-Z. ]+$" value="<?php if(!(empty($user))){echo($user->name);} ?>" placeholder="Name" id="name" name="name" class="form-control" data-minlength="3" data-pattern-error="Invalid name" data-error="Minimum of 3 characters" data-required-error="Name is Required" required autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="roll_number" class="control-label">Roll Number</label>
                                            <input type="text" pattern="^[a-zA-Z0-9. ]+$" value="<?php if(!(empty($user))){echo($user->role_number);} ?>" placeholder="Roll number" id="roll_number" name="roll_number" class="form-control" data-pattern-error="Invalid roll number" data-required-error="Roll number is required" required autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="class_id" class="control-label">Circle</label> 
                                            <select id="class_id" name="class_id" class="form-control" data-placeholder="Circle" data-plugin="select2" data-required-error="Circle is Required" required> 
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="city" class="control-label">City</label> 
                                            <select id="city" name="city" class="form-control" data-placeholder="City" data-plugin="select2" data-required-error="City is Required" required> 
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="class_location_id" class="control-label">Place</label> 
                                            <select id="class_location_id" name="class_location_id" class="form-control" data-placeholder="Place" data-plugin="select2" data-required-error="Place is Required" required> 
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="instructor_id" class="control-label">Instructor</label> 
                                            <select id="instructor_id" name="instructor_id" class="form-control" data-placeholder="Instructor" data-plugin="select2" data-required-error="Instructor is Required" required> 
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
                                            <input type="hidden" value="<?php if(!(empty($user))){if(trim($user->user_pic)!=''&&$user->user_pic!=null){echo('photos/staff/'.$user->user_pic.'-std.'.$user->extention);}else{echo('photos/default.jpg');}}else{echo('photos/default.jpg');} ?>" name="user_pic" id="user_pic">
                                            <div class="row gutter-sm">
                                                <div id="imageupdiv"></div>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="parent_name" class="control-label">Parent Name</label>
                                            <input type="text" pattern="^[a-zA-Z. ]+$" value="<?php if(!(empty($user))){echo($user->parent_name);} ?>" placeholder="Parent Name" id="parent_name" name="parent_name" class="form-control" data-minlength="3" data-pattern-error="Invalid parent name" data-error="Minimum of 3 characters" data-required-error="Parent name is required" required autocomplete="off">
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
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="parent_email" class="control-label">Parent Email</label>
                                            <input type="email" class="form-control" id="parent_email" name="parent_email" value="<?php if(!(empty($user))){echo($user->parent_email);} ?>" placeholder="Parent Email" data-error="Please enter a valid email address." required data-required-error="Email is required" autocomplete="off">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="form-group">
                                            <label for="password" class="control-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" value="<?php if(!(empty($user))){echo($user->password);} ?>" placeholder="Password" required data-required-error="Password is required" autocomplete="off">
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
        </script>
    </body>
</html>