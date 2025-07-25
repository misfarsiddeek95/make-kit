<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('includes/head'); ?>
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
        .panel-group .panel{
            border: 1px dashed #288140;
        }
        /* .threedot p {
            display: -webkit-box;
            margin: 0 auto;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 20px;
            width: 325px;
        } */

        .threedot p:not(:first-child) {
            display: none;
        }
        .threedot ol,.threedot ul{
            display: none;
        }
        .threedot p:first-of-type{
            display: -webkit-box;
            margin: 0 auto;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 20px;
            width: 100%;
        }
    </style>
</head>

<body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
        <?php $this->load->view('includes/sidebar'); ?>
        <?php if($question_list){ ?>
        <div class="site-content">
            <div class="panel panel-default panel-table">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <?php if($add){ ?>
                        <button type="button" class="btn btn-outline-success btn-pill" title="Add Question" onclick="location.href='<?=base_url()?>add-question'"><i class="zmdi zmdi-plus"></i></button>
                        <?php } ?>
                    </div>
                    <h3 class="m-t-0 m-b-5">Questions</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs toptab nav-justified m-b-15" id="top_tab">
                                <?php 
                                    $i = 0;
                                    foreach ($question_types as $row) { 
                                        $selector = url_title($row->question_type,'-', true);
                                        if ($i == 0) {
                                            $active = 'active';
                                        }else{
                                            $active = '';
                                        }
                                ?>
                                <li class="<?=$active?>">
                                    <a href="#<?=$selector?>" role="tab" data-toggle="tab">
                                    <i class="<?=$row->icon?>"></i> <?=$row->question_type?></a>
                                </li>
                                <?php $i++;} ?>
                            </ul>
                            <div class="tab-content">
                                <?php 
                                    $i = 0;
                                    foreach ($questions as $row) { 
                                        $selector = url_title($row->question_type,'-', true);
                                        if ($i == 0) {
                                            $active = 'in active';
                                        }else{
                                            $active = '';
                                        }
                                ?>
                                <div role="tabpanel" class="tab-pane fade <?=$active?>" id="<?=$selector?>">
                                    <div class="table-responsive m-y-5"> 
                                        <table class="table table-hover this-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Question</th> 
                                                    <th>Question Type</th>
                                                    <th>Class</th> 
                                                    <th>Term</th> 
                                                    <th>Subject</th> 
                                                    <th>Question By</th>
                                                    <th style="text-align: center; width:1%;">Img</th>
                                                    <?php if($view || $edit || $delete) { ?>
                                                    <th style="text-align:right;" style="width: 20%;">Options</th>  
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody class="tbody_data">   
                                                <?php 
                                                    $count = 1; foreach ($row->questions as $que) { 
                                                        if ($que->answer_method != '') {
                                                            $answermethod = ucwords(strtolower($que->answer_method));
                                                        }
                                                        $question = strlen(strip_tags($que->question)) > 60 ? substr(strip_tags($que->question),0,60)."..." : strip_tags($que->question);
                                                        $queImg = ($que->has_img == 1) ? 'zmdi-check-circle text-success' : 'zmdi-close-circle text-danger';
                                                        $url_segment = 'user-detail';
                                                        switch ($que->access_group) {
                                                            case 2:
                                                                $url_segment = 'teacher-detail';
                                                                break;
                                                            case 3:
                                                                $url_segment = 'student-detail';
                                                                break;
                                                            case 4: 
                                                                $url_segment = 'parent-detail';
                                                                break;
                                                            default:
                                                                $url_segment = 'user-detail';
                                                                break;
                                                        }

                                                        $added_person = $que->added_person;
                                                        $userId = $this->session->userdata['staff_logged_in']['user_id'];
                                                        $nameTextColor = '';
                                                        if ($userId == $que->user_id) {
                                                            $added_person = 'YOU';
                                                            $nameTextColor = 'text-danger';
                                                        }
                                                ?>
                                                <tr id="rowId<?=$que->que_id?>">
                                                    <th><?=$count?></th>
                                                    <td class="threedot" title="<?=strip_tags($que->question)?>" style="width:40%;"><?=$que->question_showing?></td>
                                                    <td>
                                                        <span class="label label-danger"><?=$row->question_type?></span>
                                                        <span class="label label-primary"><?=$answermethod?></span>
                                                    </td>
                                                    <td><?=$que->class_name?></td>
                                                    <td><span class="label label-primary"><?=$que->extype_name?></span></td>
                                                    <td><span class="label label-info" data-toggle="tooltip" data-placement="top" data-original-title="<?=$que->subject_name?>"><?=$que->subject_name?></span></td>
                                                    <td><a href="<?=base_url()?><?=$url_segment?>/<?=$que->user_id?>" target="_blank" class="<?=$nameTextColor?>"><?=$added_person?></a></td>
                                                    <td style="width: 1%; text-align:center;">
                                                        <i class="zmdi <?=$queImg?>"></i>
                                                    </td>
                                                    <?php if($view || $edit || $delete) { ?>
                                                    <td align="right" style="width: 20%;">
                                                        <?php if($view){ ?>
                                                        <button type="button" class="btn btn-sm btn-outline-warning btn-pill m-r-5" onclick="location.href='<?=base_url()?>view-question/<?=url_title($row->question_type,'-', true)?>/<?=url_title($answermethod,'-',true)?>/<?=$que->que_id?>/'"><i class="zmdi zmdi-eye"></i></button>
                                                        <?php }if($edit){ ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary btn-pill m-r-5" onclick="editQuestion('<?=$que->que_id?>');"><i class="zmdi zmdi-edit"></i></button>
                                                        <?php }if($delete){ ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-pill" onclick="deleteQuestion('<?=$que->que_id?>');"><i class="zmdi zmdi-delete"></i></button>
                                                        <?php } ?>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                                <?php $count++; } ?>
                                            </tbody> 
                                        </table>
                                    </div>
                                </div>
                                <?php $i++;} ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php $this->load->view('includes/footer'); ?>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script type="text/javascript">
        $('.this-table').DataTable();

        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "inherit");
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "auto");
        });

        // Tab lie on same after refresh also
        $('ul.toptab li a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            localStorage.setItem('activeTopTab', $(e.target).attr('href'));
        });
        var activeTopTab = localStorage.getItem('activeTopTab');
        if(activeTopTab){
            $('#top_tab a[href="' + activeTopTab + '"]').tab('show');
        }
        // Tab lie on same after refresh also
        $('ul.bottab li a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#bottom_tab a[href="' + activeTab + '"]').tab('show');
        }

        function editQuestion(id) {
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "<?=base_url()?>edit-question");

            hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "que_id");
            hiddenField.setAttribute("value", id);
            form.appendChild(hiddenField);

            document.body.appendChild(form);
            form.submit();
        }

        function deleteQuestion(id) {
            swal({
                title: "Are you sure?",
                text: "Your will not be able to recover this!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            },
            function(){
                $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>delete-question",
                    data: 'que_id='+id,
                    success: function(result) {
                    var resp = $.parseJSON(result);
                    if (resp.status=='success') {
                        var table = $('.this-table').DataTable();
                        table.row('#rowId'+id).remove().draw( false );
                        swal("Done!", resp.message, "success")
                    }else{
                        swal("Sorry!",resp.message, "error");
                    }
                    },
                    error: function(result) {
                        swal("", "Somthing went wrong :(", "error");
                    }
                });
            }); 
        } 

    </script>
</body>

</html>