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
        <div class="site-content">
            <div class="panel panel-default panel-table">
                <div class="panel-heading">
                    <h3 class="m-y-0 d-inline">Edit Question Paper</h3>
                    <a class="btn btn-outline-warning btn-pill pull-right m-y-0 d-inline" href="<?=base_url()?>Questionnaire/generateQuestionPaper/274"><i class="zmdi zmdi-arrow-left"></i></a>
                </div>
                <div class="panel-body">
                    <div class="row m-5" id="generate-div">
                        <div class="col-md-12 col-sm-12"> 
                            <form method="post" id="inputmasks" data-toggle="validator">
                                <input type="hidden" name="paperId" value="<?=$getQuestionPaper->paper_id?>" />
                                <div class="authentication-content m-b-30 m-l-30 m-r-30 m-t-30"> 
                                    <div class="row">
                                        <h4>PAPER HEADER DETAILS</h4>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label for="school_logo" class="control-label">School Logo</label>
                                                <input type="hidden" value="<?php if(!(empty($getQuestionPaper))){if(trim($getQuestionPaper->school_logo)!=''&&$getQuestionPaper->school_logo!=null){echo($getQuestionPaper->school_logo);}else{echo('photos/default.jpg');}}else{echo('photos/default.jpg');} ?>" name="school_logo" id="school_logo">
                                                <div class="row gutter-sm">
                                                    <div id="imageupdiv"></div>
                                                </div>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label for="school_name" class="control-label">School name</label>
                                                <input type="text" name="school_name" id="school_name" class="form-control" placeholder="Enter school name here..." value="<?=$getQuestionPaper->school_name?>" autocomplete="off" required data-required-error="School name is required." readonly />
                                                <p class="help-block">
                                                    <small>You can not edit this field.</small>
                                                </p>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label for="duration" class="control-label">Duration (Hours in numbers)</label>
                                                <input type="text" name="duration" id="duration" pattern="^[0-9+.]+$" class="form-control" placeholder="Paper duration" value="<?=number_format($getQuestionPaper->paper_duration, 0)?>" autocomplete="off" required data-required-error="Paper duration is required." data-pattern-error="Invalid hours. Should be a number" readonly />
                                                <p class="help-block">
                                                    <small>You can not edit this field.</small>
                                                </p>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label for="marks_count" class="control-label">Total Marks Count (in numbers)</label>
                                                <input type="text" name="marks_count" id="marks_count" pattern="^[0-9+.]+$" class="form-control" placeholder="Paper marks count" autocomplete="off" value="<?=number_format($getQuestionPaper->total_marks_count, 0)?>" required data-required-error="Paper marks count is required." data-pattern-error="Invalid marks count. Should be a number" readonly />
                                                <p class="help-block">
                                                    <small>You can not edit this field.</small>
                                                </p>
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
                                                    <?php 
                                                        foreach ($exam_types as $row) {
                                                            $sel = '';
                                                            if ($row->extype_id == $getQuestionPaper->term_id) {
                                                                $sel = 'selected';
                                                            }
                                                    ?>
                                                        <option value="<?=$row->extype_id?>" <?=$sel?>><?=$row->extype_name?></option>
                                                    <?php } ?>
                                                </select>
                                                <p class="help-block">
                                                    <small>You can not edit this field.</small>
                                                </p>
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
                                                <p class="help-block">
                                                    <small>You can not edit this field.</small>
                                                </p>
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
                                                <p class="help-block">
                                                    <small>You can not edit this field.</small>
                                                </p>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-group">
                                                <label for="attempts" class="control-label">No of Attempts</label>
                                                <input type="text" name="attempts" id="attempts" pattern="^[0-9+.]+$" class="form-control" placeholder="Paper attempts" autocomplete="off" required data-required-error="Paper attempt count is required." data-pattern-error="Invalid attempts. Should be a number" value="<?=$getQuestionPaper->no_of_attempts?>" />
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="table-responsive">
                                                <table class="table table-borderless">
                                                    <tbody>
                                                        <?php 
                                                            foreach ($question_type as $row) {
                                                                
                                                                switch ($row->question_type) {
                                                                    case 'MCQ':
                                                                        $values = $getQuestionPaper->no_of_mcqs != 0 ? $getQuestionPaper->no_of_mcqs : '';
                                                                        break;
                                                                    case 'STRUCTURED':
                                                                        $values = $getQuestionPaper->no_of_structured != 0 ?  $getQuestionPaper->no_of_structured : '';
                                                                        break;
                                                                    case 'ESSAY':
                                                                        $values = $getQuestionPaper->no_of_essays != 0 ? $getQuestionPaper->no_of_essays : '';
                                                                        break;
                                                                    default:
                                                                        $values = '';
                                                                        break;
                                                                }
                                                        ?>
                                                        <tr>
                                                            <td style="padding:0px;">No of <?=$row->question_type?></td>
                                                            <td style="padding:0px;">
                                                                <div class="form-group" style="margin-bottom: 0px;">
                                                                    <input type="text" name="<?=strtolower($row->question_type)?>Count" pattern="^[0-9+]+$" class="form-control question-types" value="<?=$values?>" placeholder="Number of <?=$row->question_type?> Questions" data-pattern-error="Invalid number" autocomplete="off" value="" readonly />
                                                                    <p class="help-block">
                                                                        <small>You can not edit this field.</small>
                                                                    </p>
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
                                                        <input class="custom-control-input question_from" type="checkbox" name="questions_from[]" value="<?=$row->extype_id?>" onclick="return false;" />
                                                        <span class="custom-control-indicator"></span>
                                                        <span class="custom-control-label"><?=$row->extype_name?></span>
                                                    </label>
                                                    <p class="help-block">
                                                        <small>You can not edit this field.</small>
                                                    </p>
                                                </div>
                                                <?php } ?>
                                                <h5 class="m-l-15 m-t-50">Do you want to add previous paper questions too?</h5>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="custom-control custom-control-success custom-radio">
                                                            <input class="custom-control-input" type="radio" name="previousPaperQue" value="1" required onclick="return false;" />
                                                            <span class="custom-control-indicator"></span>
                                                            <span class="custom-control-label">Yes</span>
                                                        </label>
                                                        <label class="custom-control custom-control-danger custom-radio">
                                                            <input class="custom-control-input" type="radio" name="previousPaperQue" value="0" required onclick="return false;" />
                                                            <span class="custom-control-indicator"></span>
                                                            <span class="custom-control-label">No</span>
                                                        </label>
                                                        <p class="help-block">
                                                            <small>You can not edit this field.</small>
                                                        </p>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 m-b-10">
                                            <div class="form-group">
                                                <label for="mcq_main_title" class="control-label">MCQ Main Title</label>
                                                <textarea name="mcq_main_title" id="mcq_main_title"><?=$getQuestionPaper->mcq_main_title?></textarea>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-12 m-b-10">
                                            <div class="form-group">
                                                <label for="structured_main_title" class="control-label">STRUCTURED Main Title</label>
                                                <textarea name="structured_main_title" id="structured_main_title"><?=$getQuestionPaper->structured_main_title?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12 m-b-10">
                                            <div class="form-group">
                                                <label for="essay_main_title" class="control-label">ESSAY Main Title</label>
                                                <textarea name="essay_main_title" id="essay_main_title"><?=$getQuestionPaper->essay_main_title?></textarea>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                                <div id="question-list-div"></div>
                                <button type="submit" class="btn btn-success" style="width: 100%;" id="update-btn">UPDATE PAPER</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('includes/footer'); ?>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script src="<?=base_url()?>assets/js/spartan-multi-image-picker.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.2/classic/ckeditor.js"></script>
    <script type="text/javascript">
        let mcqEditor;
        let strEditor;
        let essEditor;
        $(document).ready(function () {
            ClassicEditor
                .create( document.querySelector( '#mcq_main_title' ) )
                .then( newEditor => {
                    mcqEditor = newEditor;
                } )
                .catch( error => {
                        console.error( error );
                } );

            ClassicEditor
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
                } );

            var filteredQuestions = {}; // this object is used to store the filtered questions globally.
            $('#class_id').val('<?=$getQuestionPaper->class_id?>').trigger('change');
            let selectedQues = '<?=$getQuestionPaper->selected_ques_from?>';
            selectedQues.split(',').forEach(el => {
                $('input.question_from[value="'+el+'"]').prop('checked', true);
            });
            $('input[name="previousPaperQue"][value="<?=$getQuestionPaper->need_previous_added?>"]').prop('checked', true);

            let image = ``
            <?php if(!empty($getQuestionPaper)) { ?>
                image = '<?=$getQuestionPaper->school_logo?>';
            <?php } ?>
            if (image != null && image != '') {
                $('label.file_upload').find('img:eq(0)').before(`<a href="javascript:removeLogo('<?=$getQuestionPaper->paper_id?>')" data-spartanindexremove="4" style="right: 0px;top: 0px;background: rgb(254, 215, 0);border-radius: 3px;width: 20px;height: 20px;line-height: 20px;text-align: center;text-decoration: none;color: rgb(49, 62, 70);position: absolute !important;" id="edit-img" class="spartan_remove_row"><i class="fa fa-times"></i></a>`)
            }

            let questionList = <?=json_encode($getQuestionPaperQuestions, JSON_PRETTY_PRINT)?>; // converting php array to js object format.
            let questionListDiv = ``; // this is used for append the question div with question types and questions.
            let existingQuestions = []; // this array is for check the already selected questions.
            run_waitMe('.panel-body');
            setTimeout(() => {
                loadQuestions(filteredQuestions);
                let locallyStoredFilteredQues = localStorage.getItem('filteredQuestions'); // fetching the questions with type from localstorage.
                let seperatedQuestions = []; // this is used to seperate questions according to their question type. [MCQ, STRUCTURED, ESSAY]
                questionList.forEach(el => {
                    switch (el.question_type) {
                        case 'MCQ':
                            seperatedQuestions = JSON.parse(locallyStoredFilteredQues).mcq; // seperating questions by their question type. JSON.parse is used due to we have stored as stringify format in localstorage.
                            break;
                        case 'STRUCTURED':
                            seperatedQuestions = JSON.parse(locallyStoredFilteredQues).structured;
                            break;
                        case 'ESSAY':
                            seperatedQuestions = JSON.parse(locallyStoredFilteredQues).essay;
                            break;
                        default:
                            seperatedQuestions = [];
                            break;
                    }
                    questionListDiv += `<div class="authentication-content m-b-30 m-l-30 m-r-30 m-t-30">
                                            <h5 class="m-t-0">${el.question_type}</h5>
                                            <div class="table-responsive">
                                                <table class="table table-borderless">
                                                    <tbody class="t_body">`;
                                                        let count = 0;
                                                        if (el.questions.length > 0) {
                                                            el.questions.forEach((que, index) => {
                                                                existingQuestions.push(que.question_id); // add all existing question ids to the array declared above.
                                                                count = index+1;
                                                                questionListDiv += `<tr class="scorerrow question_div${el.qt_id}">
                                                                                        <th style="width:5%;" class="que-count">${index+1}</th>
                                                                                        <td class="text-center">
                                                                                            <div class="form-group" style="margin-bottom: 0px;">
                                                                                                <select class="form-control questions selectedQues${que.question_id}" name="${el.question_type.toLowerCase()}[]" data-plugin="select2"
                                                                                                data-placeholder="Select a question" style="width: 100%;">
                                                                                                    <option></option>`
                                                                                                    seperatedQuestions.forEach(q => {
                                                                                                        questionListDiv += `<option value="${q.que_id}">${q.question_showing}</option>`;
                                                                                                    });
                                                                            questionListDiv += `</select>
                                                                                                <div class="help-block with-errors"></div>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td style="text-align: center;">
                                                                                            <button type="button"
                                                                                                class="btn btn-sm btn-pill btn-outline-primary question-add-btn${el.qt_id} m-b-10 add-field"
                                                                                                style="display:none;"
                                                                                                onclick="add_fields(this,'question_div${el.qt_id}','question-add-btn${el.qt_id}','question-remove-btn${el.qt_id}','${el.question_type.toLowerCase()}');">
                                                                                                <i class="zmdi zmdi-plus"></i>
                                                                                            </button>
                                                                                            <button type="button"
                                                                                                class="btn btn-sm btn-pill btn-outline-danger question-remove-btn${el.qt_id} m-b-10"
                                                                                                onclick="remove_fields(this,'question_div${el.qt_id}','question-add-btn${el.qt_id}','question-remove-btn${el.qt_id}');">
                                                                                                <i class="zmdi zmdi-minus"></i>
                                                                                            </button>
                                                                                        </td>
                                                                                    </tr>`
                                                        });
                                                    }
                                    questionListDiv += `<tr class="scorerrow question_div${el.qt_id}">
                                                            <th style="width:5%;" class="que-count">${count+1}</th>
                                                            <td class="text-center">
                                                                <div class="form-group" style="margin-bottom: 0px;">
                                                                    <select class="form-control questions" name="${el.question_type.toLowerCase()}[]" data-plugin="select2"
                                                                    data-placeholder="Select a question" style="width: 100%;">
                                                                        <option></option>`
                                                                        seperatedQuestions.forEach(q => {
                                                                            questionListDiv += `<option value="${q.que_id}">${q.question_showing}</option>`;
                                                                        });
                                                questionListDiv += `</select>
                                                                    <div class="help-block with-errors"></div>
                                                                </div>
                                                            </td>
                                                            <td style="text-align: center;">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-pill btn-outline-primary question-add-btn${el.qt_id} m-b-10 add-field"
                                                                    onclick="add_fields(this,'question_div${el.qt_id}','question-add-btn${el.qt_id}','question-remove-btn${el.qt_id}','${el.question_type.toLowerCase()}');">
                                                                    <i class="zmdi zmdi-plus"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-pill btn-outline-danger question-remove-btn${el.qt_id} m-b-10"
                                                                    style="display:none;"
                                                                    onclick="remove_fields(this,'question_div${el.qt_id}','question-add-btn${el.qt_id}','question-remove-btn${el.qt_id}');">
                                                                    <i class="zmdi zmdi-minus"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
                });
                $('#question-list-div').html(questionListDiv);
                $(".questions").select2({
                    allowClear: true
                });
                // below code is used to select the questions which is already in the db.
                questionList.forEach(el => {
                    el.questions.forEach((que, index) => {
                        $('.selectedQues'+que.question_id).val(que.question_id).trigger('change');
                    });
                });
                console.log('existingQuestions',existingQuestions);
                $('.questions').attr(`onchange`, `checkQuestionAlreadySelected(this, '${existingQuestions}')`); // set the onchange function every question selection.
                $('.panel-body').waitMe('hide');
            }, 5000);
        });
        $(".our-select-2").select2({
            allowClear: true,
            disabled: 'readonly'
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
                    $('#sub_id').val('<?=$getQuestionPaper->subject_id?>').trigger('change');
                },
                error: function(result) {
                    toastr.error('Error :'+result)
                }
            });
        });

        $("#imageupdiv").spartanMultiImagePicker({
            fieldName:'fileUpload',
            maxCount:1,
            rowHeight:'60px',
            maxFileSize:5500000,
            allowedExt:'jpg|jpeg|png|svg',
            dropFileLabel:   'Drop logo here',
            groupClassName : 'col-md-4 col-sm-4 col-xs-6',
            placeholderImage: {image:'<?=base_url();?>'+$('#school_logo').val() ,width: '60%'},
                onRenderedPreview : function(index){
            },

            onExtensionErr : function(index, file){
                toastr["error"]('Please only input png or jpg type file');
            },
            onSizeErr : function(index, file){
                toastr["error"]('This file exceeds the max size(5MB)');
            }
        });

        function removeLogo(id) {
            $.ajax({
            type: "POST",
            url: "<?=base_url()?>remove-school-logo",
            data: 'paper_id='+id,
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

        add_fields = function (elms, dvclass, adclass, rmclass, questionType) {
            var numItems = $('.' + dvclass).length;
            const questionTypeCount = $(`input[name="${questionType}Count"]`).val();
            if (numItems >= questionTypeCount) {
                return toastr.error(`Unable to add field more than given ${questionType} question count.`);
            }

            $('.' + dvclass + ':last').find('.questions').select2('destroy');
            var ele = $(elms).closest('.' + dvclass).clone(true);
            $(elms).closest('.' + dvclass).after(ele);
            $(elms).closest('.' + dvclass).find('.' + rmclass).css({
                "display": "inline-block"
            });
            $(elms).css('display', 'none');
            $('.' + dvclass).last().find('.' + rmclass).css({
                "display": "inline-block"
            });
            $('.'+dvclass+':last th.que-count').html(numItems+1);
            $('.questions').select2({allowClear: true});
            $('.' + dvclass).last().find('.' + rmclass).css({
                "display": "inline-block"
            });
        }

        remove_fields = function (elms, dvclass, adclass, rmclass) {
            $(elms).closest('.' + dvclass).remove();
            $('.' + dvclass).last().find('.' + adclass).css({
                "display": "inline-block"
            });
            $('.' + dvclass).first().find('.' + rmclass).css({
                "display": "none"
            });
            var numItems = $('.' + dvclass).length;
            $('.'+dvclass+':last th.que-count').html(numItems);
        }

        const loadQuestions = (filteredQuestions=[]) => {
            const classId = $('#class_id option:selected').val();
            const subjectId = $('#sub_id option:selected').val();
            const termIds = [];
            $('input:checkbox[name="questions_from[]"]:checked').each(function(){
                termIds.push($(this).val());
            });
            const dataSet = {
                classId: classId,
                subjectId: subjectId,
                termIds: termIds
            }
            $.ajax({
                type: "POST",
                url: '<?=base_url()?>filter-questions',
                data: dataSet,
                success: function (result) {
                    const resp = $.parseJSON(result);
                    filteredQuestions = resp;
                    localStorage.setItem('filteredQuestions', JSON.stringify(filteredQuestions))
                },
                error: function (result) {}
            });
        }

        const checkQuestionAlreadySelected = (el, existQues=[]) => {
            const makeArr = existQues.split(',');
            const selectedVal = $(el).val();
            if(makeArr.includes(selectedVal)) {
                return toastr.error('This question is already selected in this question paper.') 
            };
        }

        $('#inputmasks').validator().on('submit', function (e) {
            if (!(e.isDefaultPrevented())) {
                e.preventDefault();
                $('#update-btn').text('UPDATING...').attr('disabled','disabled');
                var form_data = new FormData(this);
                form_data.append('mcq_main_title', mcqEditor.getData());
                /* form_data.append('structured_main_title', strEditor.getData());
                form_data.append('essay_main_title', essEditor.getData()); */
                $.ajax({
                    type: 'POST',
                    url: '<?=base_url()?>update-exam-paper',
                    data: form_data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(result) {
                        var resp = $.parseJSON(result);
                        if (resp.status == 'success') {
                            toastr.success(resp.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(resp.message)
                        }
                    },
                    error: function (result) {}
                });
            }
        });

    </script>
</body>

</html>