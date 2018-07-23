<?php

if ( defined( 'ABSPATH' ) && ! defined( 'BRIDGE_FOUNDATION_VERSION' ) ) {
	require_once dirname( __FILE__ ) . '/Loader.php';
	$loader = new \Bridge\Foundation\Loader();
	$loader->init();
}
