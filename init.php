<?php
/**
 * Tweeter init
 */
function tweeter_activation() {}
register_activation_hook(__FILE__, 'tweeter_activation');

function tweeter_deactivation() {}
register_deactivation_hook(__FILE__, 'tweeter_deactivation');

function tweeter_uninstall() {}
register_uninstall_hook(__FILE__, 'tweeter_uninstall');
