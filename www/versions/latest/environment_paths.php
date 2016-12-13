<?php
/**
 * Collection of all important paths.
 *
 * PHP version 5
 *
 *
 */


/**
 * /www, root of everything that needs to be on the server.
 */
define("WEB_ROOT", realpath(__DIR__.'/../..'));

/**
 * Location of configuration files that should not change with the version.
 */
define("STAT_CONFIG_ROOT", WEB_ROOT.'/config');

/**
 * Location of data (from users for example) that is independent of the version.
 */
define("STAT_DATA_ROOT", WEB_ROOT.'/data');

/**
 * Version independent logs data.
 */
define("STAT_LOGS_ROOT", WEB_ROOT.'/logs');

/**
 * Temporary directory for things that the next instance might need.
 */
define("STAT_TMP_ROOT", WEB_ROOT.'/tmp');

/**
 * General resources (csv, txt) that are needed by the application.
 * Hidden from user access.
 */
define("VER_RESOURCE_ROOT", __DIR__.'/res');

/**
 * Configured sites fragments.
 */
define("VER_INC_ROOT", __DIR__.'/inc');
/**
 * Root for packet manager, all dependencies end in there.
 */
define("VER_COMPOSER_ROOT", __DIR__.'/vendor');

/**
 * Directory for general purpose templates.
 */
define("VER_TEMPLATES_ROOT", __DIR__.'/templates');

/**
 * Version controlled config.
 */
define("VER_CONFIG_ROOT", __DIR__.'/config');

/**
 * Pre-configured objects ready for use.
 */
define("VER_SINGLETONS_ROOT", __DIR__.'/singletons');

/**
 * General resources that get created at run time.
 */
define("VER_RES_CACHE", __DIR__.'/public/rc');

/**
 * Cache for template engine.
 */
define("VER_TWIG_CACHE", __DIR__.'/twig_cache');

/**
 * General purpose cache.
 */
define("VER_CACHE", __DIR__.'/cache');
