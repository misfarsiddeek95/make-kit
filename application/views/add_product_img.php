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
              <a type="button" class="btn btn-outline-primary m-w-120" <?php if (!empty($back)) { echo "onclick='editSubProducts(".$back->pro_id.");'";}else{echo 'href="'.base_url().'products/view_products"';}?>>Back to View</a>
            </div>
            <h3 class="m-y-0">Add Product Images</h3>
          </div>
          <div class="panel-body">
            
            <form action="<?=base_url()?>upload_pro_img" class="dropzone" id="myDropzone">
              <input type="hidden" name="product_table" id="product_table" value="<?=$product_table?>">
              <input type="hidden" name="product_id" id="product_id" value="<?=$product_id?>">
              <div class="dz-message" data-dz-message="">
                <div class="dz-icon">
                  <i class="zmdi zmdi-upload"></i>
                </div>
                <h2>Drop files here or click to upload</h2>
                <span class="text-muted">(please select a valid jpg or jpeg photo for upload.)</span>
              </div>
            </form>

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
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        load_images();
      });

      $( function() {
        $("#sortable").sortable();
      });


      $("#sortable").on( "sortdeactivate", function( event, ui ) {
        var pp_order = "";
        $( ".sortable_div" ).each(function() {
          pp_order += $(this).attr("ppid")+",";
        });
        $("#photo_order").val(pp_order);
        change_image_order();
      });

      $("#myDropzone").dropzone({ 
      	acceptedFiles: 'image/*',
        url: "<?=base_url()?>upload_pro_img", 
        success : function(file, response){
          load_images();
        }
      });

      function load_images() {
        $.ajax({
          type: "POST",
          url: "<?=base_url();?>getSpecProImg",
          data: 'product_id='+ $('#product_id').val()+'&product_table='+ $('#product_table').val(),
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
                      '<img src="<?=base_url();?>photos/products/'+responsedata[i].photo_path+'-std.'+responsedata[i].extension+'" alt="'+responsedata[i].photo_title+'" />'+
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
                  url: "<?=base_url();?>deleteProImg",
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

      function editSubProducts(id) {
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "<?=base_url()?>edit_sub_products");

        hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "product_id");
        hiddenField.setAttribute("value", id);
        form.appendChild(hiddenField);

        document.body.appendChild(form);
        form.submit();
      }
      
    </script>

  </body>
</html>