<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php $this->load->view('includes/head'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/spectrum.css">
    <style type="text/css">
    .myClass{
      display: inline;
      float: right;
      position: relative;
      top: -34px;
      width: 19%;
      height: 34px;
      line-height: 1.538462;
      color: #555555;
      border: 1px solid #ccc;
      border-radius: 2px;
      box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
      -webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
      transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
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
              <?php if($add_attr){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-success btn-pill" data-toggle="modal" data-target="#otherModal3" title="Add"  onclick="addAttributes();"><i class="zmdi zmdi-plus"></i></button>
              </div>
              <?php }?>
              <h3 class="m-t-0 m-b-5">תכונות</h3>
            </div>
            <div class="panel-body">
              <div class="table-responsive m-y-5">
                <table class="table table-hover" id="table-1">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>זיהוי</th>
                      <th>תכונה</th>
                      <th>סוג</th>
                      <th>הצג לכולם</th>
                      <th>השפעת מחיר</th>
                      <?php if($manage_attr_val) { ?>
                      <th>נהל ערכים</th>
                      <?php } if($edit_attr || $delete_attr){?>
                      <th style="text-align:right;">אפשרויות</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="tbody_data">
                    <?php $i=1;
                    foreach ($attributes as $row) { 
                      $type = $row->type;
                      $show_to_all = 'No';
                      $price_effect = 'No';
                      if ($type==0) {
                        $type = 'תפריט נדרש';
                      }else if ($type==1) {
                        $type = 'תפריט רב נדרש';
                      }else if ($type==2) {
                        $type = 'תפריט רב';
                      }else if ($type==3) {
                        $type = 'בחירת צבע ריבוי';
                      }else if ($type==4) {
                        $type = 'בחירת צבע יחיד';
                      }
                      if ($row->show_to_all==0) {
                        $show_to_all = 'כן';
                      }
                      if ($row->price_effect==0) {
                        $price_effect = 'כן';
                      }
                    ?>
                    <tr id="attrRow<?=$row->attr_id?>">
                      <td><?=$i?></td>
                      <td><?=$row->identification_name;?></td>
                      <td><?=$row->attribute;?></td>
                      <td attr-type="<?=$row->type?>"><?=$type;?></td>
                      <td attr-show="<?=$row->show_to_all?>"><?=$show_to_all;?></td>                    
                      <td attr-price-effect="<?=$row->price_effect?>"><?=$price_effect;?></td>
                      <?php if($manage_attr_val){?>
                      <td><button type="button" class="btn btn-outline-info" onclick="manageAttrVal(<?=$row->attr_id;?>);">נהל ערכים</button></td>
                      <?php } if($edit_attr || $delete_attr){?>
                      <td align="right">
                        <?php if($edit_attr){?>
                        <button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editAttr('<?=$row->attr_id?>');"><i class="zmdi zmdi-edit"></i></button>
                        <?php } if($delete_attr){ ?>
                        <button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteAttr('<?=$row->attr_id?>');"><i class="zmdi zmdi-delete"></i></button>
                        <?php } ?>
                      </td>
                      <?php } ?>
                    </tr>
                    <?php $i++;} ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="panel panel-default panel-table" id="attrValMain" style="display: none;">
            <div class="panel-heading">
              <div class="panel-tools">
                <?php if($add_attr_val){?>
                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#attrValModal" onclick="addAttrVal
();">הוסף ערכים</button>
                <?php } ?>
                <a href="javascript:closeValMain();" class="tools-icon btn btn-outline-primary">
                  <i class="zmdi zmdi-close"></i>
                </a>
              </div>
              <h3 class="panel-title" id="atValTitle">ערכי תכונה</h3>
            </div>
            <div class="panel-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>ערך</th>
                      <th>תיאור</th>
                      <th>סטטוס</th>
                      <?php if($edit_attr_val || $delete_attr_val){ ?>
                      <th style="text-align:right;">אפשרויות</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="attrValTbody">
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
              <h4 class="modal-title" id="modal-title">תכונות</h4>
            </div>
            <form data-toggle="validator" id="inputmasks">
              <div class="modal-body">
                <input type="hidden" name="attr_id" id="attr_id" value="0">
                <div class="form-group">
                  <label for="form-control-2" class="control-label">שם זיהוי</label>
                  <input type="text" class="form-control" id="identification_name" name="identification_name" placeholder="שם זיהוי" data-required-error="שם זיהוי נדרש" required>
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group">
                  <label for="form-control-2" class="control-label">תכונה</label>
                  <input type="text" class="form-control" id="attribute" name="attribute" placeholder="תכונה" data-required-error="תכונה נדרשת" required>
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group">
                  <label for="form-control-2" class="control-label">סוג</label>
                  <select class="form-control" id="attrType" name="attrType" data-required-error="סוג תכונה נדרש" required>
                    <option value="" selected="selected" disabled="disabled">-- בחר סוג תכונה --</option>
                    <option value="0">תפריט נדרש</option>
                    <option value="1">תפריט רב נדרש</option>
                    <option value="2">תפריט רב</option>
                    <option value="3">בחירת צבע ריבוי</option>
                    <option value="4">בחירת צבע יחיד</option>
                  </select>
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group">                  
                  <label for="form-control-2" class="control-label">הצג לכולם</label>
                  <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-outline-primary showRadio">
                      <input type="radio" name="show_to_all" id="show_yes" autocomplete="off" value="0" required> כן
                    </label>
                    <label class="btn btn-outline-primary showRadio">
                      <input type="radio" name="show_to_all" id="show_no" autocomplete="off" value="1" required> לא
                    </label>
                  </div>
                </div>                
                <div class="form-group">                  
                  <label for="form-control-2" class="control-label">השפעת מחיר</label>
                  <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-outline-primary priceRadio">
                      <input type="radio" name="price_effect" id="price_effect_yes" autocomplete="off" value="0" required> כן
                    </label>
                    <label class="btn btn-outline-primary priceRadio">
                      <input type="radio" name="price_effect" id="price_effect_no" autocomplete="off" value="1" required> לא
                    </label>
                  </div>
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
      <div id="attrValModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">
                  <i class="zmdi zmdi-close"></i>
                </span>
              </button>
              <h4 class="modal-title" id="modal-val-title">ערך תכונה</h4>
            </div>
            <form data-toggle="validator" id="attrValMasks">
              <div class="modal-body">
                <input type="hidden" name="val_attr_id" id="val_attr_id" value="0">
                <input type="hidden" name="attr_val_id" id="attr_val_id" value="0">
                <div class="form-group">
                  <label for="form-control-2" class="control-label">ערך</label>
                  <input type='text' data-pattern-error="ערך לא חוקי" class="form-control" id="attrVal" name="attrVal" placeholder="ערך" data-required-error="ערך נדרש" required />
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group">
                  <label for="form-control-2" class="control-label">תיאור</label>
                  <input type="text" class="form-control" id="attrValDesc" name="attrValDesc" placeholder="תיאור">
                  <div class="help-block with-errors"></div>
                </div>
                <?php if($attr_val_status){?>
                <div class="form-group">                  
                  <label for="form-control-2" class="control-label">סטטוס</label>
                  <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-outline-primary statusRadio">
                      <input type="radio" name="av_status" id="status_on" autocomplete="off" value="0" required> פועל
                    </label>
                    <label class="btn btn-outline-primary statusRadio">
                      <input type="radio" name="av_status" id="status_off" autocomplete="off" value="1" required> כבוי
                    </label>
                  </div>
                </div>
                <?php } ?>
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
    <?php $this->load->view('includes/javascripts'); ?>
    <script src="<?=base_url()?>assets/js/forms-plugins.js"></script>
    <script src="<?=base_url()?>assets/spectrum.js"></script>
    <script type="text/javascript">
      $( ".colorPick" ).blur(function() {
        $(this).spectrum("set", $("#attrVal").val());
      });
      $('#table-1').DataTable();
      $('#inputmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#inputmasks');
            $.ajax({
              type: "POST",
              url: "<?=base_url()?>addAttributes",
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
                  toastr.error("משהו השתבש :(")
                }
                $("#otherModal3").modal('hide');
                $('#inputmasks').waitMe('hide');
              },
              error: function(result) {
                $('#inputmasks').waitMe('hide');
                toastr.error('שגיאה: '+result)
              }
          });
        }
      });
      function addAttributes() {
        $('#modal-title').text('הוסף תכונה');
        $('#attribute, #identification_name, #attrType').val("");
        $("#attr_id").val(0);
        $(".showRadio,.priceRadio").removeClass('active');
        $('[name="show_to_all"]').removeAttr('checked');
        $('[name="price_effect"]').removeAttr('checked');
      }
      function editAttr(id) {
        var identification_name = $("#attrRow"+id).find("td:eq(1)").text();
        var attribute = $("#attrRow"+id).find("td:eq(2)").text();
        var type = $("#attrRow"+id).find('td').eq(3).attr('attr-type');
        var attrshow = $("#attrRow"+id).find('td').eq(4).attr('attr-show');
        var attrpriceeffect = $("#attrRow"+id).find('td').eq(5).attr('attr-price-effect');
        $('#modal-title').text('עדכן תכונה');
        $("#attr_id").val(id);
        $('#identification_name').val(identification_name);
        $('#attribute').val(attribute);
        $('#attrType').val(type);
        $(".showRadio,.priceRadio").removeClass('active');
        $("input[name=show_to_all][value="+attrshow+"]").parent().addClass('active');
        $("input[name=show_to_all][value="+attrshow+"]").attr('checked', 'checked');
        $("input[name=price_effect][value="+attrpriceeffect+"]").parent().addClass('active');
        $("input[name=price_effect][value="+attrpriceeffect+"]").attr('checked', 'checked');
        $("#otherModal3").modal('show');
      }
      function deleteAttr(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>כן</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>לא</button>",'האם ברצונך למחוק תכונה זו?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteAttribute",
                  data: 'attr_id='+id,
                  success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                      var table = $('#table-1').DataTable();
                      table.row('#attrRow'+id).remove().draw( false );
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
      function manageAttrVal(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>AttrValues",
          data: 'attr_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            var attribute = $("#attrRow"+id).find("td:eq(2)").text();
            var attr_type = $("#attrRow"+id).find('td').eq(3).attr('attr-type');
            if (attr_type=='3'||attr_type=='4') {
              if (!$( "#attrVal" ).hasClass( "colorPick" )) {
                $("#attrVal").addClass( "colorPick" );
              }
              $('#attrVal').attr('pattern','^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$');
              $(".colorPick").spectrum({
                preferredFormat: "hex",
                showInput: true,
                allowEmpty:true,
                replacerClassName: 'myClass'
              });
              $(".colorPick").show();
            }else{
              $(".colorPick").spectrum("destroy");
              if ($( "#attrVal" ).hasClass( "colorPick" )) {
                $("#attrVal").removeClass( "colorPick" );
              }
              $('#attrVal').removeAttr("pattern");
            }
            $("#val_attr_id").val(id);
            $("#atValTitle").text(attribute+" ערכי תכונה");
            $('#attrValTbody').empty();
            var tbody = '';
            if (responsedata.attr_values.length==0) {
              $('#attrValTbody').append('<tr><td colspan="5" class="text-center">אין תוצאות</td></tr>');
            }else{
              for (var i = 0; i < responsedata.attr_values.length; i++) {
                var status = '';
                <?php if($attr_val_status){?>
                if (responsedata.attr_values[i]['status']==0) {
                  status = 'onchange="updateAttrValStatus('+responsedata.attr_values[i]['av_id']+');" checked="checked"';
                }else{
                  status = 'onchange="updateAttrValStatus('+responsedata.attr_values[i]['av_id']+');"';
                }
                <?php }else{ ?>
                  if (responsedata.attr_values[i]['status']==0) {
                    status = 'checked="checked" disabled="disabled"';
                  }else{
                    status = 'disabled="disabled"';
                  }
                <?php } ?>
                tbody+='<tr id="attrValRow'+responsedata.attr_values[i]['av_id']+'"><td>'+responsedata.attr_values[i]['value']+'</td>'+
                  '<td>'+responsedata.attr_values[i]['description']+'</td>'+
                  '<td attr_val_status="'+responsedata.attr_values[i]['status']+'"><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" '+status+'></td>'+
                  <?php if($edit_attr_val || $delete_attr_val){ ?>
                  '<td align="right">'+
                    <?php if($edit_attr_val){ ?>
                    '<button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editAttrVal('+responsedata.attr_values[i]['av_id']+');"><i class="zmdi zmdi-edit"></i></button>'+
                    <?php } if($delete_attr_val){ ?>
                    '<button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteAttrVal('+responsedata.attr_values[i]['av_id']+');"><i class="zmdi zmdi-delete"></i></button>'+
                    <?php } ?>
                  '</td>'+
                  <?php } ?>
                  '</tr>';
              }
              $('#attrValTbody').append(tbody);
              $('.js-switch').each(function() {
                if (!$(this).data('switchery')) {
                  new Switchery(this, { size: 'small', color: '#34a853' });
                }
              });
            }
            $('#attrValMain').show();
          }
        });
      }

      function closeValMain() {
        $('#attrValMain').hide();
      }
      function addAttrVal() {
        $('#modal-val-title').text('הוסף ערך תכונה');
        $('#attrVal, #attrValDesc').val("");
        $("#attr_val_id").val(0);
        $("#status_off").parent().removeClass('active');
        $("#status_on").parent().addClass('active');
        $("#status_on").attr('checked', 'checked');
      }
      function editAttrVal(id) {
        var value = $("#attrValRow"+id).find("td:eq(0)").text();
        var description = $("#attrValRow"+id).find("td:eq(1)").text();
        var attr_val_status = $("#attrValRow"+id).find("td:eq(2)").attr('attr_val_status');
        $('#modal-val-title').text('עדכן ערך תכונה');
        $("#attr_val_id").val(id);
        $('#attrVal').val(value);
        $('#attrValDesc').val(description);
        $("#status_off").parent().removeClass('active');
        $("#status_on").parent().removeClass('active');
        if (attr_val_status==0) {
          $("#status_on").parent().addClass('active');
          $("#status_on").attr('checked', 'checked');
        }else{
          $("#status_off").parent().addClass('active');
          $("#status_off").attr('checked', 'checked');
        }
        $("#attrValModal").modal('show');
      }
      function updateAttrValStatus(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>updateAttrValStatus",
          data: 'av_id='+id,
          success: function(result) {
            var responsedata = $.parseJSON(result);
            if (responsedata.status=='success') {
              toastr.success(responsedata.message)
            }else{
              toastr.error(responsedata.message)
            }
          },
          error: function(result) {
            toastr.error("משהו השתבש :(")
          }
        });
      }
      $('#attrValMasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#attrValMasks');
            $.ajax({
              type: "POST",
              url: "<?=base_url()?>addAttributeVal",
              data: $('#attrValMasks').serialize(),
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
                  toastr.error("משהו השתבש :(")
                }
                $("#attrValModal").modal('hide');
                $('#attrValMasks').waitMe('hide');
              },
              error: function(result) {
                $('#attrValMasks').waitMe('hide');
                toastr.error('שגיאה: '+result)
              }
          });
        }
      });
      function deleteAttrVal(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>כן</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>לא</button>",'האם ברצונך למחוק ערך תכונה זה?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteAttrValue",
                  data: 'av_id='+id,
                  success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                      var table = $('#table-1').DataTable();
                      table.row('#attrValRow'+id).remove().draw( false );
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