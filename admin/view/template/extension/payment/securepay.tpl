<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="securepay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
  </div>
  <div class="container-fluid">
      <?php if (isset($error['error_warning'])) { ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['error_warning']; ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      <?php } ?>
      <div class="panel panel-default">
          <div class="panel-heading">
              <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
          </div>
          <div class="panel-body">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="securepay">
		 <ul class="nav nav-tabs" id="tabs">
            <li class="active"><a href="#tab-api" data-toggle="tab">API info</a></li>
            <li><a href="#tab-more-settings" data-toggle="tab">More Settings</a></li>
          </ul>
		  <div class="tab-content">
            <div class="tab-pane active" id="tab-api">
                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="securepay_merchant_id"><?php echo $entry_merchant_id; ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="securepay_merchant_id" value="<?php echo $securepay_merchant_id; ?>" placeholder="<?php echo $securepay_merchant_id; ?>" id="securepay_merchant_id" class="form-control"/>
                        <?php if ($error_merchant_id) { ?>
                        <div class="text-danger"><?php echo $error_merchant_id; ?></div>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="securepay_transaction_password"><?php echo $entry_transaction_password; ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="securepay_transaction_password" value="<?php echo $securepay_transaction_password; ?>" placeholder="<?php echo $securepay_transaction_password; ?>" id="securepay_transaction_password" class="form-control"/>
                        <?php if ($error_transaction_password) { ?>
                        <div class="text-danger"><?php echo $error_transaction_password; ?></div>
                        <?php } ?>
                    </div>
                </div>
				
				
				
				<div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $entry_enviroment; ?></label>
                    <div class="col-sm-10">
                       <select name="securepay_enviroment" id="securepay_enviroment" class="form-control">
						  <?php if ($securepay_enviroment == 'live') { ?>
						  <option value="live" selected="selected">Live</option>
						  <option value="test">Test</option>
						  <?php } else { ?>
						  <option value="live">Live</option>
						  <option value="test" selected="selected">Test</option>
						  <?php } ?>
						</select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $entry_order_status; ?></label>
                    <div class="col-sm-10">
                        <select name="securepay_order_status_id" class="form-control">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $securepay_order_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-sm-2 control-label">Refund Order Status</label>
                    <div class="col-sm-10">
                        <select name="securepay_refund_status_id" class="form-control">
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $securepay_refund_status_id) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $entry_geo_zone; ?></label>
                    <div class="col-sm-10">
                        <select name="securepay_geo_zone_id" class="form-control">
                            <option value="0"><?php echo $text_all_zones; ?></option>
                            <?php foreach ($geo_zones as $geo_zone) { ?>
                            <?php if ($geo_zone['geo_zone_id'] == $securepay_geo_zone_id) { ?>
                            <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
                    <div class="col-sm-10">
                        <select name="securepay_status" class="form-control">
                            <?php if ($securepay_status) { ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="securepay_sort_order"><?php echo $entry_sort_order; ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="securepay_sort_order" value="<?php echo $securepay_sort_order; ?>" placeholder="<?php echo $securepay_sort_order; ?>" id="securepay_sort_order" class="form-control"/>
                    </div>
                </div>
			</div>
			<div class="tab-pane" id="tab-more-settings">
				<div class="table-responsive">
				<div class="form-group">
                    <label class="col-sm-2 control-label" for="securepay_currency"><?php echo $entry_currency; ?></label>
                    <div class="col-sm-10">
						<select name="securepay_currency" id="securepay_currency" class="form-control">
						<?php foreach ($currency_codes as $code) { ?>
						<?php if ($code == $securepay_currency) { ?>
						<option value="<?php echo $code; ?>" selected="selected"><?php echo $code; ?></option>
						<?php } else { ?>
						<option value="<?php echo $code; ?>"><?php echo $code; ?></option>
						<?php } ?>
						<?php } ?>
					  </select>
                    </div>
                </div>
				<br clear="all"/>
				<div class="form-group">
                    <label class="col-sm-2 control-label" for="securepay_display_cardholder_name"><?php echo $entry_display_cardholder_name; ?></label>
                    <div class="col-sm-10">
						<input type="checkbox" name="securepay_display_cardholder_name" id="securepay_display_cardholder_name" value="1" <?php if ($securepay_display_cardholder_name == 1) { echo 'checked="checked" '; } ?>>
                    </div>
                </div>
				<br clear="all"/>
				<div class="form-group">
                    <label class="col-sm-2 control-label" for="securepay_display_securepay_receipt"><?php echo $entry_display_securepay_receipt; ?></label>
                    <div class="col-sm-10">
						<input type="checkbox" name="securepay_display_securepay_receipt" id="securepay_display_securepay_receipt" value="1" <?php if ($securepay_display_securepay_receipt == 1) { echo 'checked="checked" '; } ?>>
                    </div>
                </div>
				<br clear="all"/>
				<div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $entry_template_type; ?></label>
                    <div class="col-sm-10">
                       <select name="securepay_template_type" id="securepay_template_type" class="form-control">
						  <option value="iframe" <?php if ($securepay_template_type == 'iframe') { echo 'selected="selected"'; } ?>>Iframe</option>
						  <option value="default" <?php if ($securepay_template_type == 'default') { echo 'selected="selected"'; } ?>>Default</option>
						</select>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-sm-2 control-label" for="securepay_iframe_width"><?php echo $entry_iframe_width; ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="securepay_iframe_width" value="<?php echo $securepay_iframe_width; ?>" placeholder="<?php echo $securepay_iframe_width; ?>" id="securepay_iframe_width" class="form-control"/>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-sm-2 control-label" for="securepay_iframe_height"><?php echo $entry_iframe_height; ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="securepay_iframe_height" value="<?php echo $securepay_iframe_height; ?>" placeholder="<?php echo $securepay_iframe_height; ?>" id="securepay_iframe_height" class="form-control"/>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $entry_transaction_type; ?></label>
                    <div class="col-sm-10">
                       <select name="securepay_transaction_type" id="securepay_transaction_type" class="form-control">
						  <option value="0" <?php if ($securepay_template_type == '0') { echo 'selected="selected"'; } ?>>PAYMENT</option>
						  <option value="1" <?php if ($securepay_template_type == '1') { echo 'selected="selected"'; } ?>>PREAUTH</option>
						  <option value="2" <?php if ($securepay_template_type == '2') { echo 'selected="selected"'; } ?>>PAYMENT with FRAUDGUARD</option>
						  <option value="3" <?php if ($securepay_template_type == '3') { echo 'selected="selected"'; } ?>>PREAUTH with FRAUDGUARD</option>
						  <option value="4" <?php if ($securepay_template_type == '4') { echo 'selected="selected"'; } ?>>PAYMENT with 3D Secure</option>
						  <option value="5" <?php if ($securepay_template_type == '5') { echo 'selected="selected"'; } ?>>PREAUTH with 3D Secure</option>
						  <option value="6" <?php if ($securepay_template_type == '6') { echo 'selected="selected"'; } ?>>PAYMENT with FRAUDGUARD and 3D Secure</option>
						  <option value="7" <?php if ($securepay_template_type == '7') { echo 'selected="selected"'; } ?>>PREAUTH with FRAUDGUARD and 3D Secure</option>
						</select>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-sm-2 control-label">Surcharge</label>
                    <div class="col-sm-10"><br />
                       <strong>Enable Surcharge:</strong> <input name="securepay_surcharge" id="securepay_surcharge" value="1" type="checkbox" <?php if ($securepay_surcharge == 1) { echo 'checked="checked" '; } ?>><br /><br />
						<table class="table table-striped table-bordered table-hover">
						  <thead>
							<tr>
							  <td class="text-left">Card Type</td>
							  <td class="text-center">Surcharge Type</td>
							  <td class="text-center">Surcharge Value</td>
							</tr>
						  </thead>
						  <tbody>
							<tr>
							  <td class="text-left">Visa</td>
							  <td class="text-center">
								<select name="securepay_surcharge_visa" id="securepay_surcharge_visa">
									<option value="" <?php if($securepay_surcharge_visa == '') echo 'selected="selected"';?>>None</option>
									<option value="flat" <?php if($securepay_surcharge_visa == 'flat') echo 'selected="selected"';?>>Flat Fee</option>
									<option value="percentage" <?php if($securepay_surcharge_visa == 'percentage') echo 'selected="selected"';?>>Percentage</option>
								</select>
							  </td>
							  <td class="text-right"><input type="text" name="securepay_surcharge_visa_value" value="<?php echo $securepay_surcharge_visa_value; ?>" class="form-control" /></td>
							</tr>
							<tr>
							  <td class="text-left">Mastercard</td>
							  <td class="text-center">
								<select name="securepay_surcharge_mastercard" id="securepay_surcharge_mastercard">
									<option value="" <?php if($securepay_surcharge_mastercard == '') echo 'selected="selected"';?>>None</option>
									<option value="flat" <?php if($securepay_surcharge_mastercard == 'flat') echo 'selected="selected"';?>>Flat Fee</option>
									<option value="percentage" <?php if($securepay_surcharge_mastercard == 'percentage') echo 'selected="selected"';?>>Percentage</option>
								</select>
							  </td>
							  <td class="text-right"><input type="text" name="securepay_surcharge_mastercard_value" value="<?php echo $securepay_surcharge_mastercard_value; ?>" class="form-control" /></td>
							</tr>
							<tr>
							  <td class="text-left">American Express</td>
							  <td class="text-center">
								<select name="securepay_surcharge_amex" id="securepay_surcharge_amex">
									<option value="" <?php if($securepay_surcharge_amex == '') echo 'selected="selected"';?>>None</option>
									<option value="flat" <?php if($securepay_surcharge_amex == 'flat') echo 'selected="selected"';?>>Flat Fee</option>
									<option value="percentage" <?php if($securepay_surcharge_amex == 'percentage') echo 'selected="selected"';?>>Percentage</option>
								</select>
							  </td>
							  <td class="text-right"><input type="text" name="securepay_surcharge_amex_value" value="<?php echo $securepay_surcharge_amex_value; ?>" class="form-control" /></td>
							</tr>
						   
						  </tbody>
						</table>
                    </div>
                </div>
              </div>
			</div>
		</div>
    </form>
  </div></div>
  </div>
</div>
  <script type="text/javascript"><!--
$('#tabs a:first').tab('show');
//--></script></div>
<?php echo $footer; ?>