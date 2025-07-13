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
        <?php if($view_teacher_list){ ?>
        <div class="site-content">
          <div class="panel panel-default panel-table">
            <div class="panel-heading">
              <?php if($add_teacher){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-success btn-pill" title="Add Teacher" onclick="location.href='<?=base_url();?>add-teacher'"><i class="zmdi zmdi-plus"></i></button>
              </div>
              <?php }?>
              <h3 class="m-t-0 m-b-5">Instructor Management</h3>
            </div>
            <div class="panel-body"> 
              <div class="table-responsive m-y-5">
                <table class="table table-hover" id="table-1">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Name</th>
                      <th>Gender</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Username</th>
                      <th>Active Status</th>
                      <?php if($edit_teacher || $delete_teacher){ ?>
                      <th style="text-align:right;">Options</th> 
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="tbody_data">
                    <?php 
                      foreach ($instructor_list as $row) {
                          $img = 'user_default.png';
                          $status = '';
                          if ($row->photo_path!=null) {
                            $img = 'staff/'.$row->photo_path.'-thu.'.$row->extension;
                          }
                          if ($row->userStatus==1) {
                            $status = 'checked="checked"';
                          }
                  
                          $gender='';
                          $typeCls = '';
                          if ($row->gender==0) {
                            $gender = 'Female';
                            $typeCls = 'primary';
                          } elseif($row->gender==1){
                            $gender = 'Male';
                            $typeCls = 'success';
                          }else{
                            $gender = 'Other';
                            $typeCls = 'warning';
                          }
                    ?>

                    <tr id="userrow<?=$row->user_id?>">
                      <td><img class="img-rounded" src="<?=base_url();?>photos/<?=$img?>" height="32"></td>
                      <td><?=$row->fname.' '.$row->lname;?></td>
                      <td><span class="label label-outline-<?=$typeCls?>"><?=$gender?></span></td>
                      <td><?=$row->email;?></td>
                      <td><?=$row->phone;?></td>
                      <td><?=$row->username;?></td>
                      <td>
                        <label class="switch switch-success m-t-10">
                          <input type="checkbox" class="s-input"  <?=$status;?> <?php if ($changeStatus) {echo 'onchange="updateUserStatus('.$row->user_id.');"';}else{echo "disabled";}?> <?php if($this->session->userdata['staff_logged_in']['user_id'] == $row->user_id){echo "disabled";}?>>
                          <span class="s-content">
                            <span class="s-track"></span>
                            <span class="s-handle"></span>
                          </span>
                        </label>
                      </td>
                      <?php if($edit_teacher || $delete_teacher){ ?>
                      <td align="right">
                        <?php if($edit_teacher){ ?>
                          <button type="button" class="btn btn-outline-primary btn-pill m-r-5" onclick="editUser(<?=$row->user_id?>);"><i class="zmdi zmdi-edit"></i></button>
                        <?php } if($delete_teacher){ ?>
                          <button type="button" class="btn btn-outline-danger btn-pill m-r-5" onclick="deleteUser(<?=$row->user_id?>);"><i class="zmdi zmdi-delete"></i></button>
                        <?php } ?>
                      </td>
                      <?php } ?> 
                    </tr>
                    <?php }?>
                  </tbody>
                </table>
              </div>
            </div> 
          </div>

        </div>
        <?php } ?>
        <?php $this->load->view('includes/footer'); ?>
    </div>
    <?php $this->load->view('includes/javascripts'); ?>
    <script type="text/javascript">
      $('#table-1').DataTable();

      $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "inherit" );
      });

      $('.table-responsive').on('hide.bs.dropdown', function () {
        $('.table-responsive').css( "overflow", "auto" );
      });

      function updateUserStatus(id) {
        $.ajax({
          type: "POST",
          url: "<?=base_url()?>updateUserStatus",
          data: 'user_id='+id,
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

      function deleteUser(id) {
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this instructor?',{
            closeButton: true,
            allowHtml: true,
            onShown: function (toast) {
              $("#confirmBtn").click(function(){
                $.ajax({
                  type: "POST",
                  url: "<?=base_url()?>deleteUser",
                  data: 'user_id='+id,
                  success: function(result) {
                    var responsedata = $.parseJSON(result);
                    if (responsedata.status=='success') {
                      var table = $('#table-1').DataTable();
                      table.row('#userrow'+id).remove().draw( false );
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

      function editUser(id) {
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "<?=base_url()?>edit-teacher");

        hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "user_id");
        hiddenField.setAttribute("value", id);
        form.appendChild(hiddenField);

        document.body.appendChild(form);
        form.submit();
      }

    </script>
  </body>
</html>