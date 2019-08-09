<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
if (! function_exists('active_menu')) {
    function active_menu($uri)
    {
        $CI = get_instance();
        $class_method = $CI->router->fetch_class().'/'.$CI->router->fetch_method();
        $action = $CI->input->get('action');
        $type = $CI->input->get('type');
        if ($uri == $class_method.'/'.$action.'/'.$type) {
            return 'active';
        } elseif ($uri == $class_method.'/'.$action) {
            return 'active';
        } elseif ($uri == $class_method) {
            return 'active';
        } else {
            return '';
        }
    }
}
