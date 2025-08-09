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

    function number_without_decimal_points(val) {
		var value = parseFloat(val);
		var num = value.toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		return num;
	}

    // Date format => dd/mm/YYY
	function dmy_date_format(date) {
		var date_val = new Date(date);  
		var dd = date_val.getDate(); 
		var mm = date_val.getMonth() + 1; 

		var yyyy = date_val.getFullYear(); 
		if (dd < 10) { 
			dd = '0' + dd; 
		} 
		if (mm < 10) { 
			mm = '0' + mm; 
		} 
		var date_val = dd + '/' + mm + '/' + yyyy; 
		return date_val;
	}
</script>