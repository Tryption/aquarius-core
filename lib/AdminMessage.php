<?php 
/** Message generated by action to be displayed to user
  **/
class AdminMessage {
    function __construct($type) {
        $this->type = $type;
        $this->parts = array();
    }

    static function with_line($type, $line) {
        $args = func_get_args();
        $message = new self(array_shift($args));
        call_user_func_array(array($message, 'add_line'), $args);
        return $message;
    }

    static function with_html($type, $html) {
        $args = func_get_args();
        $message = new self(array_shift($args));
        call_user_func_array(array($message, 'add_html'), $args);
        return $message;
    }

    function add_line($key) {
        $args = func_get_args();
        $key = array_shift($args);
        $this->parts []= array(
            'type'  => 'line',
            'text' => is_string($key) ? new Translation($key, $args) : $key
        );
    }

    function add_html($value) {
        $this->parts []= array(
            'type'  => 'html',
            'text' => $value
        );
    }

    function type() {
        return $this->type;
    }

    function html() {
        $html_parts = array();
        foreach($this->parts as $part) {
            if ($part['type'] == 'html') {
                $html_parts []= str($part['text']);
            } else {
                $html_parts []= htmlspecialchars(str($part['text']));
            }
        }
        return nl2br(join("\n", $html_parts));
    }

    function text() {
        $text_parts = array();
        foreach($this->parts as $part) {
            if ($part['type'] == 'html') {
                $text_parts []= strip_tags($part['text']);
            } else {
                $text_parts []= $part['text'];
            }
        }
        return join("\n", $text_parts);
    }
    
    function __toString() {
        return $this->text();
    }

    function has_parts() {
        return !empty($this->parts);
    }

}
