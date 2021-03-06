<?php 
/** @package Aquarius.backend */

require_once "lib/file_mgmt.lib.php";

abstract class action_fileops extends AdminAction {

    /** allows superadmins */
    function permit_user($user) {
        return $user->isSuperadmin();
    }
}

/** File operations list */
class action_fileops_center extends action_fileops implements DisplayAction {
    function get_title() {
        return new FixedTranslation('File operations');
    }

    function process($aquarius, $request, $smarty, $result) {
        $smarty->assign('remove_old_action', Action::make('fileops', 'remove_by_pattern', 'oldnew'));
        $smarty->assign('remove_action', Action::make('fileops', 'remove_by_pattern', 'regexp'));
        $smarty->assign('dircopy_action', Action::make('fileops', 'dircopy'));
        $smarty->assign('mode_override_action', Action::make('fileops', 'mode_override'));
        $smarty->assign('root_dir', $aquarius->root_path);
        $result->use_template("fileops.tpl");
    }
}

/** Action to delete files and folders by pattern_type
  *
  * Arguments to this action:
  *   pattern_type:
  *     regexp: delete files matching the regexp given as request-parameter 'regexp'
  *     oldnew: delete files typically generated by the installer ('*.xxxxx.old', 
  *             '*.xxxxx.new', and 'aquarius_*.tar.bz')
  *   root: directory to search in
  */
class action_fileops_remove_by_pattern extends action_fileops implements DisplayAction {
    var $props = array('class', 'op', 'pattern_type');
    
    function get_title() {
        if ($this->pattern_type == 'regexp') return new FixedTranslation('Delete by pattern');
        if ($this->pattern_type == 'oldnew') return new FixedTranslation('Delete installer files');
    }

    // Scan for dirs/files matching a pattern
    // Children of a dir that matches are not included
    function scanr($dir, $file, $pattern) {
        if ($file == '.' || $file == '..') return array();

        $full_path = realpath($dir.$file);
        if (!$full_path) Log::warn("Could not expand '$dir$file', ignoring");
        $matches = array();

        if (preg_match($pattern, $full_path)) {
            $matches []= $full_path;
        } elseif (is_dir($full_path)) {
            foreach(scandir($full_path) as $contained) {
                $matches = array_merge($matches, $this->scanr($full_path.DIRECTORY_SEPARATOR, $contained, $pattern));
            }
        }
        return $matches;
    }

    function process($aquarius, $request, $smarty, $result) {
        $flist = false;
        switch($this->pattern_type) {
            case "regexp":
                $pattern = get($request, 'pattern');
                if (empty($pattern)) {
                    $result->add_message(AdminMessage::with_html('warn', 'Empty pattern'));
                    return;
                }
                $escaped_pattern = '%'.str_replace('%', '\%', $pattern).'%';
                $flist = $this->scanr($aquarius->root_path, '', $escaped_pattern);
                break;
            case "oldnew":
                // Installer files and directories have an extension consisting
                // of a five-digit hexadecimal number with the string '.old' or
                // '.new' appended.
                $flist = $this->scanr(
                            $aquarius->root_path, '',
                            '%(\\.[0-9a-f]{5}\\.(old|new))|(/aquarius_[^/]*_.....\\.(php|tar\\.bz))$%'
                );
                break;
            default: throw new Exception("Pattern type '$this->pattern_type' unknown");
        }

        if (empty($flist)) {
            $result->add_message(AdminMessage::with_html('info', 'Nothing matched'));
            return;
        }
        
        $file_list = array();
        foreach($flist as $path) $file_list[$path] = $path;

        $smarty->assign('list', $file_list);
        $smarty->assign('actions', array(
            Action::make('fileops', 'remove'),
            Action::make('cancel')
        ));
        $result->use_template("confirm_list.tpl");
    }
}

/** Remove files given in request parameter 'list'
  *
  */
class action_fileops_remove extends action_fileops implements ChangeAction {
    var $props = array('class', 'op');
    
    function get_title() {
        return new FixedTranslation('Remove files and folders');
    }

    function process($aquarius, $request, $result) {
        $done_cnt = 0;
        $done_msg = AdminMessage::with_html('msg', 'Removed these files and folders:');
        $fail_cnt = 0;
        $fail_msg = AdminMessage::with_html('warn', 'Failed deleting:');

        foreach(get($request, 'list') as $rmthis) {
            if (rmall($rmthis)) {
                $done_cnt++;
                $done_msg->add_html($rmthis);
            } else {
                $fail_cnt++;
                $fail_msg->add_html($rmthis);
            }
        }
        if ($fail_cnt > 0) {
            $result->add_message($fail_msg);
        }
        if ($done_cnt > 0) {
            $result->add_message($done_msg);
        }
    }
}

class action_fileops_dircopy extends action_fileops implements ChangeAction {
    function get_title() {
        return new Translation('dircopy');
    }

    function process($aquarius, $post, $result) {
        $src = $aquarius->root_path.get($post,'src');
        $dst = $aquarius->root_path.get($post,'dst');
        if (file_exists($src)) {
            if (is_dir($dst)) {
                $dst = $dst.'/'.basename($src);
            }
            $nrcopied = dircopy($src, $dst);
            $result->add_message(new Translation("dircopy_n_files", array($nrcopied)));
        } else {
            $result->add_message(new Translation("dircopy_missing_source", array($src)));
        }
    }
}


class action_fileops_mode_override extends action_fileops implements ChangeAction {
    function get_title() {
        return new FixedTranslation('Override access mode');
    }

    function process($aquarius, $post, $result) {
        $path = $aquarius->root_path.get($post,'path');
        if (!file_exists($path)) {
            $result->add_message(AdminMessage::with_html('warn', 'Path not found'));
            return;
        } 
        $mode = octdec(get($post, "mode"));
        $modestr = decoct($mode);
        if ($mode == 0) {
            $result->add_message(AdminMessage::with_html('warn', 'Cowardly refusing to set mode to zero.'));
            return;
        }
        
        $remaining_paths = array($path);
        $failed = array();
        $dirfailed = array();
        $succeeded = 0;
        
        // Now what if the chmod takes away our rights to change the subdirs?
        // Oooh, we could first give everything 700 so we can change it, then
        // traverse it postorder. Yeah right. Fuck that. We do it preorder
        // and fail early. That way we don't risk leaving files in
        // a mode that wasn't ever requested.
        
        while($path = array_shift($remaining_paths)) {
            $success = chmod($path, $mode);
            if ($success) {
                $succeeded += 1; 
            } else {
                $failed []= $path;
            }
            
            if (is_dir($path)) {
                $dh = opendir($path);
                if (!$dh) $dirfailed []= $path;
                else {
                    while (($file = readdir($dh)) !== false) {
                        if($file != "." && $file != "..") {
                            $remaining_paths []= $path."/".$file;
                        }
                    }
                }
                closedir($dh);
            }
        }

        if ($failed) {
            $result->add_message(AdminMessage::with_html('warn', "Failed setting mode $modestr on ".count($failed)." locations: <br/>".join('<br/>', $failed)));
        }
        if ($dirfailed) {
            $result->add_message(AdminMessage::with_html('warn', "Failed descending into ".count($dirfailed)." directories: <br/>".join('<br/>', $dirfailed)));
        }
        if ($succeeded) {
            $result->add_message(AdminMessage::with_html('msg', "Changed mode for $succeeded locations."));
        }
        
    }
}