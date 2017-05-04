<h2>Payment information</h2>
<div class="alert alert-success" id="securepay-transaction-msg" style="display:none;"></div>
<table class="table table-striped table-bordered">
  <tr>
    <td>Transaction ID</td>
    <td><?php echo $securepay_order['transaction_id']; ?></td>
  </tr>
  <tr>
    <td>Order Total</td>
    <td>$<?php echo $securepay_order['total']; ?></td>
  </tr>
  <tr>
    <td>Refund Status</td>
    <td id="rebate_status">
      <?php if ($securepay_order['rebate_status'] == 1) { ?>
        <span class="rebate_text">Yes</span>
      <?php } else { ?>
        <span class="rebate_text">No</span>&nbsp;&nbsp;

         <a class="button btn btn-primary" id="button-rebate">Refund Transaction</a>
         <span class="btn btn-primary" id="loading-rebate" style="display:none;"><i class="fa fa-circle-o-notch fa-spin fa-lg"></i></span>
	<?php } ?>
    </td>
  </tr>
</table>
<script type="text/javascript"><!--
  $("#button-rebate").click(function () {
    if (confirm('Are you sure you want to refund?')) {
      $.ajax({
        type:'POST',
        dataType: 'json',
        data: {'order_id': '<?php echo $order_id; ?>'},
        url: 'index.php?route=extension/payment/securepay/rebate&token=<?php echo $token; ?>',
        beforeSend: function() {
          $('#button-rebate').hide();
          $('#loading-rebate').show();
          $('#securepay-transaction-msg').hide();
        },
        success: function(data) {
          if (data.error == false) {

            if (data.data.rebate_status == 1) {
              $('.rebate_text').text('Yes');
            } else {
              $('#button-rebate').show();
            }

            if (data.msg != '') {
              $('#securepay-transaction-msg').empty().html('<i class="fa fa-check-circle"></i> '+data.msg).fadeIn();
            }
          }
          if (data.error == true) {
            alert(data.msg);
            $('#button-rebate').show();
          }

          $('#loading-rebate').hide();
        }
      });
    }
  });
//--></script>