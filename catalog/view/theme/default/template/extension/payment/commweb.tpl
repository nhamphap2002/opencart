<form id="pay_showformcommweb" name="pay_showformcommweb" action="<?php echo $action_showform; ?>"  method="post">
    <input type="hidden" name="order_id" value="<?php echo $orderid ?>"/>
    <input type="hidden" name="payor" value="<?php echo $payor ?>" />
    <div class="buttons">
        <div class="pull-right">
            <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" id="Commweb_submit"/>
        </div>
    </div>
</form>
<script type="text/javascript">

    /*jQuery(document).ready(function ($) {
     $(document).on('click', '#Commweb_form #Commweb_submit', function (e) {
     $(document).find("#pay_showformcommweb #submit_commweb").click();
     })
     });*/
</script>