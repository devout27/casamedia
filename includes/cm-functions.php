<?php 

/*  */

if (!defined('ABSPATH')) {
    exit;
}

function cm_log($message)
{
    if (defined('CM_ERROR_LOG')) {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $file = isset($bt['file']) ? $bt['file'] : 'unknown file';
        $line = isset($bt['line']) ? $bt['line'] : 'unknown line';

        $log = sprintf(
            "[CM] %s %s:%s - %s\n",
            date('Y-m-d H:i:s'),
            $file,
            $line,
            $message
        );

        error_log($log, 3, CM_ERROR_LOG);
    }
}

function get_user_primary_role_by_id($user_id)
{
    $user = get_userdata($user_id);

    if (! $user) {
        return [
            'slug' => '',
            'name' => '',
        ];
    }

    $role_slug = ! empty($user->roles) ? $user->roles[0] : null;

    if ($role_slug && isset($GLOBALS['wp_roles']->roles[$role_slug])) {
        $role_name = $GLOBALS['wp_roles']->roles[$role_slug]['name'];
    } else {
        $role_name = null;
    }

    return [
        'slug' => $role_slug,
        'name' => $role_name,
    ];
}

function get_name_by_user_($user)
{
    $name = $user->first_name ? $user->first_name . ' ' . $user->last_name : $user->user_login;
    return $name;
}

function get_name_by_id_($user_id)
{
    $user = get_user_by('id', $user_id);
    return get_name_by_user_($user);
}

function get_email_by_id_($user_id)
{
    $user = get_user_by('id', $user_id);
    return $user->user_email;
}
