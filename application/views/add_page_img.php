<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $this->load->view('includes/head'); ?>

    <style type="text/css">
    .img_main_div .delete_img {
      display: block;
      color: #FFF;
      background: rgba(0, 0, 0, 0.4);
      width: 30px;
      height: 30px;
      top: 0;
      position: absolute;
      text-align: center;
      line-height: 30px;
      opacity: 0;
      transition: opacity .35s ease;
    }
    .img_main_div .sortable_div:hover .delete_img {
      opacity: 1;
    }
    .img_main_div{
      cursor: move;
    }
    .img_main_div .sortable_div{
      padding-left: 0;
    }
    .img_main_div .sortable_div img{
      width: 100%;
      height: 170px;
      object-fit: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }
    .dz-preview.dz-processing.dz-image-preview.dz-complete{
      display: table;
      margin: 0 auto;
      background-color: transparent;
      width: 100%;
    }
    #myDropzone{
      background-color: transparent;
      display: table;
      margin: 0 auto;
      padding: 5px;
    }
    .dz-default.dz-message{
      padding: 0;
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
              <a type="button" class="btn btn-outline-primary m-w-120" href="<?=base_url()?>Settings/pages">Back to View</a>
            </div>
            <h3 class="m-y-0">Add Page Images</h3>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col-md-2">
                <form action="<?=base_url()?>upload_page_img" class="dropzone" id="myDropzone">
                  <input type="hidden" name="page_id" id="page_id" value="<?=$page_id?>">
                  <input type="hidden" name="img_headone" id="img_headone" value="">
                  <input type="hidden" name="img_headtwo" id="img_headtwo" value="">
                  <input type="hidden" name="img_desc" id="img_desc" value=""> 
                  <div class="dz-default dz-message">
                    <span class="text-muted">Drop page image here</span>
                  </div>
                </form>
              </div>
              <form data-toggle="validator" id="inputMask">
                <div class="col-md-5">
                  <div class="form-group">
                    <label for="form-control-2" class="control-label" id="slh1">Header One</label>
                    <input type="text" class="form-control" id="slheadone" name="headerone" placeholder="Header One">
                  </div>
                  <div class="form-group">
                    <label for="form-control-2" class="control-label" id="slh">Header Two</label>
                    <input type="text" class="form-control" id="slheadtwo" name="headertwo" placeholder="Header Two">
                  </div>
                </div>
                <div class="col-md-5">
                  
                  <div class="form-group">
                    <label for="form-control-2" class="control-label" id="sl">Description</label>
                    <textarea class="form-control" name="sliderdesc" id="sliderdesc" placeholder="Description" style="height: 110px;"></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary" style="width: 100%;" id="submitBtn">Submit</button>
                </div>
              </form>
            </div> 
          </div>
        </div>

        <div class="panel panel-default" id="imgPanelID">

          <div class="panel-body">
            <div class="img_main_div" id="sortable">

            </div>
            <input type="hidden" id="photo_order">

          </div>
        </div>

      </div>
      <?php $this->load->view('includes/footer'); ?>

    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-form-masks.js"></script>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
  	<script src="<?=base_url()?>assets/js/jquery-ui.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        load_images();
      });

      $( function() {
        $("#sortable").sortable();
      });

      var dropzoneOptions = {
        url: "<?= base_url() ?>upload_page_img",
        success: function(file, response) {
          setTimeout(function() {
            // location.reload();
            load_images();
          }, 100);
        }
      };

      <?php if (($pages->page_type != null || $pages->page_type != '') && $pages->page_type != 3) { ?>
        dropzoneOptions.acceptedFiles = 'image/jpeg,image/png';
        dropzoneOptions.autoProcessQueue = false;
        dropzoneOptions.uploadMultiple = false;
        dropzoneOptions.addRemoveLinks = true;
        dropzoneOptions.maxFiles = 1;
      <?php } ?>

      $("#myDropzone").dropzone(dropzoneOptions);

      $("#sortable").on( "sortdeactivate", function( event, ui ) {
        var pp_order = "";
        $( ".sortable_div" ).each(function() {
          pp_order += $(this).attr("ppid")+",";
        });
        $("#photo_order").val(pp_order);
        change_image_order();
      });

      $('#inputMask').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          var myDropzone = Dropzone.forElement(".dropzone");

          if (myDropzone.getQueuedFiles().length > 0 || myDropzone.files.length > 0) {
 
            var headone = $( "#slheadone" ).val();
            var headtwo = $( "#slheadtwo" ).val();
            var desc = $( "#sliderdesc" ).val();
            $('#img_headone').val(headone);
            $('#img_headtwo').val(headtwo);
            $('#img_desc').val(desc);
   
            myDropzone.processQueue();
          }else{
            toastr.error("Please add a page image")
          }
        }
      });

      function load_images() {
        $.ajax({
          type: "POST",
          url: "<?=base_url();?>getSpecPageImg",
          data: 'page_id='+ $('#page_id').val(),
          success: function(result) {
            var responsedata = $.parseJSON(result);
            if (responsedata.length==0) {
              $('#imgPanelID').hide();
            }else{
              $('#imgPanelID').show();
            }
            $("#sortable").empty();
            var images = "";
            for (var i = 0; i < responsedata.length; i++) {
              const currentImage = responsedata[i].extension != 'png' ? responsedata[i].photo_path+'-std.jpg' : responsedata[i].photo_path+'-org.'+responsedata[i].extension;
              images+='<div class="col-md-2 sortable_div" ppid="'+responsedata[i].pid+'" pporder="'+responsedata[i].photo_order+'">'+
                      '<img src="<?=base_url();?>photos/pages/'+currentImage+'" alt="'+responsedata[i].photo_title+'" />'+
                      '<a class="delete_img" href="javascript:delete_image('+responsedata[i].pid+');"><i class="zmdi zmdi-close"></i></a></div>';
            }
            $("#sortable").append(images);
            $( "#sortable" ).sortable();
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }

      function delete_image(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you need to delete this photo?',
        {
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url();?>deletePageImg",
                  data: 'id='+id,
                  success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=="success") {
                      toastr.success(responsedata.message)
                    } else {
                      toastr.error(responsedata.message)
                    }
                    load_images();
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

      function change_image_order() {
        var ids = $('#photo_order').val();
        ids = ids.slice(0, -1);
        $.ajax({
          type: "POST",
          url: "<?=base_url();?>changePhotoOrder",
          data: 'ppo_ids='+ ids,
          success: function(result) {

          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }
      
    </script>

  </body>
</html>