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
 * @param $shortcode_args
 * @return string
 */
function build_uri_string($shortcode_defaults, $shortcode_args){
    $url_concat = '';
    //$arr = array(key($shortcode_defaults) => $shortcode_defaults['base_uri']) + $shortcode_args;
    //var_dump($arr);

    //non-query arguments
    if(array_key_exists('layout', $shortcode_defaults)){
        unset($shortcode_defaults['layout']);
    }
    if(array_key_exists('debug', $shortcode_defaults)){
        unset($shortcode_defaults['debug']);
    }

    //parse between WWWpublications arguments and WWWDirectory arguments
    if($shortcode_defaults['stored_procedure'] == 'WWWDirectory'){

        unset($shortcode_defaults['typelist']);
        unset($shortcode_defaults['year']);
        unset($shortcode_defaults['peopleid']);
        unset($shortcode_defaults['page']);
        unset($shortcode_defaults['pagesize']);

        if(!array_key_exists('grpid', $shortcode_defaults)){
            echo 'stored procedure expects grpid value in shortcode argument';
            return 'argument expects group id value';
        }
    } elseif ($shortcode_defaults['stored_procedure'] == 'WWWPublications'){
        if(array_key_exists('grpid', $shortcode_defaults)){
            unset($shortcode_defaults['grpid']);
        }
    }

    end($shortcode_defaults);
    $last_element = key($shortcode_defaults);

    foreach ($shortcode_defaults as $k => $v){
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

function check_header($url){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url
    ));
    curl_exec($curl);
    $content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    curl_close($curl);
    return $content_type;

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
    //var_dump(json_decode($result, JSON_PRETTY_PRINT));
    echo json_last_error();
    return json_decode($result);
}

function json_clean($json){
    // This will remove unwanted characters.
// Check http://www.php.net/chr for details

    $checkLogin = $json;
    for ($i = 0; $i <= 31; ++$i) {
        $checkLogin = str_replace(chr($i), "", $checkLogin);
    }
    $checkLogin = str_replace(chr(127), "", $checkLogin);

// This is the most common part
// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
// here we detect it and we remove it, basically it's the first 3 characters
    if (0 === strpos(bin2hex($checkLogin), 'efbbbf')) {
        $checkLogin = substr($checkLogin, 3);
    }

    $checkLogin = json_decode($checkLogin);
    print_r($checkLogin);
}

function removeBOM($data) {
    if (0 === strpos(bin2hex($data), 'efbbbf')) {
        return substr($data, 3);
    }
    return $data;
}

function curl_api($result){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $result);
    $result_curl = curl_exec($ch);
    curl_close($ch);

    return $result_curl;
}

function debug_print($obj, $result){
    echo '<pre><code>';
    echo "json error msg: " . json_last_error_msg() . "<br>";
    echo "curl target: $result <br><br>";
    foreach ($obj as $response){
        echo 'response : <br>';
        foreach($response as $array){
            echo "    Array : <br>";
            foreach ($array as $item => $value){
                echo "        $item : $value <br>";
            }
        }
    }
    echo '</code></pre>';
}