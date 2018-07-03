<?php
/**
 * Plugin Name: UCF-CREOL-SQL-Json-Reader
 * Version: 0.3.2
 * Author: Raphael Miller for UCF CREOL
 * Description: This plugin collects information from a server with a json endpoint, and converts the json data to a
 * pretty format based on the Colleges requirements.
 * Github Theme URI: https://github.com/UCF/UCF-CREOL-json-formatter
 *
 * Requirements: UCF Colleges Theme. SQL Server with Json endpoint or SQL API with Json Endpoint
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

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

function init_option_ui(){
//    echo "<h1>SQL Json Reader</h1>"; //todo: look up wordpress directory settings
//    echo '  <form action="ucf-creol-json-reader.php" method="post">
//                URL to Json Feed<br>
//                <input type="text" name="jsonURI">
//                <input type="submit">
//            </form>';
    //$json_obj = get_json_from_url("https://api.creol.ucf.edu/test.aspx?GrpID=1");
//    $result = curl_url("https://api.creol.ucf.edu/test.aspx?GrpID=1");
//    //echo $result;
//    $json_result = jsonifyier($result);
//    foreach ($json_result as $json_arr){
//        echo $json_arr['PeopleID'];
//        echo $json_arr['FirstName'];
//        echo $json_arr['LastName'];
//    }

}

/**
 * jsonifyier() - gets json string and decodes json into php object
 *
 * @param $result
 * @return array|mixed|object
 */
function jsonifyier($result){
    return json_decode($result, JSON_PRETTY_PRINT);
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

/**
 * display_img_card_deck() - displays a table using UCF colleges theme (Bootstrap) in order to produce a template for
 * all values in the json string.
 *
 * @param $json jsonObj
 */
function display_img_card_deck($json){

    echo '<div class="container">';
    echo '<div class="row align-items-center">';

    foreach ($json as $item){
//        echo '<div class="col-sm-6">';
//        echo '<div class="card-primary" >';
//        echo '<a href="https://www.creol.ucf.edu/People/Details.aspx?PeopleID=9117">';
//        echo '<img class="" style="width: 25%"
//            src="https://www.creol.ucf.edu/People/images/100x150Portrait/'; echo $item['PeopleID']; echo '.jpg">';
//        echo '<div class="card-body>"';
//        echo '<h5 class="card-title">'.$item['FirstName'].'</h5>';
//        echo '<h5 class="card-title">'.$item['LastName'].'</h5>';
//        echo '</a>';
//        echo '</div>';
//        echo '</div>';
//        echo '</div>';

        echo '
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <a class="" href="https://www.creol.ucf.edu/People/Details.aspx?PeopleID=9117">
                                        <img src="https://www.creol.ucf.edu/People/images/100x150Portrait/'.$item['PeopleID'].'.jpg"></a>
                                    </div>
                                    <a class="card-title alignright" href="https://www.creol.ucf.edu/People/Details.aspx?PeopleID=9117">
                                    <div class="col-sm-6">
                                        <h3 class="">'.$item['FirstName'].'</h3>
                                        <p class="">'.$item['LastName'].'</p>
                                    </div>
                                    </a>
                                </div>
                            </div>
                        </div>';
    }

    echo '</div>';
    echo '</div>';



}

/**
 * build_uri_string() - creates the uri string from the shortcode args given.
 *
 * @param $uri_components
 * @return string
 */
function build_uri_string($uri_components){
    return $uri_components['base_uri'] . '?grpID=' . $uri_components['group'];
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

    $result = build_uri_string($a);
    $json_string = curl_url($result);
    $json_obj = jsonifyier($json_string);
    display_img_card_deck($json_obj);
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
 * @param $atts phpArray
 */
function display_json($atts ){
    $a = shortcode_atts( array(
        'base_uri' => 'https://api.creol.ucf.edu/test.aspx',
        'group' => 1,
        'view' => ''
    ), $atts );

    $result = build_uri_string($a);
    $json_string = curl_url($result);
    $json_obj = jsonifyier($json_string);
    display_img_card_deck($json_obj);
}
add_shortcode( 'display_json', 'display_json' );

