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
              <?php if($add_cat){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-success btn-pill" data-toggle="modal" data-target="#otherModal3" title="Add"  onclick="addCategory();"><i class="zmdi zmdi-plus"></i></button>
              </div>
              <?php }?>
              <h3 class="m-t-0 m-b-5">קטגוריות</h3>
            </div>
            <div class="panel-body">
              <div class="table-responsive m-y-5">
                <table class="table table-hover" id="table-1">
                  <thead>
                    <tr>
                      <th></th>
                      <th>ID</th>
                      <th>קטגוריה</th>
                      <th>כותרת משנה של הקטגוריה</th>
                      <th>כתובת קטגוריה</th>
                      <th>עץ</th>
                      <th>מספר צפיות</th>
                      <th>סטטוס</th>
                      <th>הצג באתר</th>
                      <th>הצג בווידג'ט</th>
                      <?php if($edit_cat || $delete_cat || $imageUpload||$manage_attr||$manage_brands){?>
                        <th style="text-align:right;">אפשרויות</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="tbody_data">
                    <?php foreach ($allCates as $row) { 
                      $status = '';
                      $showInSiteStatus = '';
                      $showAsWidget = '';

                      $img = 'default.jpg';
                      if ($row->status==0) {
                        $status = 'checked="checked"';
                      }
                      if ($row->show_in_site==1) {
                        $showInSiteStatus = 'checked="checked"';
                      }
                      if ($row->show_as_widget==1) {
                        $showAsWidget = 'checked="checked"';
                      }
                      if ($row->photo_path!=null) {
                        $img = 'categories/'.$row->photo_path.'-sma.jpg';
                      }

                      $dontDeleteCates = [1,2,3,4,5];
                    ?>

                    <tr id="catRow<?=$row->cate_id?>">
                      <td><img class="img-rounded" src="<?=base_url();?>photos/<?=$img?>" alt="<?=$row->photo_title;?>" height="32"></td>
                      <td><?=$row->cate_id;?></td>
                      <td><?=$row->category;?></td>
                      <td><?=$row->category_second_title;?></td>
                      <td><?=$row->seo_url;?></td>
                      <td><?=$row->tree_path;?></td>
                      <td><?=$row->view_count ;?></td>
                      <td><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" <?=$status;?> <?php if ($changeStatus) {echo 'onchange="updateCateStatus('.$row->cate_id.');"';}else{echo "disabled";}?> ></td>
                      <td><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" <?=$showInSiteStatus;?>  onchange="updateShowInStatus('<?=$row->cate_id?>');"></td>
                      <td><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" <?=$showAsWidget;?>  onchange="updateShowAsWidget('<?=$row->cate_id?>');"></td>
                      <?php if($edit_cat || $delete_cat || $imageUpload||$manage_attr||$manage_brands){ ?>
                      <td align="right">
                      <?php if($imageUpload||$manage_attr||$manage_brands){ ?>
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary btn-pill btn-sm dropdown-toggle m-r-5" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="zmdi zmdi-more"></i>
                          </button>
                          <ul class="dropdown-menu dropdown-menu-right">
                            <?php if($imageUpload){?>
                            <li><a href="javascript:uploadImage(<?=$row->cate_id;?>);">העלה תמונה</a></li>
                            <?php } if($manage_attr){?>
                            <li><a href="javascript:manage_attr(<?=$row->cate_id;?>);">נהל תכונות</a></li>
                            <?php } if($manage_brands){?>
                            <li><a href="javascript:manage_brands(<?=$row->cate_id;?>);">נהל מותגים</a></li>
                            <?php }?>
                          </ul>
                        </div>
                        <?php } if($edit_cat){?>
                          <button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editCat('<?=$row->cate_id?>');"><i class="zmdi zmdi-edit"></i></button>
                        <?php } if($delete_cat && !in_array($row->cate_id, $dontDeleteCates)){ ?>
                          <button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteCat('<?=$row->cate_id?>');"><i class="zmdi zmdi-delete"></i></button>
                        <?php } ?>
                      </td>
                      <?php }?>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="panel panel-default panel-table" id="attrAssignMain" style="display: none;">
            <div class="panel-heading">
              <div class="panel-tools">
                <?php if($assign_attr){?>
                <button type="button" class="btn btn-outline-primary" onclick="assignAttr();">שייך תכונה</button>
                <?php } ?>
                <a href="javascript:closeAssignMain();" class="tools-icon btn btn-outline-primary">
                  <i class="zmdi zmdi-close"></i>
                </a>
              </div>
              <h3 class="panel-title" id="cateTitle">תכונות קטגוריה</h3>
            </div>
            <div class="panel-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>תכונות</th>
                      <th>שם זיהוי</th>
                      <th>סוג</th>
                      <th>הצג לכולם</th>
                      <th>השפעת מחיר</th>
                      <?php if($remove_attr){ ?>
                      <th>הסר</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="attrAssignTbody">
                    
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="panel panel-default panel-table" id="cateBrandMain" style="display: none;">
            <div class="panel-heading">
              <div class="panel-tools">
                <?php if($assign_brands){?>
                <button type="button" class="btn btn-outline-primary" onclick="assignBrands();">שייך מותג</button>
                <?php } ?>
                <a href="javascript:closeBrandMain();" class="tools-icon btn btn-outline-primary">
                  <i class="zmdi zmdi-close"></i>
                </a>
              </div>
              <h3 class="panel-title" id="brandTitle">מותגים משוייכים</h3>
            </div>
            <div class="panel-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th></th>
                      <th>מותג</th>
                      <th>מספר צפיות</th>
                      <?php if($remove_brands){ ?>
                      <th>הסר</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="brandAssignTbody">
                    
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <?php $this->load->view('includes/footer'); ?>

      <div id="otherModal3" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal-title">קטגוריות</h4>
            </div>

            <form data-toggle="validator" id="inputmasks">
              <div class="modal-body">
                <input type="hidden" name="cate_id" id="cate_id" value="0">
                <div class="form-group" id="catSelect">
                  <label for="form-control-3" class="control-label">קטגוריות</label>
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
                  <label for="category" class="control-label">קטגוריה</label>
                  <input type="text" class="form-control" id="category" name="category" placeholder="קטגוריה" data-required-error="קטגוריה נדרשת" required>
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="category_second_title" class="control-label">כותרת משנה של הקטגוריה</label>
                  <input type="text" class="form-control" id="category_second_title" name="category_second_title" placeholder="כותרת משנה של הקטגוריה" />
                  <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label for="seoUrl" class="control-label">כתובת SEO</label>
                  <div class="row">
                    <div class="col-sm-6" id="seo_url_col">
                      <input class="form-control" type="text"
                        value=""
                        placeholder="כתובת SEO" id="seoUrl" name="seo_url" required
                        data-required-error="כתובת SEO נדרשת." onkeyup="getSeoUrl(this.value);">
                    </div>
                    <div class="col-sm-6 col-md-3" id="urlEditable">
                        <button type="button" class="btn btn-info btn-block" id="urlEdit" onclick="enableFieldEdit('1','seoUrl','urlEdit');"><i class="zmdi zmdi-edit"></i></button>
                    </div>
                    <div id="ex_url"></div>
                  </div>
                  <div class="help-block with-errors"></div>
                </div>

              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">שלח</button>
                <button type="button" data-dismiss="modal" class="btn btn-default">סגור</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div id="imageModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal-title">הוסף תמונת קטגוריה</h4>
            </div>

            <form action="<?=base_url()?>uploadSingleImage" class="dropzone" id="myDropzone">
                <input type="hidden" name="field_id" id="field_id" value="">
                <div class="dz-message" data-dz-message="">
                  <div class="dz-icon">
                    <i class="zmdi zmdi-upload"></i>
                  </div>
                  <span class="text-muted">גרור תמונה לכאן או לחץ להעלאה</span>
                </div>
            </form>
          </div>
        </div>
      </div>

      <div id="attrAssignModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal_assign_title">שייך תכונה</h4>
            </div>

            <form data-toggle="validator" id="attrAssignMasks">
              <div class="modal-body">
                <input type="hidden" name="assign_cate_id" id="assign_cate_id" value="0">
                <div class="form-group">
                  <label for="form-control-2" class="control-label">תכונה</label>
                  <select class="form-control" data-plugin="select2" name="assign_attr" id="assign_attr" style="width: 100%;" data-required-error="אנא בחר תכונה ברשימה." required>
                    <option></option>
                  </select>
                  <div class="help-block with-errors"></div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">שלח</button>
                <button type="button" data-dismiss="modal" class="btn btn-default">סגור</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div id="brandAssignModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal_bassign_title">שייך מותג</h4>
            </div>
            <form data-toggle="validator" id="brandAssignMasks">
              <div class="modal-body">
                <input type="hidden" name="bassign_cate_id" id="bassign_cate_id" value="0">
                <div class="form-group">
                  <label for="form-control-2" class="control-label">מותגים</label>
                  <select class="form-control" data-plugin="select2" name="assign_brand" id="assign_brand" style="width: 100%;" data-required-error="אנא בחר מותג ברשימה." required>
                    <option></option>
                  </select>
                  <div class="help-block with-errors"></div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">שלח</button>
                <button type="button" data-dismiss="modal" class="btn btn-default">סגור</button>
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
        placeholder: "בחר קטגוריה",
        allowClear: true
      });

      $("#assign_attr").select2({
        placeholder: "בחר תכונה",
        allowClear: true
      });

      $("#assign_brand").select2({
        placeholder: "בחר מותג",
        allowClear: true
      });

      $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "inherit" );
      });

      $('.table-responsive').on('hide.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "auto" );
      });

      $('#table-1').DataTable();

      $("#myDropzone").dropzone({
        maxFiles: 1,
        acceptedFiles: 'image/jpeg',
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

        $('#seo_url_col').removeClass('col-md-9').addClass('col-md-12');
        $('#seoUrl').removeAttr('disabled');
        $('#urlEditable').hide();
      }

      function editCat(id) {
        const category = $("#catRow"+id).find("td:eq(2)").text();
        const cateSecondTitle = $("#catRow"+id).find("td:eq(3)").text();
        const cateSeoUrl = $("#catRow"+id).find("td:eq(4)").text();

        $('#modal-title').text('Update Category');
        $("#catSelect").hide();
        $("#cate_id").val(id);
        $('#category').val(category);
        $('#category_second_title').val(cateSecondTitle);

        $('#seo_url_col').removeClass('col-md-12').addClass('col-md-9');
        $('#seoUrl').val(cateSeoUrl).attr('disabled','disabled');
        $('#urlEditable').show();

        $("#otherModal3").modal('show');
      }

      function getSeoUrl(value) {
        var slug = slugifyUrl(value);
        $('#seoUrl').val(slug);
      }

      $('#seoUrl').change(function() {
        $.post( "<?=base_url()?>check-url-exist", { seourl: $("#seoUrl").val(),tbl:'categories' }, function (data){
          var resp = $.parseJSON(data);  
          if (resp == 1) {
            $('#ex_url').text('SEO Url already exists. Try another.');
            $('#ex_url').css({"display": "inline", "color": "red"});
          }else{
            $('#ex_url').text('');
            $('#ex_url').css({"display": "none"});
          }
        });
      });

      function enableFieldEdit(action,fieldId,myId) {
        if (action == 1) {
          $('#'+fieldId).removeAttr('disabled');
          $('#'+myId).attr('onclick','enableFieldEdit("0","'+fieldId+'","'+myId+'")');
          $('#'+myId).html('<i class="zmdi zmdi-close"></i>');
          $('#'+myId).removeClass('btn-info').addClass('btn-danger');
        }else{
          $('#'+fieldId).attr('disabled','disabled');
          $('#'+myId).attr('onclick','enableFieldEdit("1","'+fieldId+'","'+myId+'")');
          $('#'+myId).html('<i class="zmdi zmdi-edit"></i>');
          $('#'+myId).removeClass('btn-danger').addClass('btn-info');
        }
      }

      function deleteCat(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>כן</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>לא</button>",'האם ברצונך למחוק קטגוריה זו?',{
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

      function updateShowInStatus(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>update-show-in-the-site-status",
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

      function updateShowAsWidget(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>update-show-as-widget-status",
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

      function manage_attr(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>cateAssignAttr",
          data: 'cate_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            var category = $("#catRow"+id).find("td:eq(2)").text();

            $("#assign_cate_id").val(id);
            $("#cateTitle").text(category+" תכונות קטגוריה");
            $('#attrAssignTbody').empty();
            closeBrandMain();

            var tbody = '';
            if (responsedata.assign_attr.length==0) {
              $('#attrAssignTbody').append('<tr><td colspan="6" class="text-center">אין תוצאות</td></tr>');
            }else{
              for (var i = 0; i < responsedata.assign_attr.length; i++) {
                var type = responsedata.assign_attr[i]['type'];
                var show_to_all = 'No';
                var price_effect = 'No';
                if (type==0) {
                  type = 'Required Dropdown';
                }else if (type==1) {
                  type = 'Required Multi Dropdown';
                }else if (type==2) {
                  type = 'Multi Dropdown';
                }else if (type==3) {
                  type = 'Multi Color Pick';
                }else if (type==4) {
                  type = 'Single Color Pick';
                }
                if (responsedata.assign_attr[i]['show_to_all']==0) {
                  show_to_all = 'כן';
                }
                if (responsedata.assign_attr[i]['price_effect']==0) {
                  price_effect = 'כן';
                }
                tbody+='<tr id="assignAttr'+responsedata.assign_attr[i]['ca_id']+'"><td>'+responsedata.assign_attr[i]['attribute']+'</td>'+
                  '<td>'+responsedata.assign_attr[i]['identification_name']+'</td>'+
                  '<td>'+type+'</td>'+
                  '<td>'+show_to_all+'</td>'+
                  '<td>'+price_effect+'</td>'+
                  <?php if($remove_attr){ ?>
                  '<td><button type="button" class="btn btn-outline-danger" onclick="removeAssignAttr('+responsedata.assign_attr[i]['ca_id']+');">הסר</button></td>'+
                  <?php } ?>
                  '</tr>';
              }
            }
            $('#attrAssignTbody').append(tbody);
            $("#attrAssignMain").fadeIn("slow");
            
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }

      function removeAssignAttr(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>כן</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>לא</button>",'האם ברצונך להסיר תכונה זו?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>removeAssignAttr",
                  data: 'ca_id='+id,
                  success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                      $('#assignAttr'+id).remove();
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

      function assignAttr() {
        var cate_id = $('#assign_cate_id').val();
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getAttributes",
          data: 'cate_id='+cate_id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            $("#assign_attr").empty();
            $("#assign_attr").append("<option></option>");
            for (var i = 0; i < responsedata.attributes.length; i++) {
              $("#assign_attr").append($("<option></option>").attr("value",responsedata.attributes[i]['attr_id']).text(responsedata.attributes[i]['identification_name']+' - '+responsedata.attributes[i]['attribute']));
            }
            $("#attrAssignModal").modal('show');
            $("#assign_attr").selectpicker('refresh');
            $("#assign_attr").select2({
              placeholder: "בחר תכונה",
              allowClear: true
            });
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }

      $('#attrAssignMasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#attrAssignMasks');
          var cate_id = $('#assign_cate_id').val();
            $.ajax({
              type: "POST",
              url: "<?=base_url()?>assignCateAttr",
              data: $('#attrAssignMasks').serialize(),
              success: function(result) {
                var responsedata = $.parseJSON(result);
                if(responsedata.status=='success'){
                  toastr.success(responsedata.message)
                  setTimeout(function(){
                    manage_attr(cate_id);
                  }, 100);
                }else if(responsedata.status=='error'){
                  toastr.error(responsedata.message)
                }else{
                  toastr.error("Somthing went wrong :(")
                }
                $("#attrAssignModal").modal('hide');
                $('#attrAssignMasks').waitMe('hide');
              },
              error: function(result) {
                $('#attrAssignMasks').waitMe('hide');
                toastr.error('Error :'+result)
              }
          });
        }
      });

      function closeAssignMain() {
        $("#attrAssignMain").fadeOut("slow");
        $('#attrAssignTbody').empty();
      }

      function manage_brands(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>cateAssignBrands",
          data: 'cate_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            var category = $("#catRow"+id).find("td:eq(2)").text();
            $("#bassign_cate_id").val(id);
            $("#brandTitle").text(category+" מותגי קטגוריה");
            $('#brandAssignTbody').empty();
            closeAssignMain();

            var tbody = '';
            if (responsedata.assign_brands.length==0) {
              $('#brandAssignTbody').append('<tr><td colspan="3" class="text-center">אין תוצאות</td></tr>');
            }else{
              for (var i = 0; i < responsedata.assign_brands.length; i++) {
                var img = 'default.jpg';
                if (responsedata.assign_brands[i]['photo_path']!=null) {
                  img = 'brands/'+responsedata.assign_brands[i]['photo_path']+'-org.jpg';
                }
                tbody+='<tr id="assignBrand'+responsedata.assign_brands[i]['cb_id']+'"><td><img class="img-rounded" src="<?=base_url();?>photos/'+img+'" alt="'+responsedata.assign_brands[i]['photo_title']+'" height="32"></td>'+
                  '<td>'+responsedata.assign_brands[i]['brand']+'</td>'+
                  '<td>'+responsedata.assign_brands[i]['view_count']+'</td>'+
                  <?php if($remove_brands){ ?>
                  '<td><button type="button" class="btn btn-outline-danger" onclick="removeAssignBrands('+responsedata.assign_brands[i]['cb_id']+');">הסר</button></td>'+
                  <?php } ?>
                  '</tr>';
              }
            }
            $('#brandAssignTbody').append(tbody);
            $("#cateBrandMain").fadeIn("slow");
            
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }

      function assignBrands() {
        var cate_id = $('#bassign_cate_id').val();
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>getBrands",
          data: 'cate_id='+cate_id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            $("#assign_brand").empty();
            $("#assign_brand").append("<option></option>");
            for (var i = 0; i < responsedata.brands.length; i++) {
              $("#assign_brand").append($("<option></option>").attr("value",responsedata.brands[i]['brand_id']).text(responsedata.brands[i]['brand']));
            }
            $("#brandAssignModal").modal('show');
            $("#assign_brand").selectpicker('refresh');
            $("#assign_brand").select2({
              placeholder: "בחר מותג",
              allowClear: true
            });
          },
          error: function(result) {
            toastr.error("Somthing went wrong :(")
          }
        });
      }

      $('#brandAssignMasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#brandAssignMasks');
          var cate_id = $('#bassign_cate_id').val();
            $.ajax({
              type: "POST",
              url: "<?=base_url()?>assignCateBrand",
              data: $('#brandAssignMasks').serialize(),
              success: function(result) {
                var responsedata = $.parseJSON(result);
                if(responsedata.status=='success'){
                  toastr.success(responsedata.message)
                  setTimeout(function(){
                    manage_brands(cate_id);
                  }, 100);
                }else if(responsedata.status=='error'){
                  toastr.error(responsedata.message)
                }else{
                  toastr.error("Somthing went wrong :(")
                }
                $("#brandAssignModal").modal('hide');
                $('#brandAssignMasks').waitMe('hide');
              },
              error: function(result) {
                $('#brandAssignMasks').waitMe('hide');
                toastr.error('Error :'+result)
              }
          });
        }
      });

      function removeAssignBrands(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>כן</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>לא</button>",'האם ברצונך להסיר מותג זה?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>removeAssignBrand",
                  data: 'cb_id='+id,
                  success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                      $('#assignBrand'+id).remove();
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

      function closeBrandMain() {
        $("#cateBrandMain").fadeOut("slow");
        $('#brandAssignTbody').empty();
      }
    </script>
  </body>
</html>