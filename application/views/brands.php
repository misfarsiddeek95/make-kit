<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $this->load->view('includes/head'); ?>
    <style type="text/css">
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
      <div class="panel panel-default panel-table">
        <div class="panel-heading">
          <?php if($add_brands){?>
          <div class="panel-tools">
            <button type="button" class="btn btn-outline-success btn-pill" data-toggle="modal" data-target="#otherModal3" title="Add"  onclick="addBrands();"><i class="zmdi zmdi-plus"></i></button>
          </div>
          <?php }?>
          <h3 class="m-t-0 m-b-5">Brands</h3>
        </div>
        <div class="panel-body">
          <div class="table-responsive m-y-5">
            <table class="table table-hover" id="table-1">
              <thead>
                <tr>
                  <th></th>
                  <th>Brand</th>
                  <th>View Count</th>
                  <?php if($edit_brands || $delete_brands) { ?>
                  <th style="text-align:right;">Options</th>  
                  <?php } ?>
                </tr>
              </thead>
              <tbody id="tbody_data">
                <?php foreach ($brands as $row) { 
                  $img = 'default.jpg';
                  if ($row->photo_path!=null) {
                    $img = 'brands/'.$row->photo_path.'-org.'.$row->extension;
                  }
                ?>
                <tr id="brandRow<?=$row->brand_id?>">
                  <td><img class="img-rounded" src="<?=base_url();?>photos/<?=$img?>" alt="<?=$row->photo_title;?>" height="32"></td>
                  <td><?=$row->brand;?></td>
                  <td><?=$row->view_count;?></td>
                  <?php if($edit_brands || $delete_brands) { ?>
                  <td align="right">
                    <?php if($edit_brands){?>
                    <button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editBrands('<?=$row->brand_id?>');"><i class="zmdi zmdi-edit"></i></button>
                    <?php } if($delete_brands){ ?>
                    <button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteBrands('<?=$row->brand_id?>');"><i class="zmdi zmdi-delete"></i></button>
                    <?php } ?>
                  </td>
                  <?php } ?>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php $this->load->view('includes/footer'); ?>

      <div id="otherModal3" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal-title">Add Brands</h4>
            </div>
            <div id="brandModalCont">
              <form action="<?=base_url()?>uploadBrandImage" class="dropzone" id="myDropzone">
                <input type="hidden" name="field_id" id="field_id" value="">
                <div class="dz-default dz-message">
                  <span class="text-muted">Drop brand image here</span>
                </div>
              </form>
              <form data-toggle="validator" id="inputMask">
                <div class="modal-body">
                  <input type="hidden" name="brand_id" id="brand_id" value="">
                  <div class="form-group">
                    <label for="form-control-2" class="control-label">Brand</label>
                    <input type="text" class="form-control" id="brandName" name="brandName" placeholder="Brand" data-required-error="Brand is Required" required>
                    <div class="help-block with-errors"></div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">

      $('#table-1').DataTable();

      $("#myDropzone").dropzone({
        acceptedFiles: 'image/*',
        autoProcessQueue: false,
        uploadMultiple: false,
        addRemoveLinks: true,
        maxFiles: 1,
        url: "<?=base_url()?>uploadBrandImage",
        success : function(file, response){
          // setTimeout(function(){
          //   location.reload();
          // }, 100);
        }
      });

      $('#inputMask').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#brandModalCont');
          var myDropzone = Dropzone.forElement(".dropzone");

          if (myDropzone.getQueuedFiles().length > 0 || myDropzone.files.length > 0) {
            $.ajax({
              type: "POST",
              url: "<?=base_url()?>addBrand",
              data: $('#inputMask').serialize(),
              success: function(result) {
                var responsedata = $.parseJSON(result);
                if(responsedata.status=='insert'){
                  $("#field_id").val(responsedata.id);
                  myDropzone.processQueue();
                  toastr.success(responsedata.message)
                  location.reload();
                }else if(responsedata.status=='update'){
                  if (myDropzone.getQueuedFiles().length > 0) {
                    myDropzone.processQueue();
                  }else{
                    // location.reload();
                  }
                  toastr.success(responsedata.message)
                }else if(responsedata.status=='error'){
                  toastr.error(responsedata.message)
                }else{
                  toastr.error("Somthing went wrong :(")
                }
                $("#otherModal3").modal('hide');
                $('#brandModalCont').waitMe('hide');
              },
              error: function(result) {
                $('#brandModalCont').waitMe('hide');
                toastr.error('Error :'+result)
              }
            });
          }else{
            toastr.error("Please add brand image")
            $('#brandModalCont').waitMe('hide');
          }  
        }
      });

      function addBrands() {
        $('#modal-title').text('Add Brand');
        $('#brandName').val("");
        $("#brand_id").val(0);
        $("#field_id").val(0);
        var myDropzone = Dropzone.forElement(".dropzone");
        myDropzone.removeAllFiles();
      }

      function editBrands(id) {
        var img = $("#brandRow"+id).find("td:eq(0) img").attr('src');
        var brand = $("#brandRow"+id).find("td:eq(1)").text();
        $('#modal-title').text('Update Brand');
        $("#brand_id").val(id);
        $("#field_id").val(id);
        $('#brandName').val(brand);

        var myDropzone = Dropzone.forElement(".dropzone");
        myDropzone.removeAllFiles();
        var mockFile = { name: brand, size: 20 };
        myDropzone.options.addedfile.call(myDropzone, mockFile);
        myDropzone.options.thumbnail.call(myDropzone, mockFile, img);
        myDropzone.files.push( mockFile );

        $("#otherModal3").modal('show');
      }

      function deleteBrands(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this Brand?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteBrand",
                  data: 'brand_id='+id,
                  success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                      var table = $('#table-1').DataTable();
                      table.row('#brandRow'+id).remove().draw( false );
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