<?php

defined('DYNFORM_SHOW_SETTINGS') or define('DYNFORM_SHOW_SETTINGS', false);

/* Divert dynform mails to this address should it be given as sender address in
 * the dynform. (The dynform field must be of the 'Email' type for this to work.) */
$config['dynform']['test_email'] = false;