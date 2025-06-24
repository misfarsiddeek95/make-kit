<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $this->load->view('includes/head'); ?>
  </head>

  <body class="layout layout-header-fixed layout-left-sidebar-fixed">
    <?php $this->load->view('includes/topbar'); ?>
    <div class="site-main">
      <?php $this->load->view('includes/sidebar'); ?>
        <div class="site-content">

          
          <div class="panel panel-default panel-table">
            <div class="panel-heading">
              <?php if($add_cat){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-primary m-w-120" data-toggle="modal" data-target="#otherModal3" onclick="addCategory();">Add Category</button>
              </div>
              <?php }?>
              <h3 class="m-t-0 m-b-5">Category Attributes</h3>
            </div>
            <div class="table-responsive m-y-5">
              <table class="table table-hover" id="table-1">
                <thead>
                  <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Tree</th>
                    <th>View Count</th>
                    <th>Status</th>
                    <?php if($edit_cat){?>
                    <th>Edit</th>
                    <?php } if($imageUpload){?>
                    <th>Upload image</th>
                    <?php } if($delete_cat){ ?>
                    <th>Delete</th>
                    <?php } ?>
                  </tr>
                </thead>
                <tbody id="tbody_data">
                  <?php foreach ($allCates as $row) { 
                    $status = '';
                    $img = 'default.jpg';
                    if ($row->status==0) {
                      $status = 'checked="checked"';
                    }
                    if ($row->photo_path!=null) {
                      $img = 'categories/'.$row->photo_path.'-sma.jpg';
                    }
                  ?>

                  <tr id="catRow<?=$row->cate_id?>">
                    <td><img class="img-rounded" src="<?=base_url();?>photos/<?=$img?>" alt="<?=$row->photo_title;?>" height="32"></td>
                    <td><?=$row->cate_id;?></td>
                    <td><?=$row->category;?></td>
                    <td><?=$row->tree_path;?></td>
                    <td><?=$row->view_count ;?></td>                    
                    <td><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" <?=$status;?> <?php if ($changeStatus) {echo 'onchange="updateCateStatus('.$row->cate_id.');"';}else{echo "disabled";}?> ></td>
                    <?php if($edit_cat){?>
                    <td><button type="button" class="btn btn-outline-primary" onclick="editCat(<?=$row->cate_id;?>);">Edit</button></td>
                    <?php } if($imageUpload){?>
                    <td><button type="button" class="btn btn-outline-info" onclick="uploadImage(<?=$row->cate_id;?>);">Upload image</button></td>
                    <?php } if($delete_cat){ ?>
                    <td><button type="button" class="btn btn-outline-danger" onclick="deleteCat(<?=$row->cate_id;?>);">Delete</button></td>
                    <?php } ?>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
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
              <h4 class="modal-title" id="modal-title">Categories</h4>
            </div>

            <form data-toggle="validator" id="inputmasks">
              <div class="modal-body">
                <input type="hidden" name="cate_id" id="cate_id" value="0">
                <div class="form-group" id="catSelect">
                  <label for="form-control-3" class="control-label">Categoris</label>
                  <select class="form-control" data-plugin="select2" name="allCategories" id="allCategories" style="width: 100%">
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
                        if (isset($category->sub_cat) && sizeof($category->sub_cat) > 0) {?>
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
                      }?>
                  </select>
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group">
                  <label for="form-control-2" class="control-label">Category</label>
                  <input type="text" class="form-control" id="category" name="category" placeholder="Category" data-required-error="Category is Required" required>
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

      <div id="imageModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal-title">Add Category Photo</h4>
            </div>

            <form action="<?=base_url()?>uploadSingleImage" class="dropzone" id="myDropzone">
                <input type="hidden" name="field_id" id="field_id" value="">
                <div class="dz-message" data-dz-message="">
                  <div class="dz-icon">
                    <i class="zmdi zmdi-upload"></i>
                  </div>
                  <span class="text-muted">Drop image here or click to upload</span>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
      $("#allCategories").select2({
        placeholder: "Select a Category"
      });

      $('#table-1').DataTable();

      $("#myDropzone").dropzone({
        maxFiles: 1,
        url: "<?=base_url()?>uploadSingleImage", 
        success : function(file, response){
          toastr.success('Image uploaded successfully.')
          setTimeout(function(){
            location.reload();
          }, 500);
          $("#imageModal").modal('hide');
        }
      });

      $('#inputmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#inputmasks');
            $.ajax({
              type: "POST",
              url: "<?=base_url()?>addCategory",
              data: $('#inputmasks').serialize(),
              success: function(result) {
                var responsedata = $.parseJSON(result);
                if(responsedata.status=='success'){
                  toastr.success(responsedata.message)
                  setTimeout(function(){
                    location.reload();
                  }, 500);
                }else if(responsedata.status=='error'){
                  toastr.error(responsedata.message)
                }else{
                  toastr.error("Somthing went wrong :(")
                }
                $("#otherModal3").modal('hide');
                $('#inputmasks').waitMe('hide');
              },
              error: function(result) {
                $('#inputmasks').waitMe('hide');
                toastr.error('Error :'+result)
              }
          });
        }
      });

      function uploadImage(id) {
        $("#field_id").val(id);
        $("#imageModal").modal('show');
      }

      function addCategory() {
        $('#modal-title').text('Add Category');
        $("#catSelect").show();
        $('#category').val("");
        $("#cate_id").val(0);
      }

      function editCat(id) {
        var category = $("#catRow"+id).find("td:eq(2)").text();
        $('#modal-title').text('Update Category');
        $("#catSelect").hide();
        $("#cate_id").val(id);
        $('#category').val(category);
        $("#otherModal3").modal('show');
      }

      function deleteCat(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this Category?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteCategories",
                  data: 'cate_id='+id,
                  success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                      var table = $('#table-1').DataTable();
                      table.row('#catRow'+id).remove().draw( false );
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

      function updateCateStatus(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>updateCateStatus",
          data: 'cate_id='+id,
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
    </script>
  </body>
</html>