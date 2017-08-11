<?php
/*
 * Plugin Name: Guard
 * Plugin URI: https://wordpress.org/plugins/guard/
 * Description: Guard protects your wp-admin against bruteforce attacks.
 * Version: 1.2.2
 * Author: Mitch
 * Author URI: https://profiles.wordpress.org/lowest
 * Text Domain: guard
 * Domain Path:
 * Network:
 * License: GPL-2.0+
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function guard_create_menu() {
    add_submenu_page('options-general.php', 'Guard', 'Guard', 'administrator', 'guard', 'guard_settings_page');

    add_action('admin_init', 'guard_register_settings');
    add_action('admin_init', 'guard_is_save_triggered');
}
add_action('admin_menu', 'guard_create_menu');

function guard_scripts() {
	wp_enqueue_script('jquery');
}
add_action( 'wp_enqueue_scripts', 'guard_scripts' );

function guard_register_settings() {
	// general
	register_setting('guard-settings-group-general', 'guard_status');
	register_setting('guard-settings-group-general', 'guard_lpl');
	register_setting('guard-settings-group-general', 'guard_notices');
	
	// advanced
	register_setting('guard-settings-group-advanced', 'guard_max_attempts');
	register_setting('guard-settings-group-advanced', 'guard_lock_duration');
	register_setting('guard-settings-group-advanced', 'guard_notify_email');
	register_setting('guard-settings-group-advanced', 'guard_pin');
	register_setting('guard-settings-group-advanced', 'guard_url');
	
	// texts
	register_setting('guard-settings-group-texts', 'guard_custom_text');
}

function guard_is_save_triggered() {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true' && isset($_GET['page']) && $_GET['page'] === "guard/" . basename(__FILE__)) {
        do_action("guard/settings-updated");
    }
}
function guard_is_active() {
	$v = get_option('guard_status');
	if (!$v) {
		return 0;
	} else {
		return $v;
	}
}
function guard_get_lpl() {
	$v = get_option('guard_lpl');
	if (!$v) {
		return 0;
	} else {
		return $v;
	}
}
function guard_get_max_attempts() {
    $v = get_option('guard_max_attempts');
    if (!$v) {
        return 5;
    } else {
        return $v;
    }
}
function guard_get_lock_duration() {
    $v = get_option('guard_lock_duration');
    if (!$v) {
        return 5;
    } else {
        return 5;
    }
}
function guard_get_notify_email() {
    $v = get_option('guard_notify_email');
    if (!$v) {
        return get_option('admin_email');
    } else {
        return $v;
    }
}
function guard_get_custom_text() {
    $v = get_option('guard_custom_text');
    if (!$v) {
        return 'You have been temporary blocked because of multiple failed login attempts.';
    } else {
        return $v;
    }
}

function guard_settings_page() {
    ?>
	<div class="wrap guard">
		<h2>Guard</h2>
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo admin_url( 'options-general.php?page=guard' ); ?>" class="nav-tab<?php if(empty($_GET['tab'])) { echo ' nav-tab-active'; } ?>"><?php echo __('General', 'guard') ?></a>
			<a href="<?php echo admin_url( 'options-general.php?page=guard&tab=advanced' ); ?>" class="nav-tab<?php if(isset($_GET['tab']) && $_GET['tab'] == 'advanced') { echo ' nav-tab-active'; } ?>"><?php echo __('Advanced', 'guard') ?></a>
			<a href="<?php echo admin_url( 'options-general.php?page=guard&tab=texts' ); ?>" class="nav-tab<?php if(isset($_GET['tab']) && $_GET['tab'] == 'texts') { echo ' nav-tab-active'; } ?>"><?php echo __('Texts', 'guard') ?></a>
		</h2>
		<?php
		if(isset($_GET['tab']) && $_GET['tab'] == 'advanced') {
		?>
		<form method="post" action="options.php">
			<?php
			settings_fields('guard-settings-group-advanced');
			do_settings_sections('guard-settings-group-advanced');
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php echo __('Max retries', 'guard') ?></th>
					<td><input type="text" name="guard_max_attempts" value="<?php echo esc_attr(guard_get_max_attempts()); ?>" /><a href="javascript:void(0);" title="How many attempts can a user make before being blocked?" class="info-icon">&#x1F6C8;</a></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Lockout time (seconds)', 'guard') ?></th>
					<td><input type="text" name="guard_lock_duration" value="<?php echo esc_attr(guard_get_lock_duration()); ?>" /><a href="javascript:void(0);" title="How long does a user have to wait before being unblocked again (in seconds)?" class="info-icon">&#x1F6C8;</a></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Notification email (optional)', 'guard') ?></th>
					<td><input type="text" name="guard_notify_email" value="<?php echo esc_attr(guard_get_notify_email()); ?>" /><a href="javascript:void(0);" title="When someone tries to bruteforce your login form, we will block them and notify you via email. Leave blank to disable." class="info-icon">&#x1F6C8;</a></td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
			
		</form>
		<?php
		} elseif(isset($_GET['tab']) && $_GET['tab'] == 'texts') {
		?>
		<form method="post" action="options.php">
			<?php
			settings_fields('guard-settings-group-texts');
			do_settings_sections('guard-settings-group-texts');
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php echo __('Custom blocked text', 'guard') ?></th>
					<td><textarea name="guard_custom_text" style="height:90px"><?php echo esc_attr(guard_get_custom_text()); ?></textarea></td>
				</tr>
			</table>

			<?php submit_button(); ?>

			</form>
		<?php
		} else {
		?>
		<form method="post" action="options.php">
			<?php
			settings_fields('guard-settings-group-general');
			do_settings_sections('guard-settings-group-general');
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Guard <?php echo __('Status', 'guard') ?></th>
					<td><input type="checkbox" name="guard_status" id="guard_status" value="1" class="status" <?php checked( true, get_option('guard_status')); ?>/><label for="guard_status" id="guard_label"></label></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php echo __('Hide lost password link', 'guard') ?></th>
					<td><input type="checkbox" name="guard_lpl" id="guard_lpl" value="1" class="status_lpl" <?php checked( true, get_option('guard_lpl')); ?>/><label for="guard_lpl" id="guard_label_lpl"></label></td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
			
		</form>
		<?php
		}
		?>
	</div>
<?php
}

function guard_get_ip_address() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

global $guard_error;
$guard_error = false;

if(guard_is_active() == '1') {
	
	add_filter('authenticate', 'guard_auth_login', 99999);
	function guard_auth_login($user) {
			$timeout = intval(guard_get_lock_duration());
			$timeout = $timeout  ? $timeout : 60 * 60 * 1 ;

			$max_attempts = intval(guard_get_max_attempts());
			$max_attempts = $max_attempts ? $max_attempts : 5;

			$cnt = get_transient($k = "wp_grd_" . guard_get_ip_address());
			if ($cnt === false) {
				$cnt = 0;
			} else {
				$cnt = intval($cnt);
			}

			$cnt++;

			set_transient($k, "" . $cnt . "", $timeout);

			if ($cnt >= $max_attempts) {
				global $guard_error;
				$guard_error = true;
				
				$email = guard_get_notify_email();
				if ($cnt === $max_attempts && !empty($email)) {
					$r = wp_mail(
						$guard_error['notify_email'],
						__('WordPress Guard: Attack blocked', 'guard'),
						__('Guard has detected a new attack from the following IP: ' . guard_get_ip_address() . '

		This IP has been timed out for ' . guard_get_lock_duration() . ' seconds.

		Cheers,
		Your Guard plugin', 'guard')
					);
				}
				
				$custom_text = guard_get_custom_text();

				$error = new WP_Error();

				$error->add('banned_bruteforce', __($custom_text, 'guard'));

				return $error;
			}

			return $user;
	}
	add_action('admin_init', 'guard_restrict_admin', 10);

	function guard_restrict_admin() {
		$max_attempts = intval(guard_get_max_attempts());
		$max_attempts = $max_attempts ? $max_attempts : 5;

		$cnt = get_transient($k = "wp_grd_" . guard_get_ip_address());
		if ($cnt === false) {
			return;
		} else {
			$cnt = intval($cnt);
		}

		if ($cnt >= $max_attempts) {
			global $guard_error;
			$custom_text = guard_get_custom_text();
			$guard_error = true;
			wp_logout();
			wp_die(__($custom_text, 'guard'));
		}
	}

	if(guard_get_lpl() == '1') {
		class guard_lost_password_link {

		  function __construct() {
			add_filter( 'show_password_fields', array( $this, 'disable' ) );
			add_filter( 'gettext',              array( $this, 'remove' ) );
		  }

		  function disable() {
			if ( is_admin() ) {
			  $userdata = wp_get_current_user();
			  $user = new WP_User($userdata->ID);
			  if ( !empty( $user->roles ) && is_array( $user->roles ) && $user->roles[0] == 'administrator' )
				return true;
			}
			return false;
		  }

		  function remove($text) {
			return str_replace( array('Lost your password?', 'Lost your password'), '', trim($text, '?') );
		  }
		}
		
		$pass_reset_removed = new guard_lost_password_link();
		
		function guard_disable_password_reset() {
			return false;
		}
		add_filter ( 'allow_password_reset', 'guard_disable_password_reset' );
	}

}

function guard_footer_jquery() {
	if(isset($_GET['page']) && $_GET['page'] == 'guard') {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('.status').change(function () {
				$('#guard_label').text(this.checked ? '<?php echo __('Activated', 'guard') ?>' : '<?php echo __('Deactivated', 'guard') ?>');
			}).change();
			$('.status_lpl').change(function () {
				$('#guard_label_lpl').text(this.checked ? '<?php echo __('Enabled', 'guard') ?>' : '<?php echo __('Disabled', 'guard') ?>');
			}).change();
		});
		</script>
		<style type="text/css">
		.guard .info-icon {font-size:19px;text-decoration:none;margin-left:5px;}
		.guard h2:after {content:'WordPress Bruteforce Defender';font-size:13px;margin-left:10px;}
		</style>
		<?php
	}
}
add_action( 'admin_head', 'guard_footer_jquery' );

function guard_uninstall_hook(){
    register_uninstall_hook( __FILE__, 'guard_uninstall' );
}
register_activation_hook( __FILE__, 'guard_uninstall_hook' );

function guard_uninstall(){

	unregister_setting('guard-settings-group-general', 'guard_status');
	delete_option('guard_status');
	
	unregister_setting('guard-settings-group-general', 'guard_lpl');
	delete_option('guard_lpl');

	unregister_setting('guard-settings-group-advanced', 'guard_max_attempts');
	delete_option('guard_max_attempts');
	
	unregister_setting('guard-settings-group-advanced', 'guard_lock_duration');
	delete_option('guard_lock_duration');
	
	unregister_setting('guard-settings-group-advanced', 'guard_notify_email');
	delete_option('guard_notify_email');
	
	unregister_setting('guard-settings-group-advanced', 'guard_pin');
	delete_option('guard_pin');

	unregister_setting('guard-settings-group-texts', 'guard_custom_text');
	delete_option('guard_custom_text');
	
	unregister_setting('guard-settings-group-texts', 'guard_custom_username_text');
	delete_option('guard_custom_username_text');
	
	unregister_setting('guard-settings-group-texts', 'guard_custom_password_text');
	delete_option('guard_custom_password_text');
	
	unregister_setting('guard-settings-group-texts', 'guard_custom_authkey_text');
	delete_option('guard_custom_authkey_text');

}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), function($link) {
	return array_merge( $link, array('<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2VYPRGME8QELC" target="_blank" rel="noopener noreferrer">Donate</a>') );
} );