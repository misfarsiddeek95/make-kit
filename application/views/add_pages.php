﻿<!DOCTYPE html>
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
            <h3 class="m-y-0"><?=$type?> Page</h3>
          </div>
          <div class="panel-body">

            <div class="row">
              <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <form data-toggle="validator" id="inputmasks">

                  <input type="hidden" name="page_id" id="page_id" value="<?php if(!(empty($page))){echo($page->page_id);}else{echo(0);} ?>">

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Page For</label>
                    <select class="form-control" name="page_for" id="page_for" data-required-error="Page For is Required" required>
                      <option selected disabled>--Select For the Page--</option>
                      <option value="create_new">Add New</option>
                      <?php 
                        foreach ($page_for as $row) {
                          $sel = '';
                          if(!(empty($page))){
                            if ($row->page_for==$page->page_for) {
                              $sel = 'selected';
                            }
                          }
                      ?>
                      <option value="<?=$row->page_for?>" <?=$sel?>><?=$row->page_for?></option>
                      <?php } ?>
                    </select>
                    <div class="help-block with-errors"></div>
                  </div>
                  <div class="form-group" id="new_page_fors">
                    <input type="text" pattern="^[a-zA-Z 0-9 .&+-]*$" value="" placeholder="Add New" id="new_page_for" name="new_page_for" class="form-control" data-pattern-error="Invalid Title">
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Page Name</label>
                    <input type="text" pattern="^[a-zA-Z 0-9 .&+-]*$" value="<?php if(!(empty($page))){echo($page->name);} ?>" placeholder="Page Name" id="pageName" name="pageName" class="form-control" data-minlength="3" data-pattern-error="Invalid Page Name" data-error="Minimum of 3 characters" data-required-error="Page Name is Required" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Page Type</label>
                    <select class="form-control" name="page_type" id="page_type" data-plugin="select2" data-required-error="Page Type is Required" required>
                      <?php 
                        $ptype = array('Main Page','Slider','Banner','Gallery');
                        foreach ($ptype as $key => $value) {
                          $sel = '';
                          if(!(empty($page))){
                            if ($key==$page->page_type) {
                              $sel = 'selected';
                            }
                          }
                      ?>
                      <option value="<?=$key?>" <?=$sel?>><?=$value?></option>
                      <?php } ?>
                    </select>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Page Title</label>
                    <input type="text" pattern="^[a-zA-Z 0-9 .&+-]*$" value="<?php if(!(empty($page))){echo($page->page_title);} ?>" placeholder="Page Title" id="pageTitle" name="pageTitle" class="form-control" data-minlength="3" data-pattern-error="Invalid Page Title" data-error="Minimum of 3 characters" data-required-error="Page Title is Required" required>
                    <div class="help-block with-errors"></div>
                  </div>
                  
                  <div class="form-group">
                    <label for="form-control-3" class="control-label">SEO Title</label>
                    <input type="text" value="<?php if(!(empty($page))){echo($page->seo_title);} ?>" placeholder="SEO Title" id="seoTitle" name="seoTitle" class="form-control">
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">SEO Keywords</label>
                    <input type="text" pattern="^[a-zA-Z 0-9,]+$" value="<?php if(!(empty($page))){echo($page->seo_keywords);} ?>" placeholder="SEO Keywords" id="seoKeywords" name="seoKeywords" class="form-control">
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">SEO Description</label>
                    <input type="text" value="<?php if(!(empty($page))){echo($page->seo_description);} ?>" placeholder="SEO Description" id="seoDescription" name="seoDescription" class="form-control">
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">SEO url</label>
                    <input id="form-control-7" class="form-control" type="text" data-inputmask="'alias': 'url'" value="<?php if(!(empty($page))){echo($page->seo_url);} ?>" placeholder="SEO url" id="seoUrl" name="seoUrl">
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Headline</label>
                    <input type="text" pattern="^[a-zA-Z 0-9 .&+-]*$" value="<?php if(!(empty($page))){echo($page->headline);} ?>" placeholder="Headline" id="headline" name="headline" class="form-control" data-minlength="3" data-pattern-error="Invalid Headline" data-error="Minimum of 3 characters" data-required-error="Headline is Required" required>
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Second Title</label>
                    <input type="text" pattern="^[a-zA-Z0-9.@ -.,]*$" value="<?php if(!(empty($page))){echo($page->second_title);} ?>" placeholder="Second Title" id="secondTitle" name="secondTitle" class="form-control" data-minlength="3" data-pattern-error="Invalid Second Title" data-error="Minimum of 3 characters">
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    <label for="form-control-3" class="control-label">Page Text</label>
                    <textarea id="pageText" name="pageText" class="form-control" ><?php if(!(empty($page))){echo($page->page_text);}?></textarea>
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
    <script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script src="<?=base_url()?>assets/js/ckeditor.js"></script>
      <script type="text/javascript">
        // initialize ckeditor
        let pageText;
        ClassicEditor
        .create( document.querySelector( '#pageText' ) )
        .then( newEditor => {
            pageText = newEditor;
        } )
        .catch( error => {
            console.error( error );
        } );

      </script>
      <script type="text/javascript">

        $(document).ready(function() {
          $('#new_page_fors').hide()
        })

        $('#page_for').on('change',function() {
          if (this.value == 'create_new') {
            $('#new_page_fors').show()
          }else{
            $('#new_page_fors').hide()
          }
        })
      
        $('#inputmasks').validator().on('submit', function (e) {
          if (!(e.isDefaultPrevented())) {
            e.preventDefault();
            run_waitMe('#inputmasks');
              $('#pageText').html(pageText.getData());
              $.ajax({
                type: "POST",
                url: "<?=base_url()?>savePage",
                data: $('#inputmasks').serialize(),
                success: function(result) {
                  var responsedata = $.parseJSON(result);
                  if(responsedata.status=='success'){
                    if (responsedata.message=='edit') {
                      toastr.success("Page updated successfully.")
                      setTimeout(function(){
                        window.location = "<?=base_url()?>Settings/pages";
                      }, 500);
                    }else{
                      setTimeout(function(){
                       location.reload();
                      }, 500);
                      toastr.success("Page Added successfully.")
                    }
                  }else if(responsedata.status=='error'){
                    toastr.error(responsedata.message)
                  }else{
                    toastr.error("Somthing went wrong :(")
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
      
    </script>

  </body>
</html>