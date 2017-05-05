
<style>
    #loading{
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('<?php echo $image_loading;
            ?>') 50% 50% no-repeat;
    }
</style>
<script type="text/javascript">
    completeCallback = "<?php echo $complete_callback; ?>";
    cancelCallback = "<?php echo $cancel_callback; ?>";
    function errorCallback(error) {
        console.log(JSON.stringify(error))
        alert(JSON.stringify(error.explanation));
    }
</script>
<script src="<?php echo $commweb->_checkout_url_js; ?>" 
        data-error="errorCallback"
        data-complete="completeCallback"
        data-cancel="cancelCallback">
</script>

<script type="text/javascript">
    Checkout.configure({
        merchant: "<?php echo $commweb->commweb_merchant_id; ?>",
        session: {
            id: "<?php echo $checkout_session_id; ?>"
        },
        order: {
            amount: "<?php echo $total; ?>",
            currency: "AUD",
            description: "Commweb Order",
            id: "<?php echo $id_for_commweb; ?>"
        },
        billing: {
            address: {
                street: "<?php echo $street; ?>",
                city: "<?php echo $city; ?>",
                postcodeZip: "<?php echo $billing_postcode; ?>",
                stateProvince: "<?php echo $state; ?>",
                country: "<?php echo $country; ?>"
            }
        },
        interaction: {
            merchant: {
                name: "<?php echo $commweb->merchant_name; ?>"
            }
        }
    });
                <?php echo $payment_method; ?>
</script>
<div id="loading"></div>
</div>

