<?php

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

/**
 * Structure copied from WordPress 3.2.1 wp-admin/network/settings.php
 */

if ( ! defined( 'DOING_NETWORKBATCH' ) ) {
	die( '-1' );
}

?>

<div class="wrap">
	<?php screen_icon( 'tools' ); ?>
	<h2><?php _e( 'Network Batch' ); ?></h2>
	<p>Specify a callback to run on each blog in your network. You are
	responsible for ensuring that callback is available. (Hint: try <a
	href="http://codex.wordpress.org/Must_Use_Plugins">mu-plugins</a> or a
	<a href="http://codex.wordpress.org/Create_A_Network#WordPress_Plugins">Network
	Activated plugin</a>.)</p>
	<form method="get" action="plugins.php">
		<input type="hidden" name="page" value="network_batch" />
		<?php wp_nonce_field( 'networkbatch' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="callback"><?php _e( 'Callback' ); ?></label></th>
				<td>
					<input name="callback" type="text" id="callback" class="regular-text" value="__return_true" />
				</td>
			</tr>
		</table>

		<?php submit_button( 'Batch Process' ); ?>
	</form>
</div>
