<?php
/** Template related functions
  * @package Aquarius
  */

require_once "lib/db/Wording.php";

/** Dummy block function used to disable caching for parts of a template. */
function smarty_block_dynamic($param, $content, &$smarty) {
    // QNDFIX See smarty issue 71: Output filter runs before caching, ignoring nochache sections
    // Fixed in Smarty revision 4542, remove this after smarty upgrade.
    $content = smarty_outputfilter_replace_aqualink($content, $smarty);
    
  return $content;
}

/** Load a content and assign its fields to the smarty container.
  * Called from smarty plugins loadcontent and usecontent, returns the replaced vars */
function assign_content_fields(&$smarty, &$params) {
    $load = true;
    $reason = false;

    // Load node and content
    $node = db_Node::get_node(get($params, 'node'));
    $lg = get($params, 'lg', $smarty->get_template_vars('lg'));
    $content = false;

    if ($node) {
        // Check permissions
        $restriction_node = $node->access_restricted_node();
        if ($restriction_node) {
            $access = false;
            $user = db_Fe_users::authenticated();
            if ($user) $access = $user->hasAccessTo($restriction_node->id);

            if (!$access) {
                $load = false;
                $reason = "User does not have access to $restriction_node->id";
            }
        }

        // Load the content
        $content = $node->get_content($lg);
        if (!$content) {
            $load = false;
            $reason = "No content for node $node->id in language $lg";
        }
    } else {
        $load = false;
        $reason = "Could not load node for '$node'";
    }

    if ($load) {
        // Load the fields, put them in the smarty container, return the old ones
        $template_vars = &$smarty->get_template_vars();
        return array_replace_aqua($template_vars, $content->get_fields());
    } else {
        Log::debug("assign_content_fields unsuccessful: $reason");
        return false;
    }
}

/** get translation for a wording identifier.
  * convenience wrapper around db_Wording::getTranslation().
  * @param $key identifier to be translated
  * @param $lg optional language code
  * @return text in desired language for identifier */
function translate($key, $lg = false) {
    if (!$lg) $lg = $GLOBALS['lg'];
    return db_Wording::getTranslation($key, $lg);
}

/**
 * returns a clear link if no http:// is set
 *
 * @param string $link 
 * @return the cleaned link
 */
function clean_link($link) {
    if (0 === strpos($link, '/')) {
        $cleanedLink = $link ;
    }
    elseif (0 === strpos($link, 'http://')) {
        $cleanedLink = $link ;
    }
    elseif (0 === strpos($link, 'https://')) {
        $cleanedLink = $link ;
    }
    else {
        $cleanedLink = "http://".$link;
    }

	return $cleanedLink;
}


/** Change image link to use a resized version of the image
 * 
 * Params:
 *   image:           path to image, required
 *   w or width:      set maximum width (example: 160)
 *   h or height:     set maximum height (example: 90)
 *   q or quality:    change JPEG quality (example: 80)
 *   crop: toggle     image cropping (preset true)
 *   c or crop_ratio: set crop ratio (example: "16:9")
 *   as:              use as preset settings taken from given directory (alt settings unless th flag set to true)
 *   th:              use th settings, not alt settings
 * 
 * When none of w, h, or 'as' is set, the directory-settings for the image
 * path are used. When there are no dir-settings either, the default image sizes
 * are used (alt size).
 * 
 * When both width and height are set, the image is cropped to fit
 * unless either crop=false or crop_ratio is set.
 * 
 * Examples:
 * 
 * Max width of logo: 120px
 * {resize image=/interface/logo.png w=120}
 * 
 * Use settings of directory pictures/content
 * {resize image=/other_pictures/an_example.jpg as=pictures/content}
 * 
 */
function smarty_function_resize($params, $smarty) {
    $image      = get($params, 'image');
    $width      = get($params, 'w', get($params, 'width', false));
    $height     = get($params, 'h', get($params, 'height', false));
    $quality    = get($params, 'q', get($params, 'quality', false));
    $crop       = get($params, 'crop', true);
    $crop_ratio = get($params, 'c', get($params, 'crop_ratio', false));
    $as         = get($params, 'as', false);
    $th         = get($params, 'th', false);
    
    $using_dir_settings = false;
    $dir_settings = DB_DataObject::factory('directory_properties');
    if ($as) {
        $using_dir_settings = $dir_settings->load($as, false);
    }
    if (!$using_dir_settings && $width === false && $height === false) {
        $dir_settings->load(dirname($image));
        $using_dir_settings = true;
    }

    $slir_options = array();
    
    if ($using_dir_settings) {
        $max_size = $th ? $dir_settings->th_size : $dir_settings->alt_size;
        switch($dir_settings->resize_type) {
            case 'm':
                $slir_options ['w']= $max_size;
                $slir_options ['h']= $max_size;
                break;
            case 'w':
                $slir_options ['w']= $max_size;
                break;
            case 'h':
                $slir_options ['h']= $max_size;
                break;
        }
    }
    
    if ($width !== false) {
        $slir_options ['w']= $width;
    }
    
    if ($height !== false) {
        $slir_options ['h']= $height;
    }
    
    if ($crop_ratio !== false) {
        $slir_options ['c']= $crop_ratio;
    } elseif ($crop && $width !== false && $height !== false) {
       $slir_options ['c']= "$width:$height"; 
    }
    
    if ($quality !== false) {
        $slir_options ['q']= $quality;
    }

    $option_strings = "";
    foreach($slir_options as $option => $value) $option_strings []= $option.$value;

    require_once "file_mgmt.lib.php";
    $path = ensure_filebasedir_path(substr($image, 1));
    if (file_exists($path)) {
        $mtime = substr(filemtime($path), -4);
        return '/aquarius/slir/'.join('-', $option_strings).dirname($image).'/'.urlencode(basename($image)).'?cdate='.$mtime;
    }
}

/** Use an alternatively-sized version of the image (depends on directory settings) */
function smarty_modifier_alt($image) {
    return smarty_function_resize(array('image' => $image, 'quality' => 95), false);
}

/** Use a thumbnail-sized version of the image  (depends on directory settings) */
function smarty_modifier_th($image) {
    return smarty_function_resize(array('image' => $image, 'th' => true, 'quality' => 95), false);
}