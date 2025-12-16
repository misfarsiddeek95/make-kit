<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('includes/head'); ?>
    <style>
        .panel-group .panel{
            border: 1px dashed #288140;
        }
        #generate-div p {
            font-size: 15px;
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
                    <h3 class="m-y-0 d-inline">צפייה במבחן</h3>
                    <a class="btn btn-outline-warning btn-pill pull-right m-y-0 d-inline" href="<?=base_url()?>Questionnaire/generateQuestionPaper/274"><i class="zmdi zmdi-arrow-left"></i></a>
                </div>
                <div class="panel-body">
                    <div class="row m-5" id="generate-div">
                        <div class="col-md-12 col-sm-12"> 
                            <div class="authentication-content m-b-30 m-l-30 m-r-30 m-t-30 p-t-10 p-b-0">
                                <div class="row">
                                    <div class="col-md-4 col-sm-12" >
                                        <?php
                                            $schoolLogo = 'assets/img/hat.png';
                                            if ($paper_detail['school_logo'] != null && $paper_detail['school_logo'] != '') {
                                                $schoolLogo = $paper_detail['school_logo'];
                                            }
                                        ?>
                                        <img src="<?=base_url()?><?=$schoolLogo?>" alt="School Logo" height="100" width="100" class="img-rounded">
                                        <div class="m-t-50">
                                            <h4>שם / מספר סידורי: ...........................</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 text-center text-uppercase">
                                        <h2><?=$paper_detail['school_name']?></h2>
                                        <h3><?=$paper_detail['subject_name']?></h3>
                                        <h3><?=$paper_detail['extype_name']?></h3>
                                        <h4><?=$paper_detail['class_name']?></h4>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="img-rounded pull-right" style="width:50%;">
                                            <table class="table table-bordered text-center">
                                                <tr><td class="p-y-40"></td></tr>
                                                <tr><td class="h4">ניקוד (<?=number_format($paper_detail['total_marks_count'],0)?>)</td></tr>
                                            </table>
                                            <h4 class="text-center font-italic"><?=str_pad(number_format($paper_detail['paper_duration'],0), 2, '0', STR_PAD_LEFT)?> שעות</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($paper_detail['mcq_ques_ans'])) { ?>
                        <div class="row m-x-30 m-b-20">
                            <div class="col-md-12">
                                <?=$paper_detail['mcq_main_title']?>
                            </div>
                        </div>
                        <?php 
                            foreach ($paper_detail['mcq_ques_ans'] as $mcqIndex => $mcqs) {
                                $mcqQueClass = 'col-md-12';
                                $mcqQueImage = '';
                                if ($mcqs->queHasImg == 1) {
                                    $mcqQueImage = '<img src="'.base_url().'photos/questionaire/'.$mcqs->questionImage.'" alt="Question Image" height="120" width="120" class="img-rounded m-l-30"/>';
                                }
                                $mcqAnsClass = 'col-md-3';
                                if (in_array(1, $mcqs->answerHasImgs)) {
                                    $mcqAnsClass = 'col-md-6';
                                }
                        ?>
                        <div class="row m-x-30 m-b-20">
                            <p class="<?=$mcqQueClass?>"><?=$mcqIndex+1?>) <?=$mcqs->question?> <?=$mcqQueImage?></p>
                            <?php
                                foreach ($mcqs->answers as $ansIndex => $ans) {
                                    $mcqAnsImage = '';
                                    if ($ans->answerImage != '' && $ans->answerImage != null) {
                                        $mcqAnsImage = '<img src="'.base_url().'photos/questionaire/'.$ans->answerImage.'" alt="Answer Image" height="120" width="120" class="img-rounded m-l-30"/>';
                                    }
                            ?>
                                <p class="<?=$mcqAnsClass?> p-l-30"><?=$ansIndex+1?>. <?=$ans->answer?> <?=$mcqAnsImage?></p>
                            <?php } ?>
                        </div>
                        <?php }} ?>
                        <?php if (!empty($paper_detail['structured_ques_ans'])) { ?>
                            <div class="row m-x-30 m-b-20">
                                <div class="col-md-12">
                                    <?=$paper_detail['structured_main_title']?>
                                </div>
                            </div>
                            <?php 
                                foreach ($paper_detail['structured_ques_ans'] as $strIndex => $strucs) {
                                    $strQueImage = '';
                                    if ($strucs->queHasImg == 1) {
                                        $strQueImage = '<img src="'.base_url().'photos/questionaire/'.$strucs->questionImage.'" alt="Question Image" height="120" width="120" class="img-rounded m-l-30"/>';
                                    }
                                    $strQuestion = '<p>'.$strucs->question.' '.$strQueImage.'</p>';
                                    if ($strucs->answer_method == 'textbox') {
                                        $strQuestion = $strucs->question.' '.$strQueImage;
                                    }
                            ?>
                            <div class="row m-x-30 m-b-20">
                                <div class="col-md-1" style="width:0.3%;"><p><?=($strIndex+1)?>)</p></div><div class="col-md-11"><?=$strQuestion?></div>
                                <?php for ($i = 1; $i <= count($strucs->answers); $i++) { ?>
                                    <p class="col-md-12 p-l-30"><?=$i?>. <?=str_repeat(".",280)?></p>
                                <?php } ?>
                            </div>
                        <?php }} ?>
                        <?php if (!empty($paper_detail['essay_ques_ans'])) { ?>
                        <div class="row m-x-30 m-b-20">
                            <div class="col-md-12">
                                <?=$paper_detail['essay_main_title']?>
                            </div>
                        </div>
                        <?php foreach ($paper_detail['essay_ques_ans'] as $essIndex => $essay) { ?>
                        <div class="row m-x-30 m-b-20">
                            <div class="col-md-1" style="width:0.3%;"><p><?=($essIndex+1)?>)</p></div><div class="col-md-11"><?=$essay->question?></div>
                        </div>
                        <?php }} ?>
                        <div class="row">
                            <div class="col-md-12 text-right p-x-30">
                                <hr>
                                <div class="ladda-progress">
                                    <?php if($download_paper) { ?>
                                        <a class="btn btn-danger ladda-button" data-style="expand-left" href="<?=base_url()?>download-exam-paper/<?=base64_encode($paper_detail['paper_id'])?>/download_paper">
                                            <span class="ladda-label">הורד מבחן</span>
                                            <span class="ladda-spinner"></span><div class="ladda-progress" style="width: 91px;"></div>
                                        </a>
                                    <?php } if($download_scheme) { ?>
                                        <a class="btn btn-warning ladda-button" data-style="expand-left" href="<?=base_url()?>download-exam-paper/<?=base64_encode($paper_detail['paper_id'])?>/download_scheme">
                                            <span class="ladda-label">הורד מחוון</span>
                                            <span class="ladda-spinner"></span><div class="ladda-progress" style="width: 91px;"></div>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('includes/footer'); ?>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script src="<?=base_url()?>assets/js/ui-buttons.js"></script>
    <script type="text/javascript">
    </script>
</body>

</html>