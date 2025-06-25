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
              <?php if($add_user){?>
              <div class="panel-tools">
                <button type="button" class="btn btn-outline-primary m-w-120" onclick="location.href='<?=base_url();?>addUser'">Add User</button>
              </div>
              <?php }?>
              <h3 class="m-t-0 m-b-5">User Management</h3>
            </div>
            <div class="panel-body">
              <div class="table-responsive m-y-5">
                <table class="table table-hover" id="table-1">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Name</th>
                      <th>Company</th>
                      <th>NIC</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Username</th>
                      <th>Role</th>
                      <th>Status</th>
                      <?php if($edit_user||$delete_user){?><th></th><?php }?>
                    </tr>
                  </thead>
                  <tbody id="tbody_data">
                    <?php foreach ($staff_users as $row) { 
                      $img = 'default.jpg';
                      $status = '';
                      $access_group = $row->group_desc;
                      if ($row->photo_path!=null) {
                        $img = 'staff/'.$row->photo_path.'-sma.jpg';
                      }
                      if ($row->userStatus==0) {
                        $status = 'checked="checked"';
                      }
                      if ($row->group_id==''||$row->group_id==null||$row->group_id==0) {
                        $access_group = 'None';
                      }
                    ?>

                    <tr id="userrow<?=$row->user_id?>">
                      <td><img class="img-rounded" src="<?=base_url();?>photos/<?=$img?>" height="32"></td>
                      <td><?=$row->fname.' '.$row->lname;?></td>
                      <td><?=$row->company_name;?></td>
                      <td><?=$row->nic;?></td>
                      <td><?=$row->email;?></td>
                      <td><?=$row->phone;?></td>
                      <td><?=$row->username;?></td>
                      <td><?=$access_group;?></td>
                      <td><input type="checkbox" class="js-switch" data-size="small" data-color="#34a853" <?=$status;?> <?php if ($changeStatus) {echo 'onchange="updateUserStatus('.$row->user_id.');"';}else{echo "disabled";}?> ></td>
                      <?php if($edit_user||$delete_user){?>
                      <td><div class="btn-group">
                          <button type="button" class="btn btn-primary btn-pill btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="zmdi zmdi-more"></i>
                          </button>
                          <ul class="dropdown-menu dropdown-menu-right">
                            <?php if($edit_user){?>
                            <li><a href="javascript:editUser(<?=$row->user_id?>);">Edit</a></li>
                            <?php } if($delete_user){?>
                            <li><a href="javascript:deleteUser(<?=$row->user_id?>);">Delete</a></li>
                            <?php }?>
                          </ul>
                        </div></td><?php }?>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
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
        toastr.warning("<button type='button' id='confirmBtn' class='btn btn-danger btn-sm' style='width:40%;display:inline;margin:3px;'>Yes</button><button type='button' id='closeBtn' class='btn btn-default btn-sm' style='width:40%;display:inline;margin:3px;'>No</button>",'Do you want to delete this user?',{
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
        form.setAttribute("action", "<?=base_url()?>editUser");

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