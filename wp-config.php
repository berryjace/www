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
 * @package WordPress_3.3.1
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

define('DB_NAME', 'admin_wp');

/** MySQL database username */
define('DB_USER', 'amc_wp');

/** MySQL database password */
define('DB_PASSWORD', '2$#7DV!G');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
// for other database connection AMC database
define('DB_NAME_AMC', 'amc_wp');
//define('DB_USER_AMC', 'nyvs1');
//define('DB_PASSWORD_AMC', 'blunix401');
//define('DB_HOST_AMC', '192.168.3.44');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'hZ>kWMyW+pP>,h2pH<dPHXf&05p;%[m7:SZ9L5Q[suMa? {dO #*o-<*Qe9qw=qr');
define('SECURE_AUTH_KEY',  '1 vIh3=f-MY?l4d$Im8^}|woM?$W#||/ZmAxe4vsuTU0P,7_/IYrSw{zXyF2AD:}');
define('LOGGED_IN_KEY',    '&<ylwRcwQ7)ZmXRz3QIulU:Z1SW6a`Y/c+.+Fd5cU!48^|+UE Gn{{l:9R t0/uq');
define('NONCE_KEY',        'ks9 [eg486+gn1K*ci +*|,$?T6w9h%+/DqVxY]G*@vxie^(/D$&4{+p=4yBuz}6');
define('AUTH_SALT',        '{k@vzui&9e|q4luAf/75NG8L,@_mrVH|b|@zLDqf~_l{uA/%y2aQ{FE:i</A$1*j');
define('SECURE_AUTH_SALT', 'R-{Q+eXJzE(2j;teKmSqqaeUq!$DFGG/X~+X|-!8#fX[pk7!$;Xc*k?yxmvB:U~a');
define('LOGGED_IN_SALT',   'cZH|MRx.W-F_`QL|K>::l<m%96yc6aQ|@{H0gD&-yCb!(P8vU_=aQBjCgXN7u8/*');
define('NONCE_SALT',       ']+DrWnpe{a:ong57-|:+x<--m/@:9k[U<Zq)=?6+?2R:|?~!a3Xo14mUp4Bb5^ox');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');
define('FS_METHOD', 'direct');
define('WP_HOME', 'http://centos.softura.com');
define('WP_SITEURL', 'http://centos.softura.com');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', FALSE);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
