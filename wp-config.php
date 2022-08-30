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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

$connectstr_dbhost = getenv('DATABASE_HOST');
$connectstr_dbusername = getenv('DATABASE_USERNAME');
$connectstr_dbpassword = getenv('DATABASE_PASSWORD');
$connectst_dbname = getenv('DATABASE_NAME');

/** MySQL database name */
define('DB_NAME', $connectst_dbname);

/** MySQL database username */
define('DB_USER', $connectstr_dbusername);

/** MySQL database password */
define('DB_PASSWORD', $connectstr_dbpassword);

/** MySQL hostname */
define('DB_HOST', $connectstr_dbhost);

/** MySQL force SSL */
define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);

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
define('AUTH_KEY',         'heSOr~*XR=Mb0x.T9Kk1tw#:xq`V,z`&.y#t=bU!sGlz#V)+=CuFFf+;X}xR_d-;');
define('SECURE_AUTH_KEY',  '-rkd_4iHe0m0nt&Bn;Uj]ic2<,R:fdr&l%Hc0,#uYZvbo.#qe;:=v+32,,F=Q9q&');
define('LOGGED_IN_KEY',    'IeRP$+QYg0#n(TN!D@LdnbgMqVym^;%P):dL!>[b Yi?`{C!*sj(g.:89D~K|#hj');
define('NONCE_KEY',        'ak=SL2g!1H{K~8wap,[_6:GutsI9+|]ZBPZKMn/-FIe<i~R7Y?n<AEK(<i *:Wsd');
define('AUTH_SALT',        'k,rLO/h9:yHDB{%e[IWo}W8%_rjfpwp; (P=Ju3y1zu]Bmi>Rkx7pX3}#WiS;7!*');
define('SECURE_AUTH_SALT', 'S!b08<_t3:[Oxy4SImK8=Dl]^UyDb5,9HwYMD>F*:R+=gGt#Stvj)WGV#.n%YLk(');
define('LOGGED_IN_SALT',   '1*qr;g^2y_nrFJtd$MC,{~y4z-t`glr-CmHKd !:KG[K:n:=F&Xd{VAwH9Qm*@M+');
define('NONCE_SALT',       'mSz7+misT{S7+xk>|@W:Qev;%vVya(oAri}Snpxvn@j_UhOEB;RPRVFSTWMpJlN]');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
