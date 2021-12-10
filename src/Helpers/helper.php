<?php

/**
 * View render
 *
 * @param string $path
 * @param array $data
 * @return mixed
 */
if (! function_exists('view')) {
    function view($path, $data = []) {
        return \My_MVC\View\View::render($path, $data);
    }
}

/**
 * Request get
 *
 * @param string $key
 * @return mixed
 */
if (! function_exists('request')) {
    function request($key) {
        return \My_MVC\Http\Request::value($key);
    }
}

/**
 * Redirect
 *
 * @param string $path
 * @return mixed
 */
if (! function_exists('redirect')) {
    function redirect($path) {
        return \My_MVC\Url\Url::redirect($path);
    }
}

/**
 * Previous
 *
 * @return mixed
 */
if (! function_exists('previous')) {
    function previous() {
        return \My_MVC\Url\Url::previous();
    }
}

/**
 * Url path
 *
 * @param string $path
 * @return mixed
 */
if (! function_exists('url')) {
    function url($path) {
        return \My_MVC\Url\Url::path($path);
    }
}

/**
 * Asset path
 *
 * @param string $path
 * @return mixed
 */
if (! function_exists('asset')) {
    function asset($path) {
        return \My_MVC\Url\Url::path($path);
    }
}

/**
 * Dump and die
 *
 * @param string $data
 * @return void
 */
if (! function_exists('dd')) {
    function dd($data) {
        echo "<pre>";
        if (is_string($data)) {
            echo $data;
        } else {
            print_r($data);
        }
        echo "</pre>";
        die();
    }
}

/**
 * Get session data
 *
 * @param string $key
 * @return string $data
 */
if (! function_exists('session')) {
    function session($key) {
        return \My_MVC\session\session::get($key);
    }
}

/**
 * Get session flash data
 *
 * @param string $key
 * @return string $data
 */
if (! function_exists('flash')) {
    function flash($key) {
        return \My_MVC\session\session::flash($key);
    }
}

/**
 * Show pagination links
 *
 * @param string $current_page
 * @param string $pages
 * @return string
 */
if (! function_exists('links')) {
    function links($current_page, $pages) {
        return \My_MVC\Database\Database::links($current_page, $pages);
    }
}

/**
 * Table auth
 *
 * @param string $table
 * @return string
 */
if (! function_exists('auth')) {
    function auth($table) {
        $auth = \My_MVC\Session\Session::get($table) ?: \My_MVC\Cookie\Cookie::get($table);
        return \My_MVC\Database\Database::table($table)->where('id', '=', $auth)->first();
    }
}