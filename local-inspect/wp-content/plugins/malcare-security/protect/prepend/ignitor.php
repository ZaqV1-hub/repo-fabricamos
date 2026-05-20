<?php
if (!defined('MCDATAPATH')) exit;

if (defined('MCCONFKEY')) {
	require_once dirname( __FILE__ ) . '/../protect.php';

	MCProtect_V636::init(MCProtect_V636::MODE_PREPEND);
}