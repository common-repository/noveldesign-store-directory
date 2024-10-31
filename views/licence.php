<?php
    $carousel_status = esc_html(get_option('WPM_CAROUSEL_STATUS'));
    $discover_feature = esc_html(get_option('WPM_DISCOVER_FEATURE'));
    $show_trading_hours = esc_html(get_option('WPM_TRADING_HOURS'));
    
    $WMP_TH_MON_FROM = esc_html(get_option('WMP_TH_MON_FROM'));
    $WMP_TH_MON_TO = esc_html(get_option('WMP_TH_MON_TO'));
    
    $WMP_TH_TUE_FROM = esc_html(get_option('WMP_TH_TUE_FROM'));
    $WMP_TH_TUE_TO = esc_html(get_option('WMP_TH_TUE_TO'));
    
    $WMP_TH_WED_FROM = esc_html(get_option('WMP_TH_WED_FROM'));
    $WMP_TH_WED_TO = esc_html(get_option('WMP_TH_WED_TO'));
    
    $WMP_TH_THU_FROM = esc_html(get_option('WMP_TH_THU_FROM'));
    $WMP_TH_THU_TO = esc_html(get_option('WMP_TH_THU_TO'));
    
    $WMP_TH_FRI_FROM = esc_html(get_option('WMP_TH_FRI_FROM'));
    $WMP_TH_FRI_TO = esc_html(get_option('WMP_TH_FRI_TO'));
    
    $WMP_TH_SAT_FROM = esc_html(get_option('WMP_TH_SAT_FROM'));
    $WMP_TH_SAT_TO = esc_html(get_option('WMP_TH_SAT_TO'));
    
    $WMP_TH_SUN_FROM = esc_html(get_option('WMP_TH_SUN_FROM'));
    $WMP_TH_SUN_TO = esc_html(get_option('WMP_TH_SUN_TO'));
    
    $WMP_PUBLIC_HOLIDAY = esc_html(get_option('WMP_PUBLIC_HOLIDAY'));
    $WMP_PUBLIC_HOLIDAY_MSG = esc_html(get_option('WMP_PUBLIC_HOLIDAY_MSG'));
      
