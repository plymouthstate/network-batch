<?php
/*
Plugin Name: Network Batch
Plugin URI: 
Description: Run a callback on all blogs in your network.
Version: 0.1
Author: Adam Backstrom
Author URI: http://sixohthee.com/
Network: true
License: GPL2
*/

// Copyright (C) 2011 Adam Backstrom
// 
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

class S03_NetworkBatch {
	public function __construct() {
		add_action( 'network_admin_menu', array( $this, 'action_network_admin_menu' ) );
	}

	public function action_network_admin_menu() {
		add_plugins_page( 'Network Batch', 'Network Batch', 'network-batch', 'network_batch', array( $this, 'network_page' ) );
	}

	public function network_page() {
		define( 'DOING_NETWORKBATCH', true );

		if ( isset( $_GET['callback'] ) ) {
			$this->network_process();
			return true;
		}

		include __DIR__ . '/pages/network_page.php';
	}

	public function network_page_url() {
		return 'plugins.php?page=network_batch';
	}

	/**
	 * Run callbacks on blogs. Portions taken from WordPress 3.2.1
	 * wp-admin/network/upgrade.php.
	 */
	public function network_process() {
		global $wpdb;

		$n        = isset( $_GET['n'] ) ? intval( $_GET['n'] ) : 0;
		$callback = maybe_unserialize( $_GET['callback'] );

		if( get_site_option( 'network_batch_callback' ) ) {
			update_site_option( 'network_batch_callback', $callback );
		} else {
			add_site_option( 'network_batch_callback', $callback );
		}

		// TODO: timer to make sure things are fresh
		// TODO: detect existing run, don't overwrite callback

		$blogs = $wpdb->get_results( "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' AND spam = '0' AND deleted = '0' AND archived = '0' ORDER BY registered DESC LIMIT {$n}, 5", ARRAY_A );
		if ( empty( $blogs ) ) {
			echo '<p>' . __( 'All done!' ) . '</p>';
			return true;
		}
		echo "<ul>";
		foreach ( (array) $blogs as $details ) {
			$siteurl = get_blog_option( $details['blog_id'], 'siteurl' );
			echo "<li>$siteurl</li>";
			$response = wp_remote_get( trailingslashit( $siteurl ) . "wp-admin/upgrade.php?step=upgrade_db&do_network_batch=1", array( 'timeout' => 120, 'httpversion' => '1.1' ) );
			if ( is_wp_error( $response ) )
				wp_die( sprintf( __( 'Warning! Problem updating %1$s. Your server may not be able to connect to sites running on it. Error message: <em>%2$s</em>' ), $siteurl, $response->get_error_message() ) );
		}
		echo "</ul>";
		?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="plugins.php?page=network_batch&amp;callback=<?php echo urlencode( $callback ); ?>&amp;n=<?php echo ($n + 5) ?>"><?php _e("Next Sites"); ?></a></p>
		<script type='text/javascript'>
		<!--
		function nextpage() {
			location.href = "plugins.php?page=network_batch&callback=<?php echo urlencode( $callback ); ?>&n=<?php echo ($n + 5) ?>";
		}
		setTimeout( "nextpage()", 250 );
		//-->
		</script><?php
	}

	public static function singleton() {
		static $obj = null;

		if ( null === $obj )
			$obj = new self;

		return $obj;
	}

	public function wp_upgrade() {
		$callback = get_site_option( 'network_batch_callback' );

		call_user_func( $callback );

		die( '0' );
	}
}

call_user_func( array('S03_NetworkBatch', 'singleton') );

/**
 * Redefine wp_upgrade if we are in network batch mode
 */
if( isset( $_GET['step'] ) && 'upgrade_db' === $_GET['step'] &&
	isset( $_GET['do_network_batch'] ) && '1' === $_GET['do_network_batch'] ):

function wp_upgrade() {
	S03_NetworkBatch::singleton()->wp_upgrade();
}

endif;
