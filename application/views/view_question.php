<!DOCTYPE html>
<html lang="en">
  
<!-- Mirrored from big-bang-studio.com/cosmos/profile.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 04 Aug 2017 11:52:50 GMT -->
<head>
    <?php $this->load->view('includes/head'); ?>
    <style type="text/css">
      .table .table{
        background-color: unset;
      }
      .product .p-colors label{
        min-height: 18px;
        min-width: 18px;
      }
      .p-text h4{
        line-height: 0;
      }
    </style>
  </head>
  <body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
      <?php $this->load->view('includes/sidebar'); ?>
      <div class="site-content">
        <div class="panel panel-default m-b-0">
          <div class="panel-heading">
            <h3 class="m-y-0 d-inline">Question</h3>
            <button class="btn btn-outline-warning btn-pill pull-right m-y-0 d-inline" onclick="location.href='<?=base_url()?>Questionnaire/questions/137'"><i class="zmdi zmdi-arrow-left"></i></button>
          </div>
          <div class="panel-body">
            <?php if($question_detail->answer_method == 'single' || $question_detail->answer_method == 'multiple') { ?>
            <div class="product">
              <div class="row">
                <?php if($question_detail->has_img == 1){ ?>
                <div class="col-sm-4">
                  <div class="p-images m-b-30 m-sm-b-0">
                    <div class="m-b-20">
                      <a href="javascript:void(0);">
                        <img src="<?=base_url()?>photos/questionaire/<?=$question_detail->que_pic?>">
                      </a>
                    </div>
                  </div>
                </div>
                <?php } ?>
                <div class="col-sm-8">
                  <div class="p-title">
                    <h3 class="m-y-0"><?=$question_detail->question?></h3> x
                  </div>
                  <div class="p-text">
                    <?php 
                      foreach ($question_detail->answers as $ans) { 
                        if($ans->correct_answer == 1){
                          $cls = 'success';
                        }else{
                          $cls = 'dark';
                        }
                        if ($question_detail->answer_method == 'single') {
                          $border_radius = 'style="border-radius:10px"';
                        }else if ($question_detail->answer_method == 'multiple'){
                          $border_radius = '';
                        }
                        if ($ans->has_img == 1) {
                          $hr = '<hr/>';
                        }else{
                          $hr = '';
                        }
                    ?>
                    <div class="row gutter-sm">
                      <div class="col-sm-1">
                        <div class="p-colors m-b-30">
                          <label class="bg-<?=$cls?> m-b-5 m-r-5" <?=$border_radius?>>
                            <span class="pc-indicator">
                            </span>
                          </label>
                        </div>
                      </div>
                      <?php if($ans->answer != ''){ ?>
                      <div class="col-sm-8">
                        <h4><?=$ans->answer?></h4>
                      </div>
                      <?php } ?>
                      <?php if($ans->has_img == 1){ ?>
                      <div class="col-sm-3">
                        <div class="p-images m-b-30 m-sm-b-0">
                          <div class="m-b-20">
                            <a href="javascript:void(0);">
                              <img src="<?=base_url()?>photos/questionaire/<?=$ans->ans_pic?>" width="100" height="100">
                            </a>
                          </div>
                        </div>
                      </div>
                      <?php } ?>
                    </div>
                    <?=$hr?>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <?php } else if ($question_detail->answer_method == 'smallbox') { ?>
              <div class="product">
                <div class="row">
                  <?php if($question_detail->has_img == 1){ ?>
                  <div class="col-sm-4">
                    <div class="p-images m-b-30 m-sm-b-0">
                      <div class="m-b-20">
                        <a href="javascript:void(0);">
                          <img src="<?=base_url()?>photos/questionaire/<?=$question_detail->que_pic?>">
                        </a>
                      </div>
                    </div>
                  </div>
                  <?php } ?>
                  <div class="col-sm-8">
                    <div class="p-title">
                      <h3 class="m-y-0"><?=$question_detail->question?></h3>
                    </div>
                    <div class="p-text">
                      <?php foreach ($question_detail->answers as $ans) { ?>
                      <div class="row gutter-sm">
                        <div class="col-sm-1">
                          <div class="p-colors m-b-30">
                            <label class="bg-success m-b-5 m-r-5">
                              <span class="pc-indicator">
                              </span>
                            </label>
                          </div>
                        </div>
                        <?php if($ans->answer != ''){ ?>
                        <div class="col-sm-8">
                          <h4><?=$ans->answer?></h4>
                        </div>
                        <?php } ?>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php } else if ($question_detail->answer_method == 'textbox') { ?>
              <div class="product">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="p-title">
                      <?=$question_detail->question?>
                    </div>
                    <div class="p-text">
                      <?php 
                        foreach ($question_detail->answers as $ans) { 
                          if($ans->answer != ''){
                      ?>
                      <div class="row gutter-sm">
                        <div class="col-sm-1">
                          <div class="p-colors m-b-30">
                            <label class="bg-success m-b-5 m-r-5">
                              <span class="pc-indicator">
                              </span>
                            </label>
                          </div>
                        </div>
                        <div class="col-sm-8">
                          <?=$ans->answer?>
                        </div>
                      </div>
                      <?php }} ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
          <div class="panel-footer text-right">
            <button type="button" class="btn btn-primary btn-labeled" onclick="editQuestion('<?=$question_detail->que_id?>');">Edit
              <span class="btn-label btn-label-right p-x-10">
                <i class="zmdi zmdi-edit"></i>
              </span>
            </button>
            <button type="button" class="btn btn-warning btn-labeled" onclick="location.href='<?=base_url()?>Questionnaire/questions/137'">Back
              <span class="btn-label btn-label-right p-x-10">
                <i class="zmdi zmdi-arrow-left"></i>
              </span>
            </button>
          </div>
        </div>
      </div>
      <?php $this->load->view('includes/footer'); ?>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script>
      function goBack() {
        window.history.back();
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
    </script>
  </body>
</html>