?>
<div class="wrap">
    <div class="row">
        <form method="POST" enctype="multipart/form-data">
            <div class="col-md-12 head">        
                <h2><?php echo __('Settings', 'shop_directory'); ?></h2>
                <table>
                    <tr>
                        <td><p class="blue-text"><?php echo __('Show shop logos in carousel', 'shop_directory'); ?></p></td>
                        <td>
                            <select name="carouselstatus" id="carouselstatus" class="" style="width:150px">
                                <option value=""><?php echo __('- Select -', 'shop_directory'); ?></option>
                                <option value="1"<?php echo (($carousel_status == 1)?' selected="selected"':''); ?>><?php echo __('Enable', 'shop_directory'); ?></option>
                                <option value="0"<?php echo (($carousel_status == 0)?' selected="selected"':''); ?>><?php echo __('Disable', 'shop_directory'); ?></option>
                            </select>
                        </td>
                        <td>
                            <button type="submit" name="savecarousel" id="savecarousel" class="button button-primary"><?php echo __('Save', 'shop_directory'); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <td><p class="blue-text"><?php echo __('Show discover feature on shop listings', 'shop_directory'); ?></p></td>
                        <td>
                            <select name="discover_feature" id="discover_feature" class="" style="width:150px">
                                <option value=""><?php echo __('- Select -', 'shop_directory'); ?></option>
                                <option value="1"<?php echo (($discover_feature == 1)?' selected="selected"':''); ?>><?php echo __('Enable', 'shop_directory'); ?></option>
                                <option value="0"<?php echo (($discover_feature == 0)?' selected="selected"':''); ?>><?php echo __('Disable', 'shop_directory'); ?></option>
                            </select>
                        </td>
                        <td>
                            <button type="submit" name="save_discover_feature" id="save_discover_feature" class="button button-primary"><?php echo __('Save', 'shop_directory'); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <td><p class="blue-text"><?php echo __('Show trading hours', 'shop_directory'); ?></p></td>
                        <td>
                            <select name="show_trading_hours" id="show_trading_hours" class="" style="width:150px">
                                <option value=""><?php echo __('- Select -', 'shop_directory'); ?></option>
                                <option value="1"<?php echo (($show_trading_hours == 1)?' selected="selected"':''); ?>><?php echo __('Enable', 'shop_directory'); ?></option>
                                <option value="0"<?php echo (($show_trading_hours == 0)?' selected="selected"':''); ?>><?php echo __('Disable', 'shop_directory'); ?></option>
                            </select>
                        </td>
                        <td>
                            <button type="submit" name="save_show_trading_hours" id="save_show_trading_hours" class="button button-primary"><?php echo __('Save', 'shop_directory'); ?></button>
                        </td>
                    </tr>
                </table>
                <div class="panel">
                    <div class="panel-heading"><?php echo __('Trading Hours', 'shop_directory'); ?></div>
                    <div class="panel-body">
                        <table class="table" style="width: 40%;">
                            <tr>
                                <td colspan="2"><?php echo __('Trading Hours Monday', 'shop_directory'); ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" placeholder="i.e. 08h00" name="WMP_TH_MON_FROM" id="WMP_TH_MON_FROM" value="<?php echo esc_html($WMP_TH_MON_FROM); ?>" />
                                </td>
                                <td>
                                    <input type="text"  placeholder="i.e. 20h00" name="WMP_TH_MON_TO" id="WMP_TH_MON_TO" value="<?php echo esc_html($WMP_TH_MON_TO); ?>" />
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="2"><?php echo __('Trading Hours Tuesday', 'shop_directory'); ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" placeholder="i.e. 08h00" name="WMP_TH_TUE_FROM" id="WMP_TH_TUE_FROM" value="<?php echo esc_html($WMP_TH_TUE_FROM); ?>" />
                                </td>
                                <td>
                                    <input type="text" placeholder="i.e. 20h00" name="WMP_TH_TUE_TO" id="WMP_TH_TUE_TO" value="<?php echo esc_html($WMP_TH_TUE_TO); ?>" />
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="2"><?php echo __('Trading Hours Wednesday', 'shop_directory'); ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" placeholder="i.e. 08h00" name="WMP_TH_WED_FROM" id="WMP_TH_WED_FROM" value="<?php echo esc_html($WMP_TH_WED_FROM); ?>" />
                                </td>
                                <td>
                                    <input type="text" placeholder="i.e. 20h00" name="WMP_TH_WED_TO" id="WMP_TH_WED_TO" value="<?php echo esc_html($WMP_TH_WED_TO); ?>" />
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="2"><?php echo __('Trading Hours Thursday', 'shop_directory'); ?></td>
                            </tr>                            
                            <tr>
                                <td>
                                    <input type="text" placeholder="i.e. 08h00" name="WMP_TH_THU_FROM" id="WMP_TH_THU_FROM" value="<?php echo esc_html($WMP_TH_THU_FROM); ?>" />
                                </td>
                                <td>
                                    <input type="text" placeholder="i.e. 20h00" name="WMP_TH_THU_TO" id="WMP_TH_THU_TO" value="<?php echo esc_html($WMP_TH_THU_TO); ?>" />
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="2"><?php echo __('Trading Hours Friday', 'shop_directory'); ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" placeholder="i.e. 08h00" name="WMP_TH_FRI_FROM" id="WMP_TH_FRI_FROM" value="<?php echo esc_html($WMP_TH_FRI_FROM); ?>" />
                                </td>
                                <td>
                                    <input type="text" placeholder="i.e. 20h00" name="WMP_TH_FRI_TO" id="WMP_TH_FRI_TO" value="<?php echo esc_html($WMP_TH_FRI_TO); ?>" />
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="2"><?php echo __('Trading Hours Saturday', 'shop_directory'); ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" placeholder="i.e. 09h00"name="WMP_TH_SAT_FROM" id="WMP_TH_SAT_FROM" value="<?php echo esc_html($WMP_TH_SAT_FROM); ?>" />
                                </td>
                                <td>
                                    <input type="text" placeholder="i.e. 13h00" name="WMP_TH_SAT_TO" id="WMP_TH_SAT_TO" value="<?php echo esc_html($WMP_TH_SAT_TO); ?>" />
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="2"><?php echo __('Trading Hours Sunday', 'shop_directory'); ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" placeholder="i.e. 10h00" name="WMP_TH_SUN_FROM" id="WMP_TH_SUN_FROM" value="<?php echo esc_html($WMP_TH_SUN_FROM); ?>" />
                                </td>
                                <td>
                                    <input type="text" placeholder="i.e. 12h00" name="WMP_TH_SUN_TO" id="WMP_TH_SUN_TO" value="<?php echo esc_html($WMP_TH_SUN_TO); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php echo __('Public Holiday', 'shop_directory'); ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" name="WMP_PUBLIC_HOLIDAY" placeholder="Pick a date." class="uidatepicker" id="WMP_PUBLIC_HOLIDAY" value="<?php echo esc_html($WMP_PUBLIC_HOLIDAY); ?>" />
                                </td>
                                <td>
                                    <input type="text" name="WMP_PUBLIC_HOLIDAY_MSG" placeholder="e.g. * Optional Trading" id="WMP_PUBLIC_HOLIDAY_MSG" value="<?php echo esc_html($WMP_PUBLIC_HOLIDAY_MSG); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="submit" name="save_trading_hours" id="save_trading_hours" class="button button-primary"><?php echo __('Save', 'shop_directory'); ?></button>
                                </td>
                            </tr>
                            
                        </table>
                    </div>
                </div>
                
                <div class="panel">
                    <div class="panel-heading"><?php echo __('Default Shop Image', 'shop_directory'); ?></div>
                    <div class="panel-body">
                        <table class="table">
                            <tr>
                                <td><?php echo __('Change default image', 'shop_directory'); ?></td>
                                <td>
                                    <input type="file" name="default_shop_image" id="default_shop_image" />
                                </td>
                                <td>
                                    <button type="submit" name="btn_default_shop_image" id="btn_default_shop_image" class="button button-primary">
                                        <?php echo __('Upload', 'shop_directory'); ?>
                                    </button>
                                </td>
                            </tr>
                            <?php if(get_option('WMP_DEFAULT_IMAGE')): ?>
                                <tr>
                                    <td colspan="3">
                                        <img src="<?php echo esc_url(plugins_url('/images/'.get_option('WMP_DEFAULT_IMAGE'), dirname(__FILE__))); ?>" style="height:120px;" />
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>