<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'club-critique');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'ZT@{B_ _/}~!kc]o]A7Nj)V&]a:/6Zyg!{:NN`n_k}%8teOW;X~TVGgs-9remy`q');
define('SECURE_AUTH_KEY',  '!-H>-j{I6<w?tl%i/YEWOhnms|-je>^eDb;k7J1kWM6%T|b$euF6Df9-]#Jp_{3&');
define('LOGGED_IN_KEY',    'oTzZmtX=f}:8$)jV?qG&Q[~9#E,RiN{Flfv5Y]PJ~nWL[zd`s5^?Og RxU(a;EcI');
define('NONCE_KEY',        '_JL^l5`p<rSAy5^k>/U0QMegrcRS}REuW4o]qkRXIEajI5OANnO8$LO@xux$[K4o');
define('AUTH_SALT',        '3.6oOVIo.Jm-C59(2OQ@9e12NZxF},mQgL_+E| 4>f8#;k4^`lKTU%Z7yVxo`tNZ');
define('SECURE_AUTH_SALT', '5lT}Ve^qnz9E(=Gq8.UDiPIBIo1GpJt.Mnjt=9%-w-)]h29w{)ct,Pi*k?>?f&@Z');
define('LOGGED_IN_SALT',   'Gys3lIx#}DhdVV;ZDk =?@CP3##k?aUuiVcMctnyd|-.bC}e&8fpLUQIoS7jTR#[');
define('NONCE_SALT',       'E)ARL/tsZ8}T*e6daA9@/l7oE*9<6psa;:E! <du?jo2lihL#<ytLW^s}m593B02');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
