<?php
/**
 * Plugin Name: UCF-CREOL-SQL-Json-Reader
 * Version: 0.4.0
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

function display_publications($json_obj){
    $pub_base_url = 'https://www.creol.ucf.edu/Research/Publications/';

    echo '<h4>Publications</h4>';
    foreach ($json_obj as $json_item){
        //echo $json_item['Authors'];
        $pdf_uri = $pub_base_url . $json_item['PublicationID'] . '.pdf';
        $pub_year = $json_item['PublicationYear'];
        $pub_month = DateTime::createFromFormat('!m', $json_item['PublicationMonth']);
        $month_name = $pub_month->format('F');
        $pub_authors = $json_item['Authors'];
        $pub_title = $json_item['Title'];
        $pub_ref = $json_item['Reference'];

        $header = get_headers($pdf_uri, 1);
        //var_dump($header['Content-Type']);

        //$check_url_status = check_uri($pdf_uri);
        if($header['Content-Type'] == 'application/pdf'){
            $pdf_button = "<button class='button btn-primary'><a href=\"$pdf_uri\"><i class=\"fa fa-download\"></i>Download PDF</a></button>";
        } else {
            $pdf_button = '';
        }

            echo "
                <div class='row'>
                    <div class='col-sm-2'>
                        $pdf_button
                    </div>
                    <div class='col-sm-2'>
                        <h6>$pub_year, $month_name</h6>
                    </div>
                    <div class='col'>
                        <p>Author(s): $pub_authors</p>
                        <p>Publication Title: $pub_title</p>
                        <p>Journal/ Reference: $pub_ref</p>
                    </div>
                </div>
                <p></p>
            ";

            //echo "$key = $value<br><br>";


    }
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
                    echo '<p>' . $json_items['Phone'] . '</p>';
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
function build_uri_string_directory($uri_components){
    return $uri_components['base_uri'] . '?' . $uri_components['stored_procedure'] . '&' . 'GrpID=' . $uri_components['grp_id'];
}

/**
 * build_uri_string_publications() - builds the url string from the shortcode arguments list.
 * @param $uri_components
 * @return string
 */
function build_uri_string_publications($uri_components){
    //var_dump($uri_components);
    $url_concat = '';
    $last_element = end($uri_components);
    foreach ($uri_components as $k => $v){
        if($k == 'base_uri'){
            $url_concat = $url_concat . $v . "?";
        } else if($k == 'stored_procedure'){
            $url_concat = $url_concat . $v . '&';
        } else if($v == $last_element){
            $url_concat = $url_concat . $k . '=' . $v;
        } else {
            $url_concat = $url_concat . $k . '=' . $v . '&';
        }
    }
    return $url_concat;
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
        'grp_id' => 2,
        'show_fields' => true
    ), $atts );

    $result = build_uri_string_directory($a);
    //var_dump($result);
    $json_string = curl_url($result);
    //var_dump($json_string);
    $json_obj = jsonifyier($json_string);


    if($atts['show_fields'] == true) {
        display_people($json_obj);
    } else {
        echo $json_string;
    }
}
add_shortcode( 'ucf-creol-people-directory', 'display_people_directory' );

/**
 * ucf_creol_publications-shortcode() - shortcode gen for pub shortcode
 *
 * @param $args
 */
function ucf_creol_publications_shortcode($args ){
    $a = shortcode_atts( array(
        'base_uri' => 'https://api.creol.ucf.edu/SqltoJson.aspx',
        'stored_procedure' => 'WWWPublications',
        'TypeList' => '3',      //STRING sep with commas.
        'Year' => 0,
        'peopleid' => 0,
        'page' => 1,
        'pageSize' => 10
    ), $args );


    $result = build_uri_string_publications($a);
    $json_string = curl_url($result);
    $json_obj = jsonifyier($json_string);
    //var_dump($json_obj);

    display_publications($json_obj);
}
add_shortcode( 'ucf-creol-pub', 'ucf_creol_publications_shortcode' );

