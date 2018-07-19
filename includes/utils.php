<?php
/**
 * Created by PhpStorm.
 * User: raphael
 * Date: 7/17/18
 * Time: 9:03 AM
 */

/**
 * build_uri_string_publications() - builds the url string from the shortcode arguments list.
 * @param $shortcode_defaults
 * @return string
 */
function build_uri_string_publications($shortcode_defaults, $shortcode_args){
    $url_concat = '';
    $arr = array(key($shortcode_defaults) => $shortcode_defaults['base_uri']) + $shortcode_args;
    //var_dump($arr);
    end($arr);
    $last_element = key($arr);

    if($arr['stored_procedure'] == 'WWWDirectory'){
        if(!array_key_exists('grpid', $arr)){
            return 'argument expects group id value';
        }
    }

    foreach ($arr as $k => $v){
        if($k == 'base_uri'){
            $url_concat = $url_concat . $v . "?";
        } else if($k == 'stored_procedure'){
            $url_concat = $url_concat . $v . '&';
        } else if($k == $last_element){
            $url_concat = $url_concat . $k . '=' . $v;
        } else {
            $url_concat = $url_concat . $k . '=' . $v . '&';
        }
    }

    //echo $url_concat;
    return $url_concat;
}

/**
 * build_uri_string() - creates the uri string from the shortcode args given.
 *
 * @param $uri_components
 * @return string
 */
function build_uri_string_directory($uri_components){
    return $uri_components['base_uri'] . '?' . $uri_components['stored_procedure'] . '&' . 'GrpID=' . $uri_components['grp_id'];
}

function check_uri($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);

    return $headers['http_code'];
}

/**
 * curl_url() - creates a connection to url and returns the contents of curl. expected json string.
 *
 * @param $url
 * @return mixed json_string
 */
function curl_url($url){
    // Step 1
    $cSession = curl_init();
// Step 2
    curl_setopt($cSession,CURLOPT_URL,$url);
    curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($cSession,CURLOPT_HEADER, false);
// Step 3
    $result=curl_exec($cSession);
// Step 4
    curl_close($cSession);
// Step 5
    //echo $result;
    return $result;
}

function jsonifyier($result){
    return json_decode($result, JSON_PRETTY_PRINT);
}