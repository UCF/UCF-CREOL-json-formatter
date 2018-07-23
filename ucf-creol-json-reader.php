<?php
/**
 * Plugin Name: UCF-CREOL-SQL-Json-Reader
 * Version: 0.4.5
 * Author: Raphael Miller for UCF CREOL
 * Description: This plugin collects information from a server with a json endpoint, and converts the json data to a
 * pretty format based on the Colleges requirements.
 * Github Plugin URI: https://github.com/UCF/UCF-CREOL-json-formatter
 *
 * Requirements: UCF Colleges Theme. SQL Server with Json endpoint or SQL API with Json Endpoint
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once("includes/utils.php");
require_once("includes/layout.php");

add_action( 'admin_menu', 'UCF-CREOL_SQL_Json_Reader' );

function SQL_DB_Connector(){

    $page_title = 'SQL Json Reader';
    $menu_title = 'SQL Json Reader';
    $capability = 'manage_options';
    $menu_slug  = 'extra-post-info';
    $function   = 'init_option_ui';
    $icon_url   = 'dashicons-external';
    $position   = 4;

    add_menu_page( $page_title,
        $menu_title,
        $capability,
        $menu_slug,
        $function,
        $icon_url,
        $position );
}


/**
 *  display_json_shortcode() - first test for reader from Server api. look at display_json for more information, will
 *  delete later.
 *
 * @param $atts
 */
function display_json_shortcode($atts ){
    $a = shortcode_atts( array(
        'base_uri' => 'https://api.creol.ucf.edu/test.aspx',
        'group' => 1
    ), $atts );

    $result = build_uri_string_directory($a);
    $json_string = curl_url($result);
    $json_obj = jsonifyier($json_string);
    layout_people($json_obj);
}
add_shortcode( 'display_json_gen_table', 'display_json_shortcode' );

/**
 * display_json() - connects to sql database outside of the wordpress from C&M team. basic functionality of collecting
 * json data collected from server.
 *
 * $atts - an array that contains args for the specific call to be produced. Current working example is a call to
 * collect a json object that contains PEOPLE from the CREOL server. The function parses the information given by the
 * function call, based on that call a url string is created to interact with the server using curl. see phpcURL library
 * the curl function returns a json recognizable string. this is converted to a json-php object. From that information,
 * the formatter prints the information in a pretty format for committee review at a later date.
 *
 * @param $atts array
 */
function ucf_creol_people_directory_shortcode($atts ){
    $a = shortcode_atts( array(
        'base_uri' => 'https://api.creol.ucf.edu/SqltoJson.aspx',
        'stored_procedure' => 'WWWDirectory',
        'grp_id' => 2,
        'show_fields' => true
    ), $atts );

    $result = build_uri_string_directory($a);
    //var_dump($result);
    $json_string = curl_url($result);
    //var_dump($json_string);
    $json_obj = jsonifyier($json_string);


    if($atts['show_fields'] == true) {
        layout_people($json_obj);
    } else {
        echo $json_string;
    }
}
add_shortcode( 'ucf-creol-people-directory', 'ucf_creol_people_directory_shortcode' );

/**
 * ucf_creol_publications-shortcode() - shortcode gen for pub shortcode
 *
 * @param $args
 */
function ucf_creol_publications_shortcode($args ){
    $a = shortcode_atts( array(
        'base_uri' => 'https://api.creol.ucf.edu/SqltoJson.aspx',
        'stored_procedure' => 'WWWPublications',
        'typelist' => '3',      //STRING sep with commas.
        'year' => 0,
        'peopleid' => 0,
        'page' => 1,
        'pagesize' => 3
    ), $args );

    $result = build_uri_string_publications($a);
    $json_string = curl_url($result);
    $json_obj = jsonifyier($json_string);
    $_POST['json_obj'] = $json_obj;
    layout_publications($json_obj);
}
add_shortcode( 'ucf-creol-pub', 'ucf_creol_publications_shortcode' );

function ucf_creol_generic_shortcode($args ){
    $a = shortcode_atts( array(
        'base_uri' => 'https://api.creol.ucf.edu/SqltoJson.aspx',
        'stored_procedure' => 'WWWPublications',
//        'typelist' => '3',      //STRING sep with commas.
//        'year' => 0,
//        'peopleid' => 0,
//        'page' => 1,
//        'pagesize' => 3,
//        'grpid' => 0,
//        'layout' => 'pub'
    ), $args );

    $result = build_uri_string_publications($a, $args);
    $json_string = curl_url($result);
    $json_obj = jsonifyier($json_string);

    //var_dump($args);

    switch ($args['stored_procedure']){
        case "WWWPublications":
            layout_publications($json_obj);
            break;
        case "WWWDirectory":
            layout_people($json_obj);
            break;
        default:
            echo 'Error: stored procedure is missing or invalid';
            break;
    }

    //layout_people($json_obj);
}
add_shortcode( 'ucf-creol', 'ucf_creol_generic_shortcode' );

