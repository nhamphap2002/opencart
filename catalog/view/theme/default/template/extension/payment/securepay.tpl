<div class="choose_exist_card_and_save_card_payment option_payment_securepay">
    <input type="radio" name="save_card" value="exists_card"/>
    <label>Pay with card was saved has three last numbers test: xxx33</label>
    <br/>
    <br/>
    <input type="radio" name="save_card" value="save_new_card"/>
    <label>Pay & store new card details</label>
    <br/>
    <br/>
    <input type="radio" name="save_card" value="not_save_card"/>
    <label>Pay & not store card details</label>
    <br/>
    <br/>
</div>
<div> 
    <form id="pay_saved_card_form" name="pay_saved_card_form" action="<?php echo $action_pay_saved_card; ?>"  method="post">
        <input type="hidden" name="order_id" value="<?php echo $orderid ?>"/>
        <input type="hidden" name="payor" value="<?php echo $payor ?>" />
        <input id="submit_secure_xml" style="position: absolute; left: -10000px; top: -10000px;" type="submit">
    </form>
</div>
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

    jQuery(document).ready(function ($) {
        /*$("#securepay_submit").click(function() {
         
         $("#securepay_chekout_frame").show('slow');
         $("#securepay_submit").hide();
         });*/
        $(document).on('click', '#securepay_form #securepay_submit', function (e) {
            var $value = $(document).find('.option_payment_securepay input:checked').attr('value');
            if (typeof $value == 'undefined') {
                e.preventDefault();
                alert("Please choose option payment");
            }
            if ($.trim($value) == 'exists_card') {
                e.preventDefault();
                $('#securepay_chekout_frame').hide();
                $(document).find("#pay_saved_card_form #submit_secure_xml").click();
            } else {
                $('#securepay_chekout_frame').show();
                $("#securepay_form #securepay_submit").click();
            }
        })
        /*$(document).on('change', '.option_payment_securepay input', function () {
         var $value = $(this).attr('value');
         if ($.trim($value) == 'exists_card') {
         $('#securepay_chekout_frame').hide();
         $("#pay_saved_card_form #submit_secure_xml").click();
         } else {
         $('#securepay_chekout_frame').show();
         $("#securepay_form #securepay_submit").click();
         }
         })*/
    });
</script>