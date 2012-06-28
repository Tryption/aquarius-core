<?php

/**************** LOGGING */

// Log messages are written to a log-file and included as HTML comments
// Available log levels: NEVER | FAIL |  WARN | INFO | MESSAGES | DEBUG | BACKTRACE | ALL
$config['log'] = array(
    'level'     => 'INFO',  // What messages to write to file, use level INFO on working sites
    'echolevel' => 'NEVER', // include log messages from this level as HTML comments in output, use level NEVER in production
    'firelevel' => 'NEVER', // send log messages as HTTP headers, requires Firefox/FirePHP extension, use NEVER in production
    'file'      => 'cache/log.txt' // This file must be writable by the webserver and relative to the root path.
);



/**************** SESSION */

/** Where to save session files
  *
  * Specify a filesystem path where session files are stored. If this path is
  * relative, it will be taken relative to the aquarius cache path. The
  * preset value is 'session'. If this is set to false, the value preset
  * by the webserver is not changed, the standard location is '/tmp'.
  *
  * On servers with shared hosting, it is insecure to store session data in a
  * common directory with other sites. Unfortunately, webservers are frequently
  * configured that way. For this reason Aquarius does not by default use the
  * session.save_path preset by the webserver.
  *
  * See also: PHP's session.save_path directive
  *
  */
$config['session']['save_path'] = 'session';

/** How long session data is preserved before it's cleared (minimum) in seconds
  *
  * The preset is 30 minutes. If this value is set to false, the value
  * configured by the webserver is not changed.
  *
  * See also: PHP's session.gc_maxlifetime directive
  *
  */
$config['session']['lifetime'] = '1800';

/** Name of the session cookie, preset is 'aquarius3_session' */
$config['session']['name'] = 'aquarius3_session';


/**************** FRONTEND */

/* Set the standard domain name */
// $config['frontend']['domain'] = 'www.aquaverde.ch';


/* Always use session in frontend
 * If this is false (the default, sessions will only be enabled for restricted nodes) */
$config['frontend']['use_session'] = false;

/* Use different language or base node, based on domain name.
   Default language, node and redirects may be specified based on domain-name. These parameters can be set:
    node: A node id or name to use instead of the root node
    lg:   A language to use instead of using browser detection or the default
    moved_permanently: an URL to redirect to.

  The 'node' and 'lg' parameters are considered only if they are not specified in the URL. 'moved_permanently' on the other hand is always active.

   Example: Assume we have the two domains 'coolthing.example.com' and 'trucfroid.example.com', the two languages 'en' and 'fr', also the default language is 'en'. Now we configure the following:

    $config['frontend']['domains'] = array(
            'search.coolthing.example.com' => array('node' => 'search'),
                   'trucfroid.example.com' => array('lg'   => 'fr'),
         'recherche.trucfroid.example.com' => array('node' => 'search'),
                     'oldcool.example.com' => array('moved_permanently' => 'http://coolthing.example.com')
        );

   What this means is that for 'search.coolthing.example.com' and all subdomains we use the node named 'search' as base node. For all domains ending in 'trucfroid.example.com' we select 'fr' as language, and finally, for 'recherche.trucfroid.example.com' we also use the node named 'search'.

   Note that it was not necessary to specify 'lg'=>'fr' again for 'recherche.trucfroid.example.com', because that was already covered by 'trucfroid.example.com'.

   All requests on domain oldcool.example.com will be answered with a HTTP 301 redirection to location 'http://coolthing.example.com'. */
$config['frontend']['domains'] = array();

/* Frontend redirects to proper URI if this is enabled. May lead to redirect loops, surprising behaviour and overall confusion. Required to please the holy GOOG. */
$config['frontend']['uri_correction'] = true;

/* Automagically assign content fields for each item in the smarty {list} block. */
$config['frontend']['loadcontent'] = false;



/**************** ADMIN */

/** Optional: Domain to use for backend, clients using another domain will be redirected to this */
//$config['admin']['domain'] = 'admin.site.example';

/** Path to backend. Optional, standard value is '/admin/'  */
//$config['admin']['path'] = '/aquarius/';

$config['admin']['user']['edit_permission_tree_depth'] = 2; // How many levels of tree to allow adding edit permission for users (default 2)

// light config
$config['admin']['menu_links'] = array(
    array( 'parent' => 'menu_super_links', 'title' => 'Statistics', 'url' => '/stats/'),
    array( 'parent' => 'menu_super_links', 'title' => 'Aquarius manual', 'url' => 'http://wiki.aquarius3.ch/', 'target' => '_new'),
    array( 'parent' => 'menu_super_links', 'title' => 'Database admin', 'url' => '/aquarius/dbadmin', 'target' => '_new', 'allow' => 0)
);

$config['admin']['rte']['browse_path_img'] = 'pictures/richtext';
$config['admin']['rte']['browse_path_file'] = 'download';



/** Generate thumbnail-sized and alt-sized files on upload.
  * Also provide an action to regenerate those files. Some legacy templates and
  * plugins may require this, though it is recommended to fix them instead. */
$config['legacy']['generate_thumbs'] = false;


/** Allow administrators to manage languages
  *
  * Preset is false.
  */
$config['admin']['allow_languageadmin'] = false;

/** Standard email address to use as sender address
  * This is used in the "Sender:" header when the system generates mails. The
  * "From:" header will also be set to this address should it not be set
  * explicitly.
  * 
  * The "@host" part may be omitted, and only the local part (before the "@")
  * specified. In this case the request-hostname will be used, with the
  * "www." stripped off.
  */
$config['email']['sender_address'] = 'info';