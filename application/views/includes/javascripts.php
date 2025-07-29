<script src="<?=base_url()?>assets/js/vendor.js"></script>
<script src="<?=base_url()?>assets/js/cosmos.js"></script>
<script src="<?=base_url()?>assets/js/application.js"></script>  
<script src="<?=base_url()?>assets/js/waitMe.js"></script>
<script src="<?=base_url()?>assets/js/commonScripts.js"></script>
<script type="text/javascript">
	Dropzone.autoDiscover = false;

	function slugifyUrl(string){
        string = string.toLowerCase();
        string = string.replace(/[^a-zA-Z0-9_-]/g, '-');
        string = string.replace("(", "");
        string = string.replace(")", "");
        string = string.replace("---","-");
        string = string.replace("--","-");
        return string;
    }
</script>