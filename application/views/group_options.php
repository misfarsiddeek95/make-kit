<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view('includes/head'); ?>
        <link rel="stylesheet" href="<?=base_url()?>assets/css/gijgo.css">
    </head>
    
    <body class="layout layout-header-fixed layout-left-sidebar-fixed">
        <?php $this->load->view('includes/topbar'); ?>
        <div class="site-main">
            <?php $this->load->view('includes/sidebar'); ?>
            <div class="site-content">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3 class="m-y-0">Access Group Options</h3>
                  </div>
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3" id="access_group_div">
                        <div class="form-group">
                            <select class="custom-select" name="access_group" id="access_group" required="required">
                                <option selected="selected" disabled="disabled">-- Select Access Group --</option>
                                <?php foreach ($access_groups as $row){ ?>
                                <option value="<?php echo $row->group_id; ?>"><?php echo $row->group_desc; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div id="tree"></div>
                        </div>
                        <button class="btn btn-primary btn-block" id="submit_btn" disabled>Submit</button>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <?php $this->load->view('includes/footer'); ?>
        </div>
        <?php $this->load->view('includes/javascripts'); ?>
        <script src="<?=base_url()?>assets/js/gijgo.js"></script>

        <script type="text/javascript">
            var tree;
            $( "#access_group" ).change(function() {
              if($('#access_group').val()!= ""){
                    var group_id= $('#access_group').val();                               
                    if(tree == null || tree == undefined){
                        tree = $('#tree').tree({
                            primaryKey: 'optid',
                            uiLibrary: 'bootstrap',
                            dataSource: { url: '<?= base_url('GroupOptions/get_group_options')?>', data: { group_id: group_id }, method: 'POST' },
                            checkboxes: true,
                            border: true
                        });
                        $('#submit_btn').prop('disabled', false);
                    }else{
                        var treeData = tree.data();
                        treeData.dataSource.data.group_id = group_id;
                        tree.reload(); 
                    }
                }
            });

            $( "#submit_btn" ).click(function() {
                var ids=tree.getCheckedNodes();
                var group_id = $('#access_group').val();
                if (group_id!=''&&group_id!=null&&ids!=''&&ids!=null) {
                    run_waitMe('#access_group_div');
                    $.ajax({
                      type: "POST",
                      url: "<?=base_url()?>GroupOptions/update_group_options",
                      data: 'ids='+ids+'&group_id='+group_id,
                      success: function(result) {
                        var responsedata = $.parseJSON(result);
                        if(responsedata.status=='success'){
                            toastr.success(responsedata.message)
                            setTimeout(function(){
                                location.reload();
                            }, 500);
                        }else{
                            toastr.error(responsedata.message)
                            $('#access_group_div').waitMe('hide');
                        }
                      },
                      error: function(result) {
                        toastr.error("Somthing went wrong :(")
                      }
                    });
                }else{
                    toastr.error("Please select the Access Group.")
                }
            });
        </script>
    </body>
</html>