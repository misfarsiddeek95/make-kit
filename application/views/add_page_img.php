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
    .img_main_div .sortable_div img{
      width: 100%;
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
            
            <!-- <form action="<?=base_url()?>upload_page_img" class="dropzone" id="myDropzone">
              <input type="hidden" name="page_id" id="page_id" value="<?=$page_id?>">
              <div class="dz-message" data-dz-message="">
                <div class="dz-icon">
                  <i class="zmdi zmdi-upload"></i>
                </div>
                <h2>Drop files here or click to upload</h2>
                <span class="text-muted">(please select a valid jpg or jpeg photo for upload.)</span>
              </div>
            </form> -->


            <div class="row">
              <div class="col-md-2">
                <form action="<?=base_url()?>upload_page_img" class="dropzone" id="myDropzone">
                  <input type="hidden" name="page_id" id="page_id" value="<?=$page_id?>">
                  <input type="hidden" name="image_type" id="image_type" value="">
                  <input type="hidden" name="img_val" id="img_val" value="">
                  <div class="dz-default dz-message">
                    <span class="text-muted">Drop brand image here</span>
                  </div>
                </form>
              </div>
              <form data-toggle="validator" id="inputMask">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="form-control-2" class="control-label" style="display: block;">Type</label>
                    <div class="btn-group" data-toggle="buttons">
                      <label class="btn btn-outline-primary active">
                        <input type="radio" name="type" id="description" value="0" autocomplete="off" checked="checked"> Description
                      </label>
                      <label class="btn btn-outline-primary">
                        <input type="radio" name="type" id="category" value="1" autocomplete="off"> Category
                      </label>
                      <label class="btn btn-outline-primary">
                        <input type="radio" name="type" id="product" value="2" autocomplete="off"> Product
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group" id="apndOption">
                    <label for="form-control-2" class="control-label" id="typeLable">Description</label>
                    <input type="text" class="form-control" id="descriptions" name="typeval" placeholder="Description">

                    <select class="form-control" name="typeval" id="categories" data-plugin="select2" data-required-error="Category is Required" disabled="disabled">
                      <option></option>
                      <?php
                        function write_with_child($category) {
                            $arr = explode("|",$category->tree_path);
                            $depth = count($arr)-1;
                            $val_str = "";
                            for ($i=0; $i <$depth ; $i++) { 
                              $val_str ="&#160;&#160;". $val_str;
                            }
                            $val_str = $val_str.$category->category;
                            if (isset($category->sub_cat) && sizeof($category->sub_cat) > 0) {
                                ?>

                                <option value="<?=$category->cate_id?>"><?=$val_str?></option>
                                        <?php foreach ($category->sub_cat as $child_cat) { ?>
                                            <?php write_with_child($child_cat); ?>
                                        <?php } ?>
                            <?php } else { ?>
                                <option value="<?=$category->cate_id?>"><?=$val_str?></option>
                                <?php
                            }
                        }
                        
                        foreach ($categories as $cate) {
                            write_with_child($cate);
                        }
                      ?>
                    </select>

                    <select class="form-control" name="typeval" id="products" data-plugin="select2" data-required-error="Product is Required" disabled="disabled">
                      <option></option>
                      <?php foreach ($products as $row) {?>
                      <option value="<?=$row->pro_id?>"><?=$row->name?></option>
                      <?php } ?>
                    </select>
                    <div class="help-block with-errors"></div>
                  </div>
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-primary" style="width: 100%;margin-top: 25px;" id="submitBtn">Submit</button>
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
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $('#categories, #products').next(".select2-container").hide();
        load_images();
      });

      $( function() {
        $("#sortable").sortable();
      });

      $("#myDropzone").dropzone({
        acceptedFiles: 'image/*',
        autoProcessQueue: false,
        uploadMultiple: false,
        addRemoveLinks: true,
        maxFiles: 1,
        url: "<?=base_url()?>upload_page_img",
        success : function(file, response){
          setTimeout(function(){
            location.reload();
          }, 100);
        }
      });

      $("#categories").select2({
        placeholder: "Select a Category"
      });

      $("#products").select2({
        placeholder: "Select a product"
      });

      $("#sortable").on( "sortdeactivate", function( event, ui ) {
        var pp_order = "";
        $( ".sortable_div" ).each(function() {
          pp_order += $(this).attr("ppid")+",";
        });
        $("#photo_order").val(pp_order);
        change_image_order();
      });

      $('input:radio[name="type"]').change(function(){
        if (this.checked) {
          $('#typeLable').text($(this).parent('label').text());
          $('#typeval').remove();
          if (this.value==1) {
            $('#products, #descriptions').prop('disabled', 'disabled');
            $('#products').next(".select2-container").hide();
            $('#categories').attr('required', true);
            $('#products').attr('required', false);
            $('#descriptions').hide();
            $('#categories').prop('disabled', false);
            $('#categories').next(".select2-container").show();
          }else if(this.value==2){
            $('#categories, #descriptions').prop('disabled', 'disabled');
            $('#categories').next(".select2-container").hide();
            $('#products').attr('required', true);
            $('#categories').attr('required', false);
            $('#descriptions').hide();
            $('#products').prop('disabled', false);
            $('#products').next(".select2-container").show();
          }else{
            $('#categories, #products').prop('disabled', 'disabled');
            $('#categories, #products').next(".select2-container").hide();
            $('#categories,#products').attr('required', false);
            $('#descriptions').prop('disabled', false);
            $('#descriptions').show();
          }
        }
      });

      $('#inputMask').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          var myDropzone = Dropzone.forElement(".dropzone");

          if (myDropzone.getQueuedFiles().length > 0 || myDropzone.files.length > 0) {
            var type = $("input:radio[name='type']:checked").val();
            var typeVal = $( "[name='typeval']:enabled" ).val();
            $('#image_type').val(type);
            $('#img_val').val(typeVal);
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
              images+='<div class="col-md-2 sortable_div" ppid="'+responsedata[i].pid+'" pporder="'+responsedata[i].photo_order+'">'+
                      '<img src="<?=base_url();?>photos/pages/'+responsedata[i].photo_path+'-org.jpg" alt="'+responsedata[i].photo_title+'" />'+
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