<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12 m-y-5">
        <div class="btn-group" data-toggle="buttons">

            <?php 
            if (isset($events)) {
                foreach ($events as $event){ 
                    $type = strtok($event->url, '|');
                    $action = substr($event->url, strlen($type)+1);
                    if ($type=='url') {
                        $attr = "location.href = '".$action."';";
                    }elseif ($type=='fun') {
                        $attr = $action."();";
                    }
            ?>  
                    <label class="btn btn-outline-primary eventcls" id="eventId<?=$event->optid?>" onclick="<?=$attr?>">
                        <input type="radio" id="<?=$event->description?>" autocomplete="off"> <?=$event->description?></label>
            <?php } } ?>                                    
        </div>
    </div>
</div>