<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('includes/head'); ?>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet">
    <style>
        .table>thead {
            background-color: #ffffff;
        }
        .table>tbody#t_body>tr>td {
            padding: 8px 0px;
        }
        .p-t-8{
            padding-top: 8px;
        }
        .p-t-6{
            padding-top: 6px;
        }
        .closeButtonbg{
            right:0px;
            position: absolute;
            padding: 2px !important;
            color: #cd1e1a !important;
            background-color: #e5e5e5 !important;
            opacity: 0.7 !important;
        }
        .closeButtonbg:hover{
            color: #a90404 !important;
            background-color: #ffffff !important;
        }
        .closeButtonsm{
            left:21px;
            position: absolute;
            color: #cd1e1a !important;
            background-color: #e5e5e5 !important;
            opacity: 0.7 !important;
        }
        .closeButtonsm:hover{
            color: #a90404 !important;
            background-color: #ffffff !important;
        }

        .quill-editor {
            height: 150px; 
        }
    </style>
</head>

<body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
        <?php $this->load->view('includes/sidebar'); ?>

        <div class="site-content">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <button type="button" class="btn btn-outline-warning btn-pill" title="Go Back" onclick="location.href='<?=base_url()?>Questionnaire/questions/137'"><i class="zmdi zmdi-arrow-left"></i></button>
                    </div>
                    <h3 class="m-y-0"><?=$type?> Question</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2">
                            <form data-toggle="validator" id="inputmasks">
                                <input type="hidden" name="que_id" id="que_id" value="<?php if(!empty($question_detail)){echo($question_detail->que_id);}else{echo(0);} ?>">
                                <div class="row">
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <label for="class_id" class="control-label">Institute</label>
                                            <select class="form-control" name="class_id" id="class_id"
                                                data-plugin="select2" data-placeholder="Select a institute"
                                                data-required-error="Institute is required" required>
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
                                            <select class="form-control" name="sub_id" id="sub_id" data-plugin="select2"
                                                data-placeholder="Select a circle"
                                                data-required-error="Circle is required" required>
                                                <option></option>
                                                <option value="">Science</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <label for="term_id" class="control-label">Point Type</label>
                                            <select class="form-control" name="term_id" id="term_id"
                                                data-plugin="select2" data-placeholder="Select a point type"
                                                data-required-error="Point type is required" required>
                                                <option></option>
                                                <?php 
                                                    foreach ($terms as $row) { 
                                                        $sel = '';
                                                        if (!empty($question_detail)) {
                                                            if ($question_detail->exam_type == $row->extype_id) {
                                                                $sel = 'selected';
                                                            }
                                                        }
                                                ?>
                                                    <option value="<?=$row->extype_id?>" <?=$sel?>><?=$row->extype_name?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <label for="qt_id" class="control-label">Question Type</label>
                                            <select class="form-control" name="qt_id" id="qt_id" data-plugin="select2"
                                                data-placeholder="Select a question type"
                                                data-required-error="Question type is required" required>
                                                <option></option>
                                                <?php foreach ($question_type as $row) { ?>
                                                    <option value="<?=$row->qt_id?>"><?=$row->question_type?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-md-3 p-t-8 when-mcq-triggered hidden-xs hidden-lg">
                                        <div class="form-group">
                                            <label class="custom-control custom-control-primary custom-radio">
                                                <input class="custom-control-input" type="radio" name="mcqanswermethods" value="single" onchange="loadFields()">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-label">Single Answer</span>
                                            </label>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3 p-t-8 when-mcq-triggered hidden-xs hidden-lg">
                                        <div class="form-group">
                                            <label class="custom-control custom-control-primary custom-radio">
                                                <input class="custom-control-input" type="radio" name="mcqanswermethods" value="multiple" onchange="loadFields()">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-label">Multiple Answer</span>
                                            </label>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3 p-t-8 when-strc-triggered hidden-xs hidden-lg">
                                        <div class="form-group">
                                            <label class="custom-control custom-control-primary custom-radio">
                                                <input class="custom-control-input" type="radio" name="structuredanswermethod" value="smallbox" onchange="loadFields()">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-label">Small Box</span>
                                            </label>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3 p-t-8 when-strc-triggered hidden-xs hidden-lg">
                                        <div class="form-group">
                                            <label class="custom-control custom-control-primary custom-radio">
                                                <input class="custom-control-input" type="radio" name="structuredanswermethod" value="textbox" onchange="loadFields()">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-label">Text Box</span>
                                            </label>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="noq" name="noq" placeholder="No. of Questions" data-required-error="No of questions are required" disabled required autocomplete="off" onchange="loadFields()">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="noa" name="noa" placeholder="No. of Answers" data-required-error="No of answers are required" disabled required autocomplete="off" onchange="loadFields()">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="question_fields">
                                    
                                </div>
                                <button type="submit" class="btn btn-primary btn-block" id="submitBtn">Submit</button> <!-- onclick="tinyMCE.triggerSave(true,true);" -->
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
    <!-- <script src="//cdn.tinymce.com/4/tinymce.min.js"></script> -->
    <!-- <script src="https://cdn.tiny.cloud/1/qrzhu7xxauibdx9fu2r32z02zajqauddn3li98u8803z276c/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.js"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            <?php if (!empty($question_detail)) { ?>
                var questionType = '<?=$question_detail->qt_id?>';
                var queHasImg = '<?=$question_detail->has_img?>';

                $('#class_id').val('<?=$question_detail->class_id?>').trigger('change');
                $('#qt_id').val('<?=$question_detail->qt_id?>').trigger('change');
                $('input[type="radio"][value="<?=$question_detail->answer_method?>"]').prop('checked',true).trigger('change');
                $('#noq').val(1).trigger('change').attr('disabled','disabled');
                $('#noa').val('<?=count($question_detail->answers)?>').trigger('change');
                if (questionType == 1) {
                    $('input[name="questions[]"]').val(`<?=$question_detail->question?>`);
                }else{
                    $('textarea[name="questions[]"]').val(`<?=$question_detail->question?>`); // normal textarea boxes
                    $('div.questionTextBox').find('.ql-editor').html(`<?=$question_detail->question?>`); // quill editor boxes
                }
                if (queHasImg == 1) {
                    $('#question-1-img').val('<?=$question_detail->que_pic?>');
                    $('#question-1-img-prv').find('label').hide();
                    $('#question-1-img-prv').find('div.mm-avatar').show();
                    $('#question-1-img-prv').find('div.mm-avatar').find('img').attr('src','<?=base_url()?>photos/questionaire/<?=$question_detail->que_pic?>');
                    $('#question-1-img-prv').find('div.mm-avatar').find('button').attr('onclick','removeImage(`<?=$question_detail->que_pic?>`,`question-1-img`)');
                }
                var answerArr = <?php echo json_encode($question_detail->answers); ?>;
                if (questionType != 3) {
                    if ('<?=$question_detail->answer_method?>' == 'single'  || '<?=$question_detail->answer_method?>' == 'multiple'  || '<?=$question_detail->answer_method?>' == 'smallbox') {
                        for (let i = 0; i < answerArr.length; i++) {
                            $('.set-answer-'+i).val(answerArr[i].answer);
                            if ('<?=$question_detail->answer_method?>' != 'smallbox') {
                                if (answerArr[i].correct_answer == 1) {
                                    $('input[name="correctanswer[0]['+i+']"]').prop('checked',true);
                                }
                                if (answerArr[i].has_img == 1) {
                                    $('#question-1-answer-'+(i+1)+'-img').val(answerArr[i].ans_pic);
                                    $('#question-1-answer-'+(i+1)+'-img-prv').find('label').hide();
                                    $('#question-1-answer-'+(i+1)+'-img-prv').find('div.mm-avatar').show();
                                    $('#question-1-answer-'+(i+1)+'-img-prv').find('div.mm-avatar').find('img').attr('src','<?=base_url()?>photos/questionaire/'+answerArr[i].ans_pic);
                                    $('#question-1-answer-'+(i+1)+'-img-prv').find('div.mm-avatar').find('button').attr('onclick','removeImage(`'+answerArr[i].ans_pic+'`,`question-1-answer-'+(i+1)+'-img`)');
                                }
                            }
                        }
                    }else{
                        for (let i = 0; i < answerArr.length; i++) {
                            // $('textarea.answerTextBox').val(answerArr[i].answer);
                            $('div.answerTextBox').find('.ql-editor').html(answerArr[i].answer);
                        }
                    }
                }else{
                    for (let i = 0; i < answerArr.length; i++) {
                        // $('#answerTextBox0_'+i).val(answerArr[i].answer);
                        $('#answer-editor-0_'+i).find('.ql-editor').html(answerArr[i].answer);
                    }
                }
                
            <?php } ?>
        });

        $("#class_id,#sub_id,#term_id,#qt_id").select2({
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
                    <?php if (!empty($question_detail)) { ?>
                        $('#sub_id').val('<?=$question_detail->subject?>').trigger('change');
                    <?php } ?>
                },
                error: function(result) {
                    toastr.error('Error :'+result)
                }
            });
        })

        $('#qt_id').on('change',function() {
            var qType = this.value;
            if (qType == 1) {
                $('#question_fields').html('');
                if (!$('.when-strc-triggered').hasClass('hidden-xs hidden-lg')) {
                    $('.when-strc-triggered').addClass('hidden-xs hidden-lg');
                }
                $('#noq,#noa').parent().parent().removeClass('col-md-6').addClass('col-md-3')
                $('.when-mcq-triggered').removeClass('hidden-xs hidden-lg');
                $('input[name="mcqanswermethods"]').attr('required',true);
                $('input[name="structuredanswermethod"]').attr('required',false);
                $('#noq,#noa').attr('disabled',false);
                $('#noa').val('').removeAttr('readonly');
                $('#noq,#noa').val('');
            } else if (qType == 2) {
                $('#question_fields').html('');
                if (!$('.when-mcq-triggered').hasClass('hidden-xs hidden-lg')) {
                    $('.when-mcq-triggered').addClass('hidden-xs hidden-lg');
                }
                $('#noq,#noa').parent().parent().removeClass('col-md-6').addClass('col-md-3')
                $('.when-strc-triggered').removeClass('hidden-xs hidden-lg');
                $('#noq,#noa').attr('disabled',false);
                $('input[name="mcqanswermethods"]').attr('required',false);
                $('input[name="structuredanswermethod"]').attr('required',true);
                $('#noa').val('').removeAttr('readonly');
                $('#noq,#noa').val('');
            } else if (qType == 3) {
                $('#question_fields').html('');
                if (!$('.when-strc-triggered').hasClass('hidden-xs hidden-lg')) {
                    $('.when-strc-triggered').addClass('hidden-xs hidden-lg');
                }
                if (!$('.when-mcq-triggered').hasClass('hidden-xs hidden-lg')) {
                    $('.when-mcq-triggered').addClass('hidden-xs hidden-lg');
                }
                $('#noq,#noa').parent().parent().removeClass('col-md-3').addClass('col-md-6')
                $('#noq,#noa').attr('disabled',false);
                // $('#noa').val(1).attr('readonly','readonly').trigger('change');
                $('#noq,#noa').val('').removeAttr('readonly');
                $('#noq').val('');
                $('input[name="mcqanswermethods"]').attr('required',false);
                $('input[name="structuredanswermethod"]').attr('required',false);
            } else {
                $('#question_fields').html('');
                if (!$('.when-strc-triggered').hasClass('hidden-xs hidden-lg')) {
                    $('.when-strc-triggered').addClass('hidden-xs hidden-lg');
                }
                if (!$('.when-mcq-triggered').hasClass('hidden-xs hidden-lg')) {
                    $('.when-mcq-triggered').addClass('hidden-xs hidden-lg');
                }
                $('#noq,#noa').parent().parent().removeClass('col-md-6').addClass('col-md-3')
                $('#noq,#noa').val('').removeAttr('readonly');
                $('#noq,#noa').attr('disabled',true);
                $('input[name="mcqanswermethods"]').attr('required',false);
                $('input[name="structuredanswermethod"]').attr('required',false);
            }
        });

        function loadFields() {
            var qType = $('#qt_id option:selected').val();
            var noOfQues = $('#noq').val();
            var noOfAnsw = $('#noa').val();

            var fields = '';

            if (qType == 1) {
                var fieldType = $('input[name="mcqanswermethods"]:checked').val();
                if (fieldType == 'single') {
                    for (let q = 0; q < noOfQues; q++) {
                        fields +=   '<div class="col-sm-6 col-md-12 authentication-content m-b-10">'+
                                        '<div class="form-group row gutter-xs">'+
                                            '<label class="col-md-1 control-label">QUE'+(q+1)+'</label>'+
                                            '<div class="col-md-10">'+
                                                '<input class="form-control" type="text" placeholder="Question" name="questions[]" autocomplete="off">'+
                                            '</div>'+
                                            '<div class="col-md-1">'+
                                                '<span class="input-group-btn" id="question-'+(q+1)+'-img-prv">'+
                                                    '<label class="btn btn-primary file-upload-btn">'+
                                                        '<input class="file-upload-input" type="file" onchange="uploadImage(event,`question-'+(q+1)+'-img`)">'+
                                                        '<input type="hidden" name="questionImgs[]" id="question-'+(q+1)+'-img">'+
                                                            '<i class="zmdi zmdi-attachment-alt"></i>'+
                                                    '</label>'+
                                                    '<div class="mm-avatar" style="display:none;position:relative;"><button type="button" class="close closeButtonsm"><span>&times;</span></button><img src="" alt="" width="32" height="32"></div>'+
                                                '</span>'+
                                            '</div>'+
                                        '</div>';
                                    for (let a = 0; a < noOfAnsw; a++) {
                                        fields +=   '<div class="row">'+
                                                        '<div class="col-sm-6 col-md-12">'+
                                                            '<div class="form-group row gutter-xs">'+
                                                                '<label class="col-sm-1 col-md-1 control-label"></label>'+
                                                                '<div class="col-sm-7 col-md-7 ">'+
                                                                    '<input class="form-control set-answer-'+a+'" type="text" placeholder="Answer" name="answers['+q+'][]" autocomplete="off">'+
                                                                '</div>'+
                                                                '<div class="col-sm-2 col-md-2 p-t-6">'+
                                                                    '<div class="form-group">'+
                                                                        '<label class="custom-control custom-control-success custom-radio">'+
                                                                            '<input class="custom-control-input radioinput'+q+'" type="radio" name="correctanswer['+q+']['+a+']" value="1" onclick="selectOneRadio(this,`radioinput'+q+'`);">'+
                                                                            '<span class="custom-control-indicator"></span>'+
                                                                        '</label>'+
                                                                        '<div class="help-block with-errors"></div>'+
                                                                    '</div>'+
                                                                '</div>'+
                                                                '<div class="col-sm-2 col-md-2">'+
                                                                    '<span class="input-group-btn" id="question-'+(q+1)+'-answer-'+(a+1)+'-img-prv">'+
                                                                        '<label class="btn btn-warning file-upload-btn">'+
                                                                            '<input class="file-upload-input" type="file" onchange="uploadImage(event,`question-'+(q+1)+'-answer-'+(a+1)+'-img`)">'+
                                                                            '<input type="hidden" name="answerImgs['+q+'][]" id="question-'+(q+1)+'-answer-'+(a+1)+'-img">'+
                                                                            '<i class="zmdi zmdi-attachment-alt"></i>'+
                                                                        '</label>'+
                                                                        '<div class="mm-avatar" style="display:none;position:relative;"><button type="button" class="close closeButtonsm"><span>&times;</span></button><img src="" alt="" width="32" height="32"></div>'+
                                                                    '</span>'+
                                                                '</div>'+
                                                            '</div>'+
                                                        '</div>'+
                                                    '</div>';
                                    }
                        fields += '</div>';
                    }
                    $('#question_fields').html(fields);

                }else if (fieldType == 'multiple') {
                    for (let q = 0; q < noOfQues; q++) {
                        fields +=   '<div class="col-sm-6 col-md-12 authentication-content m-b-10">'+
                                        '<div class="form-group row gutter-xs">'+
                                            '<label class="col-md-1 control-label">QUE'+(q+1)+'</label>'+
                                            '<div class="col-md-10">'+
                                                '<input class="form-control" type="text" placeholder="Question" name="questions[]" autocomplete="off">'+
                                            '</div>'+
                                            '<div class="col-md-1">'+
                                                '<span class="input-group-btn" id="question-'+(q+1)+'-img-prv">'+
                                                    '<label class="btn btn-primary file-upload-btn">'+
                                                        '<input class="file-upload-input" type="file" onchange="uploadImage(event,`question-'+(q+1)+'-img`)">'+
                                                        '<input type="hidden" name="questionImgs[]" id="question-'+(q+1)+'-img">'+
                                                            '<i class="zmdi zmdi-attachment-alt"></i>'+
                                                    '</label>'+
                                                    '<div class="mm-avatar" style="display:none;position:relative;"><button type="button" class="close closeButtonsm"><span>&times;</span></button><img src="" alt="" width="32" height="32"></div>'+
                                                '</span>'+
                                            '</div>'+
                                        '</div>';
                                    for (let a = 0; a < noOfAnsw; a++) {
                                        fields +=   '<div class="row">'+
                                                        '<div class="col-sm-6 col-md-12">'+
                                                            '<div class="form-group row gutter-xs">'+
                                                                '<label class="col-sm-1 col-md-1 control-label"></label>'+
                                                                '<div class="col-sm-7 col-md-7 ">'+
                                                                    '<input class="form-control set-answer-'+a+'" type="text" placeholder="Answer" name="answers['+q+'][]" autocomplete="off">'+
                                                                '</div>'+
                                                                '<div class="col-sm-2 col-md-2 p-t-6">'+
                                                                    '<div class="form-group">'+
                                                                        '<label class="custom-control custom-control-success custom-checkbox active">'+
                                                                            '<input class="custom-control-input" type="checkbox" name="correctanswer['+q+']['+a+']" value="1">'+
                                                                            '<span class="custom-control-indicator"></span>'+
                                                                        '</label>'+
                                                                        '<div class="help-block with-errors"></div>'+
                                                                    '</div>'+
                                                                '</div>'+
                                                                '<div class="col-sm-2 col-md-2">'+
                                                                    '<span class="input-group-btn" id="question-'+(q+1)+'-answer-'+(a+1)+'-img-prv">'+
                                                                        '<label class="btn btn-warning file-upload-btn">'+
                                                                            '<input class="file-upload-input" type="file" onchange="uploadImage(event,`question-'+(q+1)+'-answer-'+(a+1)+'-img`)">'+
                                                                            '<input type="hidden" name="answerImgs['+q+'][]" id="question-'+(q+1)+'-answer-'+(a+1)+'-img">'+
                                                                            '<i class="zmdi zmdi-attachment-alt"></i>'+
                                                                        '</label>'+
                                                                        '<div class="mm-avatar" style="display:none;position:relative;"><button type="button" class="close closeButtonsm"><span>&times;</span></button><img src="" alt="" width="32" height="32"></div>'+
                                                                    '</span>'+
                                                                '</div>'+
                                                            '</div>'+
                                                        '</div>'+
                                                    '</div>';
                                    }
                        fields += '</div>';
                    }
                    $('#question_fields').html(fields);
                } else {
                    $('#question_fields').html('');
                }
            } else if (qType == 2) {
                var fieldType = $('input[name="structuredanswermethod"]:checked').val();
                if (fieldType == 'smallbox') {
                    $('#noa').removeAttr('readonly');
                    
                    for (let q = 0; q < noOfQues; q++) {
                        fields +=   '<div class="col-sm-6 col-md-12 authentication-content m-b-10">'+
                                        '<div class="form-group row gutter-xs">'+
                                            '<label class="col-md-1 control-label">QUE'+(q+1)+'</label>'+
                                            '<div class="col-md-10">'+
                                                '<textarea data-plugin="autosize" class="form-control" placeholder="Type your question..." name="questions[]" style="resize: none; height: 54px; overflow: hidden; overflow-wrap: break-word;" autocomplete="off"></textarea>'+
                                            '</div>'+
                                            '<div class="col-md-1">'+
                                                '<span class="input-group-btn" id="question-'+(q+1)+'-img-prv">'+
                                                    '<label class="btn btn-primary file-upload-btn" style="width: 100%; height:53px;">'+
                                                        '<input class="file-upload-input" type="file" onchange="uploadImage(event,`question-'+(q+1)+'-img`)">'+
                                                        '<input type="hidden" name="questionImgs[]" id="question-'+(q+1)+'-img">'+
                                                            '<i class="zmdi zmdi-attachment-alt" style="font-size: 35px;"></i>'+
                                                    '</label>'+
                                                    '<div class="mm-avatar" style="display:none;position:relative;"><button type="button" class="close closeButtonbg"><span>&times;</span></button><img src="" alt="" width="50" height="53"></div>'+
                                                '</span>'+
                                            '</div>'+
                                        '</div>';
                                    for (let a = 0; a < noOfAnsw; a++) {    
                                        fields +=   '<div class="row">'+
                                                        '<div class="col-sm-6 col-md-12">'+
                                                            '<div class="form-group row gutter-xs">'+
                                                                '<label class="col-sm-1 col-md-1 control-label"></label>'+
                                                                '<div class="col-sm-10 col-md-10 ">'+
                                                                    '<input class="form-control set-answer-'+a+'" type="text" placeholder="Answer" name="answers['+q+'][]" autocomplete="off">'+
                                                                '</div>'+
                                                            '</div>'+
                                                        '</div>'+
                                                    '</div>';
                                    }
                        fields += '</div>';
                    }
                    $('#question_fields').html(fields);
                } else if (fieldType == 'textbox') {
                    $('#noa').val(1).attr('readonly','readonly');
                    noOfAnsw = 1;
                    
                    for (let q = 0; q < noOfQues; q++) {
                        fields +=   '<div class="col-sm-6 col-md-12 authentication-content m-b-10">'+
                                        '<div class="form-group row gutter-xs">'+
                                            '<label class="col-md-1 control-label">QUE'+(q+1)+'</label>'+
                                            '<div class="col-md-10">'+
                                                // '<textarea  class="form-control questionTextBox" id="questionTextBox'+q+'" name="questions[]"></textarea>'+
                                                '<div id="question-editor-' + q + '" class="quill-editor questionTextBox" data-id="' + q + '"></div>'+
                                            '</div>'+
                                        '</div>';
                                    for (let a = 0; a < noOfAnsw; a++) {   
                                        fields +=   '<div class="row">'+
                                                        '<div class="col-sm-6 col-md-12">'+
                                                            '<div class="form-group row gutter-xs">'+
                                                                '<label class="col-sm-1 col-md-1 control-label">ANS</label>'+
                                                                '<div class="col-sm-10 col-md-10 ">'+
                                                                    // '<textarea  class="form-control answerTextBox" id="answerTextBox'+q+'" name="answers['+q+'][]"></textarea>'+
                                                                    '<div id="answer-editor-' + q +'" class="quill-editor answerTextBox" data-id="' + q +'"></div>'+
                                                                '</div>'+
                                                            '</div>'+
                                                        '</div>'+
                                                    '</div>';
                                    }
                        fields += '</div>';
                    }
                    $('#question_fields').html(fields);
                    $('.quill-editor').each(function() {
                        var editorId = $(this).attr('id');
                        var quill = new Quill('#' + editorId, {
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],        // Text formatting options
                                    [{ 'color': [] }, { 'background': [] }],          // Text color and background color
                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],     // Ordered and unordered lists
                                    [{ 'indent': '-1'}, { 'indent': '+1' }],          // Increase and decrease indentation
                                    [{ 'align': [] }],                                // Text alignment
                                    ['link', 'image', 'video'],                       // Link, image, and video insertion
                                    ['clean']                                         // Remove formatting
                                ],
                            },
                            formats: [
                                'header',
                                'bold', 'italic', 'underline', 'strike',
                                'color', 'background',
                                'list', 'indent', 'align',
                                'link', 'image', 'video'
                            ],
                            theme: 'snow',
                        });
                    });
                    /* tinymce.init({
                        selector: ".questionTextBox,.answerTextBox",
                        branding: false,
                        height : "250",
                        theme: 'silver',
                        plugins: [
                            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                            "searchreplace wordcount visualblocks visualchars code fullscreen",
                            "insertdatetime media nonbreaking save table contextmenu directionality",
                            "textcolor"
                        ],
                        toolbar: "styleselect fontselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | print preview media | link image"
                    });
                    // This line for when append tinymce it wont work properly. now works.
                    setTimeout(
                        function(){
                            $(".questionTextBox,.answerTextBox").each(function(){
                                tinymce.execCommand('mceRemoveEditor',true,this.id);
                                tinymce.execCommand('mceAddEditor',true,this.id);
                            })
                    },50);  */
                } else {
                    $('#question_fields').html('');
                }
            } else if (qType == 3) {
                for (let q = 0; q < noOfQues; q++) {
                        fields +=   '<div class="col-sm-6 col-md-12 authentication-content m-b-10">'+
                                        '<div class="form-group row gutter-xs">'+
                                            '<label class="col-md-1 control-label">QUE'+(q+1)+'</label>'+
                                            '<div class="col-md-10">'+
                                                // '<textarea  class="form-control questionTextBox" id="questionTextBox'+q+'" name="questions[]"></textarea>'+
                                                '<div id="question-editor-' + q + '" class="quill-editor questionTextBox" data-id="' + q + '"></div>'+
                                            '</div>'+
                                        '</div>';
                                    for (let a = 0; a < noOfAnsw; a++) {   
                                        fields +=   '<div class="row">'+
                                                        '<div class="col-sm-6 col-md-12">'+
                                                            '<div class="form-group row gutter-xs">'+
                                                                '<label class="col-sm-1 col-md-1 control-label">ANS</label>'+
                                                                '<div class="col-sm-10 col-md-10 ">'+
                                                                    // '<textarea  class="form-control answerTextBox" id="answerTextBox'+q+'_'+a+'" name="answers['+q+'][]"></textarea>'+
                                                                    '<div id="answer-editor-' + q +'_'+a+'" class="quill-editor answerTextBox" data-id="' + q +'_'+a+'"></div>'+
                                                                '</div>'+
                                                            '</div>'+
                                                        '</div>'+
                                                    '</div>';
                                    }
                        fields += '</div>';
                    }
                    $('#question_fields').html(fields);
                    $('.quill-editor').each(function() {
                        var editorId = $(this).attr('id');
                        var quill = new Quill('#' + editorId, {
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],        // Text formatting options
                                    [{ 'color': [] }, { 'background': [] }],          // Text color and background color
                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],     // Ordered and unordered lists
                                    [{ 'indent': '-1'}, { 'indent': '+1' }],          // Increase and decrease indentation
                                    [{ 'align': [] }],                                // Text alignment
                                    ['link', 'image', 'video'],                       // Link, image, and video insertion
                                    ['clean']                                         // Remove formatting
                                ],
                            },
                            formats: [
                                'header',
                                'bold', 'italic', 'underline', 'strike',
                                'color', 'background',
                                'list', 'indent', 'align',
                                'link', 'image', 'video'
                            ],
                            theme: 'snow',
                        });
                    });
                    /* tinymce.init({
                        selector: ".questionTextBox,.answerTextBox",
                        branding: false,
                        height : "250",
                        theme: 'silver',
                        plugins: [
                            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                            "searchreplace wordcount visualblocks visualchars code fullscreen",
                            "insertdatetime media nonbreaking save table contextmenu directionality",
                            "textcolor"
                        ],
                        toolbar: "styleselect fontselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | print preview media | link image"
                    });
                    // This line for when append tinymce it wont work properly. now works.
                    setTimeout(
                        function(){
                            $(".questionTextBox,.answerTextBox").each(function(){
                                tinymce.execCommand('mceRemoveEditor',true,this.id);
                                tinymce.execCommand('mceAddEditor',true,this.id);
                            })
                    },50);  */
            }
        }

        function selectOneRadio(el,classForSelection) {
			$('.'+classForSelection).prop('checked',false);
			$(el).prop('checked',true);
		}

        // preview the image before upload....

        /* var loadFile = function(event) {
            var image = document.getElementById('output');
            image.src = URL.createObjectURL(event.target.files[0]);
        }; */

        function getEditorContent(editorId) {
            console.log('editorId', editorId)
            var quill = $('#' + editorId);
            if (quill) {
                var htmlContent = $('#' + editorId).find('.ql-editor').html();
                console.log('htmlContent', htmlContent)
                return htmlContent;
            } else {
                console.error('Quill editor not found with ID: ' + editorId);
                return '';
            }
        }

        function uploadImage(event,setField) {
            var file = event.target.files[0];
            var formData = new FormData();
            formData.append('questionaireImg', file);
            $.ajax({
                type:'POST',
                url: '<?=base_url()?>upload-questionaire-image',
                data:formData,
                cache:false,
                contentType: false,
                processData: false,
                success:function(data){
                    var resp = $.parseJSON(data);
                    if (resp.status == 'success') {
                        $('#'+setField).val(resp.uploadedImage);
                        $('#'+setField+'-prv').find('label').hide();
                        $('#'+setField+'-prv').find('div.mm-avatar').show();
                        $('#'+setField+'-prv').find('div.mm-avatar').find('img').attr('src','<?=base_url()?>photos/questionaire/'+resp.uploadedImage);
                        $('#'+setField+'-prv').find('div.mm-avatar').find('button').attr('onclick','removeImage(`'+resp.uploadedImage+'`,`'+setField+'`)');
                    }else{
                        toastr.error(resp.message)
                    }
                },
                error: function(data){
                    toastr.error(data)
                }
            });
        }

        function removeImage(imageName,setField) {
            $.ajax({
                type:'POST',
                url: '<?=base_url()?>remove-questionaire-image',
                data:'image='+imageName,
                success:function(data){
                    var resp = $.parseJSON(data);
                    if (resp.status == 'success') {
                        $('#'+setField).val('');
                        $('#'+setField+'-prv').find('div.mm-avatar').hide();
                        $('#'+setField+'-prv').find('label').show();
                    }else{
                        toastr.error(resp.message)
                    }
                },
                error: function(data){
                    toastr.error(data)
                }
            });
        }

        $('#inputmasks').on('submit',function(e) {
            if (!(e.isDefaultPrevented())) {
                e.preventDefault();
                var formData = new FormData(this);

                const classId = $('#class_id option:selected').val();
                const subId = $('#sub_id option:selected').val();
                const termId = $('#term_id option:selected').val();

                const queType = $('#qt_id option:selected').val();

                const hasSelected = classId && subId && termId && queType;
                if (!hasSelected) {
                    return toastr.error('Please select required fields.');
                }

                run_waitMe('#inputmasks');
                var noOfQues = $('#noq').val();
                var noOfAnsw = $('#noa').val();
                if (queType == 2) {
                    var fieldType = $('input[name="structuredanswermethod"]:checked').val();
                    if (fieldType == 'textbox') {
                        for (let q = 0; q < noOfQues; q++) {
                            /* $('#questionTextBox'+q).html(tinymce.get('questionTextBox'+q).getContent());
                            $('#answerTextBox'+q).html(tinymce.get('answerTextBox'+q).getContent()); */

                            formData.append('questions[]', getEditorContent('question-editor-' + q)); // changed according to the quill editor
                            formData.append('answers['+q+'][]', getEditorContent('answer-editor-' + q)); // changed according to the quill editor
                        }
                    }
                } else if (queType == 3) {
                    for (let q = 0; q < noOfQues; q++) {
                        // $('#questionTextBox'+q).html(tinymce.get('questionTextBox'+q).getContent());
                        formData.append('questions[]', getEditorContent('question-editor-' + q)); // changed according to the quill editor
                        for (let a = 0; a < noOfAnsw; a++) {
                            // $('#answerTextBox'+q+'_'+a).html(tinymce.get('answerTextBox'+q+'_'+a).getContent());
                            formData.append('answers['+q+'][]', getEditorContent('answer-editor-' + q + '_'+a)); // changed according to the quill editor
                        }
                    }
                }
                $.ajax({
                    type: "POST",
                    url: "<?=base_url()?>save-questions",
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success : function(result) {
                        var resp = $.parseJSON(result);
                        if (resp.status == 'success') {
                            toastr.success(resp.message)
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                            $('#inputmasks').waitMe('hide');
                        }else{
                            toastr.error(resp.message)
                        }
                    },
                    error : function(result) {
                        $('#inputmasks').waitMe('hide');
                        toastr.error(result)
                    }
                });
            }
        })

    </script>

</body>
</html>