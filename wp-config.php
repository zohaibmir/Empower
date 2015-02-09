<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'empower');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'KI}OC?o5{u>@Nly4TW$;UEXN/=I]Q76{z2`$xK{+(f==?rn|Q>L4h[[W>~}4@ ($');
define('SECURE_AUTH_KEY',  '*t.-Rp-JS F~W|2e,w#icIA[H!}$$okoMVwSz)^$in`uz{]Z>[WxE!`EF{yvk_)5');
define('LOGGED_IN_KEY',    '-m2Cx6 Gn+UafMpx:t?u?61{MOd+o=!*V6%WLrrj.1Tqr([w~-*[-<yyL7,[ze5n');
define('NONCE_KEY',        'yZhpivR|nee/Xe}YRsNwI]R!(CSvL9XpkQiV9s_:.jso>i-Uo/1szv2}CG@S+oj4');
define('AUTH_SALT',        'HjW#>`ydc~U3ZM-YN~IZgzXZ.P:RU }%/E:39|z<oQlxEltk$$:&qlM)hVaRrn8?');
define('SECURE_AUTH_SALT', 'D&C;^|6SI=qeOGjNKSJ!~7_q56L$smKgzI<e*in8Wwb]Et,X_.H@RHONhYDHe</e');
define('LOGGED_IN_SALT',   'e<yRlqi3v))A{+~ysGCAL_AUH*V#O{_%wB4{W---DPD<6Ag#Sbg}5%Dx*O*cD&%%');
define('NONCE_SALT',       'xEL7L{8g{?)+yM`k;3v):[0dTO(44F(Ot2Nly7&sd5hmxTDO{Ba&;u4%NI2fCVT<');


define('WP_HOME','http://localhost/Empower');
define('WP_SITEURL','http://localhost/Empower');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
