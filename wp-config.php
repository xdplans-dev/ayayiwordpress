<?php

//Begin Really Simple Security session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple Security cookie settings
//Begin Really Simple Security key
define('RSSSL_KEY', 'DMo1Py6ivvrsXKHXJwWpfdilOuf2eeWIi9onceln8kMWlvc9WNVIrL5VVR6DuDNF');
//END Really Simple Security key
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'arturo66_wp240' );

/** Database username */
define( 'DB_USER', 'arturo66_wp240' );

/** Database password */
define( 'DB_PASSWORD', '88q39!S(pL' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'teucg0tkn29ko3jsnx2urkdfdwktspavlcue87joki0ohzdcxi2jhppw6ezx8gwk' );
define( 'SECURE_AUTH_KEY',  '29v81rmqpvjbajbi7e49mysprkegu8rmlr4lr0ofs4qwes5sbw6avmpgzd4k4i5k' );
define( 'LOGGED_IN_KEY',    '2iv7krroanv2n2jmst7o5j3r2vmkrusmpqwb98ondnqoyic6q4ndmwdmpji5dd2g' );
define( 'NONCE_KEY',        'jlhfr0jbirv5ckzulpjlbxvsx0bh2vc5z4drxsxaf4qe3q0n7rd8f1jdevmda3mw' );
define( 'AUTH_SALT',        '6r1e4tpixzgtkvfecazzxxxjr8x04rlyilfat8nplfclfnmfapmvhhryhdlvywnn' );
define( 'SECURE_AUTH_SALT', 'mmuo4ientodajlsasewcarxacxr0milwsshsmv0xa2lvszvt5mm8ruhmiomyhntf' );
define( 'LOGGED_IN_SALT',   'ilybkut6kjd1p3cddmx2b12dxo2rfbxmjc52kp3rehy4ykwhosddijv6uos8fiam' );
define( 'NONCE_SALT',       'hppp7kzlbfjflpsqiottenuvzazfhfcg4p1loy0nq3dcpkjkagzqa5imgilkcz4g' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wpti_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
