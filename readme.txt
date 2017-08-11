=== Guard ===
Contributors: lowest
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2VYPRGME8QELC
Tags: bruteforce, security, anti-hack, protect, hacking, anti-bruteforce, lightweight, guard, wp-admin, wp-login, login, admin, hide lost password
Requires at least: 3.0
Stable tag: 1.6
Tested up to: 4.7

Guard protects your wp-admin against bruteforce attacks.

== Description ==

Guard protects your wp-admin against bruteforce attacks.

Features:

* Max retries: You have the power to choose how many attempts a user can make before the user is blocked;
* Lockdown: This prevents the user from being able to use the login form after too many retries;
* Email notifications: We'll report a user lockdown to you via email;
* Hide lost password: Hides the "Forgot your password?" link from the login form page.

This plugin does not make any changes to core: When you delete/uninstall the plugin, everything will revert back to normal.

== Installation ==

1. Upload the 'guard' folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Guard

== Changelog ==

* Fixed an security vulnerability regarding `target="_blank"` ([read more](https://core.trac.wordpress.org/ticket/36809))
* Added support for WordPress 4.7

= 1.2.1 =
* Added donation link
* Support for WordPress 4.6

= 1.2 =
* Small changes
* Added header and icon

= 1.1 =
* Bug fixes

= 1.0 =
* Initial release