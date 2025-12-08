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
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-success btn-pill" data-toggle="modal" data-target="#otherModal3" title="Add"  onclick="addAccessGroups();"><i class="zmdi zmdi-plus"></i></button>
              </div>
              <h3 class="m-t-0 m-b-5">קבוצות גישה</h3>
            </div>
            <div class="panel-body">
              <div class="table-responsive m-y-5">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th></th>
                      <th>קוד</th>
                      <th>תיאור</th>
                      <th style="text-align: right;">פעולה</th>
                    </tr>
                  </thead>
                  <tbody id="tbody_data">
                    <?php $i=1; 
                      foreach ($access_groups as $row) {
                        $ids = [1,2,3];
                    ?>
                    <tr id="groupRow<?=$row->group_id?>">
                      <td><?=$i?></td>
                      <td><?=$row->group_code;?></td>
                      <td><?=$row->group_desc;?></td>
                      <td align="right">
                        <button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editGroup('<?=$row->group_id?>');"><i class="zmdi zmdi-edit"></i></button>
                        <?php if(!in_array($row->group_id, $ids)) { ?>
                        <button type="button" class="btn btn-outline-danger btn-pill m-r-5" <?php if($this->session->userdata['staff_logged_in']['group_id'] == $row->group_id){?> disabled <?php } ?> <?php if($this->session->userdata['staff_logged_in']['group_id'] != $row->group_id){?> onclick="deleteGroup(<?=$row->group_id?>);" <?php } ?>"><i class="zmdi zmdi-delete"></i></button>
                        <?php } ?>
                      </td>
                    </tr>
                    <?php $i++; } ?>
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
                <h4 class="modal-title" id="modal-title">קבוצת גישה</h4>
              </div>

              <form data-toggle="validator" id="inputmasks">
                <div class="modal-body">
                    <input type="hidden" name="group_id" id="group_id" value="0">
                    <div class="form-group">
                      <label for="form-control-2" class="control-label">קוד קבוצה</label>
                      <input type="text" class="form-control" pattern=".{3}" data-pattern-error="נדרשים 3 תווים" id="group_code" name="group_code" placeholder="קוד קבוצה" data-required-error="קוד נדרש" required>
                      <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                      <label for="form-control-2" class="control-label">תיאור קבוצה</label>
                      <input type="text" class="form-control" id="group_desc" name="group_desc" placeholder="תיאור קבוצה" data-required-error="תיאור נדרש" required>
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
    <script type="text/javascript">

      $('#inputmasks').validator().on('submit', function (e) {
        if (!(e.isDefaultPrevented())) {
          e.preventDefault();
          run_waitMe('#inputmasks');
          $.ajax({
            type: "POST",
            url: "<?=base_url()?>addAccessGroups",
            data: $('#inputmasks').serialize(),
            success: function(result) {
              var responsedata = $.parseJSON(result);
              if(responsedata.status=='success'){
                toastr.success(responsedata.message)
                $("#otherModal3").modal('hide');
                setTimeout(function(){
                  location.reload();
                }, 1000);
              }else{
                toastr.error("משהו השתבש :(")
              }
            },
            error: function(result) {
              $('#inputmasks').waitMe('hide');
              toastr.error('שגיאה: '+result)
            }
          });
          $('#inputmasks').waitMe('hide');
        }
      });

      function addAccessGroups() {
        $('#modal-title').text('הוסף קבוצת גישה');
        $('#group_id').val(0);
        $('#group_code').val('');
        $('#group_desc').val('');
      }

      function editGroup(id) {
        var code = $("#groupRow"+id).find("td:eq(1)").text();
        var desc = $("#groupRow"+id).find("td:eq(2)").text();
        $('#modal-title').text('עדכן קבוצת גישה');
        $('#group_id').val(id);
        $('#group_code').val(code);
        $('#group_desc').val(desc);
        $("#otherModal3").modal('show');
      }

      function deleteGroup(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>כן</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>לא</button>",'האם ברצונך למחוק קבוצה זו?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteGroups",
                  data: 'group_id='+id,
                  success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                      $('#groupRow'+id).remove();
                      toastr.success(responsedata.message)
                    }else{
                      toastr.error(responsedata.message)
                    }
                  },
                  error: function(result) {
                    toastr.error("משהו השתבש :(")
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