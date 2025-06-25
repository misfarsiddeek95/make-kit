<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>   
<script type="text/javascript">
    function onclickEvent(url){
       window.location =  url;
    }
</script>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12 m-y-5">
        <div class="btn-group" data-toggle="buttons">
            <?php foreach ($events as $event){ ?>  
            <label class="btn btn-outline-primary <?php if($current_evn->optid==$event->optid){echo 'active';}?>" onclick="onclickEvent('<?= base_url($programme->url.'/'.DEFAULT_METHOD.'/'.$programme->optid.'/'.$event->optid)?>')">
                    <input type="radio" id="<?=$event->description?>" autocomplete="off" > <?=$event->description?>
            </label>
            <?php } ?>                                    
        </div>
    </div>
</div>
