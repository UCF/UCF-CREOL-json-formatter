<?php
/**
 * Plugin Name: UCF-CREOL-SQL-Json-Reader
 * Version: 0.3.3
 * Author: Raphael Miller for UCF CREOL
 * Description: This plugin collects information from a server with a json endpoint, and converts the json data to a
 * pretty format based on the Colleges requirements.
 * Github Plugin URI: https://github.com/UCF/UCF-CREOL-json-formatter
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

//function init_option_ui(){
////    echo "<h1>SQL Json Reader</h1>"; //todo: look up wordpress directory settings
////    echo '  <form action="ucf-creol-json-reader.php" method="post">
////                URL to Json Feed<br>
////                <input type="text" name="jsonURI">
////                <input type="submit">
////            </form>';
//    //$json_obj = get_json_from_url("https://api.creol.ucf.edu/test.aspx?GrpID=1");
////    $result = curl_url("https://api.creol.ucf.edu/test.aspx?GrpID=1");
////    //echo $result;
////    $json_result = jsonifyier($result);
////    foreach ($json_result as $json_arr){
////        echo $json_arr['PeopleID'];
////        echo $json_arr['FirstName'];
////        echo $json_arr['LastName'];
////    }
//
//}

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
 * display_people() - displays a table using UCF colleges theme (Bootstrap) in order to produce a template for
 * all values in the json string. This assumes an array with associative values that are specific to the query.
 * contact please to refer to header address api.creol.ucf.edu/SqltoJson.aspx for more information.
 *
 * @param $json array
 */
function display_people($json){

    echo '<div class="row mx-5 my-5">';
    foreach($json as $json_items){
        echo '<div class="col-lg-6">';

            echo '<div class="row align-items-top my-3 mx-3">';
                echo '<div class="col-lg-6">';
                if (key($json_items ) == 'PeopleID'){
                    echo '<a href="https://www.creol.ucf.edu/People/Details.aspx?PeopleID='.$json_items['PeopleID'].'">
                    <img src="https://www.creol.ucf.edu/People/images/200x300Portrait/'.$json_items['PeopleID'].'.jpg"></a>';
                } else {
                    echo '<img src="https://www.creol.ucf.edu/People/images/100x150Portrait/NoImage.jpg">';
                }
                echo '</div>';
                echo '<div class="col-lg-6">';

                if(array_key_exists('FullName', $json_items)){
                    echo '<a style=" color: #0a0a0a" href="https://www.creol.ucf.edu/People/Details.aspx?PeopleID='.$json_items['PeopleID'].'">';
                    echo '<h5>' .$json_items['FullName'] . '</h5></a>';
                } else{
                    echo '<a href="https://www.creol.ucf.edu/People/Details.aspx?PeopleID='.$json_items['PeopleID'].'">';
                    echo '<h5>' . $json_items['FirstName'] . $json_items['LastName'] . '</h5></a>';
                }

                if(array_key_exists('Position', $json_items)){
                    echo '<h6>' . $json_items['Position'] . '</h6>';
                }

                if(array_key_exists('Phone', $json_items)){
                    echo '<p><strong>Phone:</strong> ' . $json_items['Phone'] . '</p>';
                }

                if(array_key_exists('Email', $json_items)){
                    echo '<a style="text-decoration: none" href="mailto:'. $json_items['Email'] .'"><p>'
                        . $json_items['Email'] . '</p></a>';
                }

                if(array_key_exists('Location', $json_items)){
                    echo '<p>' . $json_items['Location'] . '</p>';
                }

                echo'</div>';
            echo '</div>';
        echo '</div>';
    }

    echo '</div>';


}

/**
 * build_uri_string() - creates the uri string from the shortcode args given.
 *
 * @param $uri_components
 * @return string
 */
function build_uri_string($uri_components){
    return $uri_components['base_uri'] . '?' . $uri_components['stored_procedure'] . '&' . 'GrpID=' . $uri_components['grp_id'];
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
    display_people($json_obj);
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
function display_people_directory($atts ){
    $a = shortcode_atts( array(
        'base_uri' => 'https://api.creol.ucf.edu/SqltoJson.aspx',
        'stored_procedure' => 'WWWDirectory',
        'grp_id' => 2
    ), $atts );

    $result = build_uri_string($a);
    //var_dump($result);
    $json_string = curl_url($result);
    //var_dump($json_string);
    $json_obj = jsonifyier($json_string);
    //var_dump($json_obj);
//    foreach ($json_obj as $json_arr){
//        while($key_val = current($json_arr)){
//            echo $json_arr[key($json_arr)] . ' ';
//            next($json_arr);
//            echo '<br>';
//        }
//    }
    display_people($json_obj);
}
add_shortcode( 'ucf-creol-people-directory', 'display_people_directory' );

