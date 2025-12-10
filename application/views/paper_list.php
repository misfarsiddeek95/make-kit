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
        #error-msgs .alert-warning {
            background-color: #ffc10780;
            border-color: #ffc107;
            color: #773707;
            font-weight: 700;
        }
    </style>
</head>

<body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
        <?php $this->load->view('includes/sidebar'); ?>
        <?php if($list){ ?>
        <div class="site-content">
            <div class="panel panel-default panel-table">
                <div class="row m-5" id="generate-div">
                    <div class="col-md-12 col-sm-12"> 
                        <div class="authentication-content m-b-30 m-l-30 m-r-30 m-t-30"> 
                            <?php if($add) { ?>
                            <div id="error-msgs">
                                
                            </div>
                            <form method="post" id="inputmasks" data-toggle="validator">
                                <div class="row">
                                    <h4>PAPER HEADER DETAILS</h4>
                                    <div class="col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <label for="school_name" class="control-label">Paper Title</label>
                                            <input type="text" name="school_name" id="school_name" class="form-control" placeholder="Enter paper title here..." autocomplete="off" required data-required-error="Paper title is required.">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">School logo</label>
                                            <label class="btn btn-default file-upload-btn d-block" for="school_logo">
                                                Choose logo...
                                                <input id="school_logo" class="file-upload-input" type="file" name="fileUpload" />
                                            </label>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <label for="duration" class="control-label">Duration (Hours in numbers)</label>
                                            <input type="text" name="duration" id="duration" pattern="^[0-9+.]+$" class="form-control" placeholder="Paper duration" autocomplete="off" required data-required-error="Paper duration is required." data-pattern-error="Invalid hours. Should be a number" />
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <label for="marks_count" class="control-label">Total Marks Count (in numbers)</label>
                                            <input type="text" name="marks_count" id="marks_count" pattern="^[0-9+.]+$" class="form-control" placeholder="Paper marks count" autocomplete="off" required data-required-error="Paper marks count is required." data-pattern-error="Invalid marks count. Should be a number" />
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <h4>PAPER GENERATION DETAILS</h4>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <label for="term_id" class="control-label">Point Type</label>
                                            <select class="form-control our-select-2" name="term_id" id="term_id"
                                                data-plugin="select2" data-placeholder="Select a exam term"
                                                data-required-error="Exam term is required" style="width:100%;" required>
                                                <option></option>
                                                <?php foreach ($exam_types as $row) { ?>
                                                    <option value="<?=$row->extype_id?>" ><?=$row->extype_name?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <label for="class_id" class="control-label">Institute</label>
                                            <select class="form-control our-select-2" name="class_id" id="class_id"
                                                data-plugin="select2" data-placeholder="Select a class"
                                                data-required-error="Class is required" style="width:100%;" required>
                                                <option></option>
                                                <?php foreach ($class as $row) { ?>
                                                    <option value="<?=$row->class_id?>" ><?=$row->class_name?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <label for="sub_id" class="control-label">Circle</label>
                                            <select class="form-control our-select-2" name="sub_id" id="sub_id" data-plugin="select2"
                                                data-placeholder="Select a subject"
                                                data-required-error="Subject is required" style="width:100%;" required>
                                                <option></option>
                                                <option value="">Science</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <label for="attempts" class="control-label">No of Attempts</label>
                                            <input type="text" name="attempts" id="attempts" pattern="^[0-9+.]+$" class="form-control" placeholder="Paper attempts" autocomplete="off" required data-required-error="Paper attempt count is required." data-pattern-error="Invalid attempts. Should be a number" />
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <?php foreach ($question_type as $row) { ?>
                                                    <tr>
                                                        <td style="padding:0px;">No of <?=$row->question_type?></td>
                                                        <td style="padding:0px;">
                                                            <div class="form-group" style="margin-bottom: 0px;">
                                                                <input type="text" name="<?=strtolower($row->question_type)?>" pattern="^[0-9+]+$" class="form-control question-types" placeholder="Number of <?=$row->question_type?> Questions" data-pattern-error="Invalid number" autocomplete="off" value="">
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group" style="margin-bottom: 0px;">
                                                                <input type="text" name="<?=strtolower($row->question_type)?>_score" pattern="^[0-9+]+$" class="form-control question-types-score" placeholder="Score per <?=$row->question_type?> Question" data-pattern-error="Invalid number" autocomplete="off" value="">
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <h5 class="m-l-15">Select questions from</h5>
                                            <?php foreach ($exam_types as $row) { ?>
                                            <div class="col-md-6">
                                                <label class="custom-control custom-control-primary custom-checkbox active">
                                                    <input class="custom-control-input" type="checkbox" name="questions_from[]" value="<?=$row->extype_id?>">
                                                    <span class="custom-control-indicator"></span>
                                                    <span class="custom-control-label"><?=$row->extype_name?></span>
                                                </label>
                                            </div>
                                            <?php } ?>
                                            <h5 class="m-l-15 m-t-50">Do you want to add previous paper questions too?</h5>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="custom-control custom-control-success custom-radio">
                                                        <input class="custom-control-input" type="radio" name="previousPaperQue" value="1" checked="checked" required>
                                                        <span class="custom-control-indicator"></span>
                                                        <span class="custom-control-label">Yes</span>
                                                    </label>
                                                    <label class="custom-control custom-control-danger custom-radio">
                                                        <input class="custom-control-input" type="radio" name="previousPaperQue" value="0" required>
                                                        <span class="custom-control-indicator"></span>
                                                        <span class="custom-control-label">No</span>
                                                    </label>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 m-b-10">
                                        <div class="form-group">
                                            <label for="mcq_main_title" class="control-label">MCQ Main Title</label>
                                            <textarea name="mcq_main_title" id="mcq_main_title"></textarea>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-12 m-b-10">
                                        <div class="form-group">
                                            <label for="structured_main_title" class="control-label">STRUCTURED Main Title</label>
                                            <textarea name="structured_main_title" id="structured_main_title"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 m-b-10">
                                        <div class="form-group">
                                            <label for="essay_main_title" class="control-label">ESSAY Main Title</label>
                                            <textarea name="essay_main_title" id="essay_main_title"></textarea>
                                        </div>
                                    </div> -->
                                </div>
                                <button type="submit" class="btn btn-success">GENERATE</button>
                                <button type="button" class="btn btn-outline-danger" onclick="showHideGenerateDiv('hide');">CANCEL</button>
                            </form>
                            <?php } else { ?>
                                <div class="alert alert-danger alert-dismissable" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">
                                        <i class="zmdi zmdi-close"></i>
                                        </span>
                                    </button>
                                    <span class="alert-icon">
                                        <i class="zmdi zmdi-close-circle-o"></i>
                                    </span>
                                    <strong>Oh snap!</strong> You don't have the permission to generate question papers.
                                </div> 
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="panel-heading">
                    <div class="panel-tools">
                        <?php if($add){ ?>
                        <button type="button" class="btn btn-outline-success btn-pill" title="Generate Paper" onclick="showHideGenerateDiv('show');"><i class="zmdi zmdi-plus"></i></button>
                        <?php } ?>
                    </div>
                    <h3 class="m-t-0 m-b-5">Question Papers</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs toptab nav-justified m-b-15" id="top_tab">
                                <?php 
                                    $i = 0;
                                    foreach ($exam_types as $row) { 
                                        $selector = url_title($row->extype_name,'-',true);
                                        if ($i == 0) {
                                            $active = 'active';
                                        }else{
                                            $active = '';
                                        }
                                ?>
                                <li class="<?=$active?>">
                                    <a href="#<?=$selector?>" role="tab" data-toggle="tab">
                                        <i class="zmdi zmdi-file-text"></i> <?=$row->extype_name?>
                                    </a>
                                </li>
                                <?php $i++;} ?>
                            </ul>
                            <div class="tab-content">
                                <?php 
                                    $i = 0;
                                    foreach ($get_all_papers as $row) { 
                                        $selector = url_title($row->extype_name,'-',true);
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
                                                    <th></th>
                                                    <th>Paper</th> 
                                                    <!-- <th style="text-align: center;">Generated By</th> -->
                                                    <th style="text-align: center;">Generated At</th>
                                                    <th style="text-align: center;">Paper Status</th>
                                                    <?php if($view || $edit || $delete) { ?>
                                                    <th style="text-align:right;">Options</th>  
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody class="tbody_data">
                                                <?php 
                                                    foreach ($row->papers as $pp) { 
                                                        $title = $row->extype_name.' - '.$pp->class_name.' - '.$pp->subject_name.' Paper: <b> '.number_format($pp->paper_duration,0).' Hour/s | Total Marks: '.number_format($pp->total_marks_count,0).'</b>';
                                                        $url_segment = 'user-detail';
                                                        switch ($pp->access_group) {
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
                                                        $added_person = $pp->added_person;
                                                        $userId = $this->session->userdata['staff_logged_in']['user_id'];
                                                        $nameTextColor = '';
                                                        if ($userId == $pp->user_id) {
                                                            $added_person = 'YOU';
                                                            $nameTextColor = 'text-danger';
                                                        }

                                                        $status = '';
                                                        if ($pp->status == 1) {
                                                            $status = 'checked="checked"';
                                                        }
                                                ?>
                                                <tr id="rowId<?=$pp->paper_id?>" class="table-row">
                                                    <td style="width: <?=$pp->term_id == 2 ?  '10%' : '5%';?>;"><i class="zmdi zmdi-long-arrow-tab"></i> <?=$pp->term_id == 2 ?  base64_encode($pp->paper_id) : '';?> </td>
                                                    <td style="width: 50%;"><?=trim($title)?></td>
                                                    <!-- <td style="text-align: center;"><a href="<?=base_url()?><?=$url_segment?>/<?=$pp->user_id?>" target="_blank" class="<?=$nameTextColor?>"><?=$added_person?></a></td> -->
                                                    <td style="text-align: center;"><?=date('d/m/Y', strtotime($pp->created_at))?></td>
                                                    <td style="text-align: center;">
                                                        <label class="switch switch-success m-t-10">
                                                            <input type="checkbox" class="s-input"  <?=$status;?> <?php if ($change_status) {echo 'onchange="updateStatus('.$pp->paper_id.');"';}else{echo "disabled";}?>>
                                                            <span class="s-content">
                                                                <span class="s-track"></span>
                                                                <span class="s-handle"></span>
                                                            </span>
                                                        </label>
                                                    </td>
                                                    <?php if($view || $edit || $delete) { ?>
                                                    <td align="right">
                                                        <?php if($view){ ?>
                                                        <a class="btn btn-outline-warning btn-sm btn-pill" href="<?=base_url()?>view-paper/<?=base64_encode($pp->paper_id);?>"><i class="zmdi zmdi-eye"></i></a>
                                                        <?php }if($edit){ ?>
                                                        <a class="btn btn-outline-primary btn-sm btn-pill" href="<?=base_url()?>edit-paper/<?=base64_encode($pp->paper_id);?>"><i class="zmdi zmdi-edit"></i></a>
                                                        <?php }if($delete){ ?>
                                                        <button type="button" class="btn btn-outline-danger btn-sm btn-pill" onclick="deleteMe('<?=$pp->paper_id?>');"><i class="zmdi zmdi-delete"></i></button>
                                                        <?php } ?>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                                <?php } ?>
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
    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.2/classic/ckeditor.js"></script>
    <script type="text/javascript">
        let mcqEditor;
        let strEditor;
        let essEditor;
        $(document).ready(function () {
            $('#generate-div').hide();
            
            ClassicEditor
                .create( document.querySelector( '#mcq_main_title' ) )
                .then( newEditor => {
                    mcqEditor = newEditor;
                } )
                .catch( error => {
                        console.error( error );
                } );

            /* ClassicEditor
                .create( document.querySelector( '#structured_main_title' ) )
                .then( editor => {
                    strEditor = editor;
                } )
                .catch( error => {
                        console.error( error );
                } );

            ClassicEditor
                .create( document.querySelector( '#essay_main_title' ) )
                .then( editor => {
                    essEditor = editor;
                } )
                .catch( error => {
                        console.error( error );
                } ); */
        });

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

        $(".our-select-2").select2({
            allowClear: true
        });

        $('#class_id').on('change',function() {
            $.ajax({
                type: "POST",
                url: "<?=base_url()?>load-class-subjects",
                data: 'class_id='+this.value,
                success: function(result){
                    var resp = $.parseJSON(result);
                    var option = '<option></option>';
                    for (let i = 0; i < resp.length; i++) { 
                      option += '<option value='+resp[i].subject_id+'>'+resp[i].subject_name+'</option>'; 
                    }
                    $('#sub_id').html(option); 
                },
                error: function(result) {
                    toastr.error('Error :'+result)
                }
            });
        });

        const showHideGenerateDiv = (type) => {
            switch (type) {
                case 'show':
                    $('.panel-heading, .panel-body').hide();
                    $('#generate-div').show();
                    break;
                case 'hide':
                    $('#generate-div').hide();
                    $('.panel-heading, .panel-body').show();
                    $('#error-msgs').html('');
                    $("#inputmasks")[0].reset();
                    $(".our-select-2").each(function () {
                        $(this).select2('destroy').val("").select2({allowClear: true});
                    });
                default:
                    $('#generate-div').hide();
                    $('.panel-heading, .panel-body').show();
                    break;
            }
        }

        $('#inputmasks').validator().on('submit', function (e) {
            if (!(e.isDefaultPrevented())) {
                e.preventDefault();
                const checkedQueFrom = $('input[name="questions_from[]"]:checked').length;
                if (checkedQueFrom <= 0) {
                    return toastr.error('Please select one of the questions from field.');
                }
                
                var questionTypeCount = 0;
                var questionTypeScore = 0;
                $('.question-types').each(function() {
                    let count = $(this).val();
                    if (count == '') {
                        count = 0;
                    }
                    questionTypeCount += parseInt(count);
                });
                if (isNaN(questionTypeCount) || questionTypeCount == 0) {
                    return toastr.error('Please enter number of questions count in question type fields (MCQ or STRUCTURED or ESSAY).');
                }

                $('.question-types-score').each(function() {
                    let count = $(this).val();
                    if (count == '') {
                        count = 0;
                    }
                    questionTypeScore += parseInt(count);
                });
                if (isNaN(questionTypeScore) || questionTypeScore == 0) {
                    return toastr.error('Please enter score of question in question type fields (MCQ or STRUCTURED or ESSAY).');
                }

                const selectedTerm = slugifyUrl($('#term_id option:selected').text());

                run_waitMe('#inputmasks');
                var form_data = new FormData(this);
                form_data.append('mcq_main_title', mcqEditor.getData());
                /* form_data.append('structured_main_title', strEditor.getData());
                form_data.append('essay_main_title', essEditor.getData()); */
                setTimeout(() => {
                    $.ajax({
                        type: 'POST',
                        url: '<?=base_url()?>generate-exam-paper',
                        data: form_data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(result) {
                            var resp = $.parseJSON(result);
                            if (resp.status == 'success') {
                                let tr = ``;
                                if (resp.queMessage.length > 0) {
                                    let msgs = ``;
                                    resp.queMessage.forEach(el => {
                                        msgs += `<div class="alert alert-warning alert-icon-bg alert-dismissable" role="alert">
                                                    <div class="alert-icon">
                                                        <i class="zmdi zmdi-alert-triangle"></i>
                                                    </div>
                                                    <div class="alert-message">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">
                                                            <i class="zmdi zmdi-close"></i>
                                                        </span>
                                                        </button>
                                                        <strong>Warning!</strong> ${el}
                                                    </div>
                                                </div>`;
                                    });
                                    $('#error-msgs').html(msgs);
                                    $('html, body').animate({scrollTop:$('#error-msgs').position().top}, 'slow');
                                } else {
                                    $("#inputmasks")[0].reset();
                                    $(".our-select-2").each(function () {
                                        $(this).select2('destroy').val("").select2({allowClear: true});
                                    });
                                    $('#generate-div').hide();
                                    $('.panel-heading, .panel-body').show();
                                }
                                if (resp.last_added != false && jQuery.isEmptyObject(resp.last_added) == false) {
                                    const data = resp.last_added;
                                    const title = data.extype_name+' - '+data.class_name+' - '+data.subject_name+' Paper: <b> '+number_without_decimal_points(data.paper_duration)+' Hour/s | Total Marks: '+number_without_decimal_points(data.total_marks_count)+'</b>';
                                    let url_segment = 'user-detail';
                                    switch (data.access_group) {
                                        case '2':
                                            url_segment = 'teacher-detail';
                                            break;
                                        case '3':
                                            url_segment = 'student-detail';
                                            break;
                                        case '4': 
                                            url_segment = 'parent-detail';
                                            break;
                                        default:
                                            url_segment = 'user-detail';
                                            break;
                                    }
                                    let added_person = data.added_person;
                                    const userId = '<?=$this->session->userdata['staff_logged_in']['user_id']?>';
                                    let nameTextColor = '';
                                    if (userId == data.user_id) {
                                        added_person = 'YOU';
                                        nameTextColor = 'text-danger';
                                    }

                                    let status = '';
                                    if (data.status == 1) {
                                        status = 'checked="checked"';
                                    }

                                    const change_status = '<?=$change_status?>';

                                    const action = change_status ? `onchange="updateStatus('${data.paper_id}')";` : `disabled`;

                                    // <td style="text-align: center;"><a href="<?=base_url()?>${url_segment}/${data.user_id}" target="_blank" class="${nameTextColor}">${added_person}</a></td>

                                    tr = `<tr id="rowId${data.paper_id}" class="table-row">
                                            <td style="width: ${data.term_id == 2 ? '10%' : '5%' };"><i class="zmdi zmdi-long-arrow-tab"></i> ${data.term_id == 2 ? btoa(data.paper_id) : ''}</td>
                                            <td style="width: 50%;">${title.trim()}</td>
                                            <td style="text-align: center;">${dmy_date_format(data.created_at)}</td>
                                            <td style="text-align: center;">
                                                <label class="switch switch-success m-t-10">
                                                    <input type="checkbox" class="s-input"  ${status} ${action}>
                                                    <span class="s-content">
                                                        <span class="s-track"></span>
                                                        <span class="s-handle"></span>
                                                    </span>
                                                </label>
                                            </td>
                                            <?php if($view || $edit || $delete) { ?>
                                            <td align="right">
                                                <?php if($view){ ?>
                                                <a class="btn btn-outline-warning btn-sm btn-pill" href="<?=base_url()?>view-paper/${btoa(data.paper_id)}"><i class="zmdi zmdi-eye"></i></a>
                                                <?php }if($edit){ ?>
                                                <a class="btn btn-outline-primary btn-sm btn-pill" href="<?=base_url()?>edit-paper/${btoa(data.paper_id)}"><i class="zmdi zmdi-edit"></i></a>
                                                <?php }if($delete){ ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-pill" onclick="deleteMe('${data.paper_id}');"><i class="zmdi zmdi-delete"></i></button>
                                                <?php } ?>
                                            </td>
                                            <?php } ?>
                                        </tr>`;
                                    const rowCount = $(`#${selectedTerm} table tbody.tbody_data tr.table-row`).length;
                                    if (rowCount == 0) {
                                        $(`#${selectedTerm} table tbody.tbody_data`).html(tr);
                                    } else {
                                        $(`#${selectedTerm} table tbody.tbody_data`).append(tr);
                                    }
                                }
                                $('#inputmasks').waitMe('hide');
                            }
                        },
                        error: function(result) {
                            $('#inputmasks').waitMe('hide');
                            toastr.error('Error :'+result)
                        }
                    });
                }, 2000);
            }
        });

        function updateStatus(id) {
            $.ajax({
                type: "POST",
                url: "<?=base_url()?>change-exam-paper-status",
                data: 'paper_id='+id,
                success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                    toastr.success(responsedata.message)
                    }else{
                    toastr.error(responsedata.message)
                    }
                },
                error: function(result) {
                    toastr.error("Somthing went wrong :(")
                }
            });
        }

        function deleteMe(paperId) {
            toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this question paper?',{
                closeButton: true,
                allowHtml: true,
                onShown: function (toast) {
                $("#confirmBtn").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "<?=base_url()?>delete-exam-paper",
                        data: 'paperId='+paperId,
                        success: function(result) {
                            var responsedata = $.parseJSON(result);
                            if (responsedata.status=='success') {
                                const table = $('.this-table').DataTable();
                                table.row('#rowId'+paperId).remove().draw( false );
                                toastr.success(responsedata.message)
                            }else{
                                toastr.error(responsedata.message)
                            }
                        },
                        error: function(result) {
                            toastr.error("Somthing went wrong :(")
                        }
                    });
                });
                $("#closeBtn").click(function(){
                    toastr.clear()
                });
                }
            });
        }
    </script>
</body>

</html>