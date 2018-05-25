<?php
defined('ABSPATH') or die;
$post_revisions = 0; $drafted = 0; $trashed = 0; $comments = 0; $trackbacks = 0; $transients = 0;
if (is_multisite()) {
    $sites = get_sites();
    foreach ($sites as $site) {
        switch_to_blog($site->blog_id);
        $post_revisions += (int)Breeze_Configuration::getElementToClean('revisions');
        $drafted        += (int)Breeze_Configuration::getElementToClean('drafted');
	    $trashed        += (int)Breeze_Configuration::getElementToClean('trash');
	    $comments       += (int)Breeze_Configuration::getElementToClean('comments');
	    $trackbacks     += (int)Breeze_Configuration::getElementToClean('trackbacks');
	    $transients     += (int)Breeze_Configuration::getElementToClean('transient');
    }
    restore_current_blog();
} else {
    $post_revisions = (int)Breeze_Configuration::getElementToClean('revisions');
    $drafted        = (int)Breeze_Configuration::getElementToClean('drafted');
	$trashed        = (int)Breeze_Configuration::getElementToClean('trash');
	$comments       = (int)Breeze_Configuration::getElementToClean('comments');
	$trackbacks     = (int)Breeze_Configuration::getElementToClean('trackbacks');
	$transients     = (int)Breeze_Configuration::getElementToClean('transient');
}
?>
<div class="breeze-top-notice">
    <p class="breeze_tool_tip"><?php _e('Important: Backup your databases before using the following options!','breeze')?></p>
</div>
<table cellspacing="15">
    <tr>
        <td>
            <label for="data0" class="breeze_tool_tip"><?php _e('Select all','breeze')?></label>
        </td>
        <td>
            <input type="checkbox" id="data0" name="all_control" value="all_data"/>
            <span class="breeze_tool_tip"><?php _e('Select all following options. Click Optimize to perform actions.','breeze')?></span>
        </td>
    </tr>
    <tr>
        <td>
            <label for="data1" class="breeze_tool_tip"><?php _e('Post revisions','breeze')?><?php echo "&nbsp(".$post_revisions.")"; ?></label>
        </td>
        <td>
            <input type="checkbox" id="data1" name="clean[]" class="clean-data" value="revisions"/>
            <span class="breeze_tool_tip"><?php _e('Use this option to delete all post revisions from the WordPress database.','breeze')?></span>
        </td>
    </tr>
    <tr>
        <td>
            <label for="data2" class="breeze_tool_tip" ><?php _e('Auto drafted content','breeze')?><?php echo "&nbsp(".$drafted.")"; ?></label>
        </td>
        <td>
            <input type="checkbox" id="data2" name="clean[]" class="clean-data" value="drafted"/>
            <span class="breeze_tool_tip"><?php _e('Use this option to delete auto saved drafts from the WordPress database.','breeze')?></span>
        </td>
    </tr>
    <tr>
        <td>
            <label for="data3" class="breeze_tool_tip" ><?php _e('All trashed content','breeze')?><?php echo "&nbsp(".$trashed.")"; ?></label>
        </td>
        <td>
            <input type="checkbox" id="data3" name="clean[]" class="clean-data" value="trash"/>
            <span class="breeze_tool_tip"><?php _e('Use this option to delete all trashed content from the WordPress database.','breeze')?></span>

        </td>
    </tr>
    <tr>
        <td>
            <label for="data4" class="breeze_tool_tip" ><?php _e('Comments from trash & spam','breeze')?><?php echo "&nbsp(".$comments.")"; ?></label>
        </td>
        <td>
            <input type="checkbox" id="data4" name="clean[]" class="clean-data" value="comments"/>
            <span class="breeze_tool_tip"><?php _e('Use this option to delete trash and spam comments from the WordPress database.','breeze')?></span>
        </td>
    </tr>
    <tr>
        <td>
            <label for="data5" class="breeze_tool_tip" ><?php _e('Trackbacks and pingbacks','breeze')?><?php echo "&nbsp(".$trackbacks.")"; ?></label>
        </td>
        <td>
            <input type="checkbox" id="data5" name="clean[]" class="clean-data" value="trackbacks"/>
            <span class="breeze_tool_tip"><?php _e('Use this option to delete Trackbacks and Pingbacks from the WordPress database.','breeze')?></span>
        </td>
    </tr>
    <tr>
        <td>
            <label for="data6" class="breeze_tool_tip" ><?php _e('Transient options','breeze')?><?php echo "&nbsp(".$transients.")"; ?></label>
        </td>
        <td>
            <input type="checkbox" id="data6" name="clean[]" class="clean-data" value="transient"/>
            <span class="breeze_tool_tip"><?php _e('Delete expired and active transients from the WordPress database.','breeze')?></span>
        </td>
    </tr>
</table>
