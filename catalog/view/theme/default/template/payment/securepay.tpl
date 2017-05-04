<?php
echo $iframe;
?>
<form id="securepay_form" action="<?php echo $gateway_url; ?>" method="post" <?php echo $target; ?>>
<?php
foreach($parameters as $key=>$value){
	?>
	<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
	<?php
}
?>
<div class="buttons">
    <div class="pull-right">
        <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" id="securepay_submit"/>
    </div>
</div>
</form>
<script type="text/javascript">
$(document).ready(function() {
	$("#securepay_submit").click(function() {
		$("#securepay_chekout_frame").show('slow');
		$("#securepay_submit").hide();
	});
});
</script>