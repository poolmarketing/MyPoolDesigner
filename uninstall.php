<?php
/**
 * Uninstall script for MyPoolDesigner Gallery Plugin
 * 
 * This file is called when the plugin is uninstalled from WordPress.
 * It cleans up all plugin data from the database.
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('mpd_api_key');

// Clean up any transients we may have set
delete_transient('mpd_api_validation_cache');

// Note: We intentionally don't delete user designs or collections data
// as this data belongs to the user's MyPoolDesigner.ai account