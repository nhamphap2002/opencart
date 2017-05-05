<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="commweb" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="commweb">
                    <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="#tab-api" data-toggle="tab">API info</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-api">
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="commweb_merchant_id"><?php echo $entry_merchant_id; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="commweb_merchant_id" value="<?php echo $commweb_merchant_id; ?>" placeholder="<?php echo $commweb_merchant_id; ?>" id="commweb_merchant_id" class="form-control"/>
                                    <?php if ($error_merchant_id) { ?>
                                    <div class="text-danger"><?php echo $error_merchant_id; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="commweb_api_password"><?php echo $entry_api_password; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="commweb_api_password" value="<?php echo $commweb_api_password; ?>" placeholder="<?php echo $commweb_api_password; ?>" id="commweb_api_password" class="form-control"/>
                                    <?php if ($error_api_password) { ?>
                                    <div class="text-danger"><?php echo $error_api_password; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_checkout_method; ?></label>
                                <div class="col-sm-10">
                                    <select name="commweb_checkout_method" id="commweb_checkout_method" class="form-control">
                                        <?php if ($commweb_checkout_method == 'Lightbox') { ?>
                                        <option value="Lightbox" selected="selected">Lightbox</option>
                                        <option value="part">Redirect</option>
                                        <?php } else { ?>
                                        <option value="Lightbox">Lightbox</option>
                                        <option value="part" selected="selected">Redirect</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_3d_secure; ?></label>
                                <div class="col-sm-10">
                                    <input type="checkbox" name="commweb_3d_secure" value="<?php echo $commweb_3d_secure; ?>" <?php if($commweb_3d_secure) echo 'checked="checked"';?>  placeholder="<?php echo $commweb_3d_secure; ?>" id="commweb_3d_secure" class="form-control"/>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_debug_log; ?></label>
                                <div class="col-sm-10">
                                    <input type="checkbox" name="commweb_debug_log" value="<?php echo $commweb_debug_log; ?>" <?php if($commweb_debug_log) echo 'checked="checked"';?> placeholder="<?php echo $commweb_debug_log; ?>" id="commweb_debug_log" class="form-control"/>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_order_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="commweb_order_status_id" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $commweb_order_status_id) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                                <div class="col-sm-10">
                                    <select name="commweb_geo_zone_id" id="input-geo-zone" class="form-control">
                                        <option value="0"><?php echo $text_all_zones; ?></option>
                                        <?php foreach ($geo_zones as $geo_zone) { ?>
                                        <?php if ($geo_zone['geo_zone_id'] == $commweb_geo_zone_id) { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="commweb_status" class="form-control">
                                        <?php if ($commweb_status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="commweb_sort_order"><?php echo $entry_sort_order; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="commweb_sort_order" value="<?php echo $commweb_sort_order; ?>" placeholder="<?php echo $commweb_sort_order; ?>" id="commweb_sort_order" class="form-control"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        $('#commweb_3d_secure').change(function () {
            if ($(this).is(":checked")) {
                $(this).val(1);
            } else {
                $(this).val(0);
            }
        })
        $('#commweb_debug_log').change(function () {
            if ($(this).is(":checked")) {
                $(this).val(1);
            } else {
                $(this).val(0);
            }
        })
    })
</script>
<?php echo $footer; ?>