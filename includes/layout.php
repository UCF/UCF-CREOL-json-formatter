<?php
/**
 * Created by PhpStorm.
 * User: raphael
 * Date: 7/17/18
 * Time: 3:24 PM
 */

/**
 * display_people() - displays a table using UCF colleges theme (Bootstrap) in order to produce a template for
 * all values in the json string. This assumes an array with associative values that are specific to the query.
 * contact please to refer to header address api.creol.ucf.edu/SqltoJson.aspx for more information.
 *
 * @param $json array
 */


/**
 * layout_people() - a layout that is specifically designed for people json pulls
 *
 * @param $json object
 */
function layout_people($json){

    //offline debug
    $path = dirname(__DIR__);
    $get_json_sample = file_get_contents( $path . '/tests/sample1.json');
    $parse_json = json_decode($get_json_sample, JSON_PRETTY_PRINT);
    $json = $parse_json;


    //begin loop for each json element
    echo "<div class='row'>";
    foreach ($json as $item){

        //section off json items for later use.
        $photo_url = 'https://www.creol.ucf.edu/People/images/200x300Portrait/' . $item['PeopleID'] . '.jpg';
        $site_url = 'https://www.creol.ucf.edu/People/Details.aspx?PeopleID=' . $item['PeopleID'];
        $name = $item['FullName'];
        $room_no = $item['Location'];
        $email = $item['Email'];
        $phone = $item['Phone'];
        $position = $item['Position'];

        //checks if a photo exists, if not then generate no-image jpg.
        $check_photo = check_header($photo_url);
        if($check_photo != 'image/jpeg'){
            $photo_url = 'https://www.creol.ucf.edu/People/images/100x150Portrait/NoImage.jpg';
        }

        //html generation
        echo "
                <div class='col-lg-3 mb-3'>
                    <a href='$site_url'><img class='card' src='$photo_url'></a>
                </div>
                <div class='col-lg-3'>
                    <h5>$name</h5>
                    <h6>$position</h6>
                    <p>$room_no</p>
                    <p>$phone</p>
                    <a href='mailto:$email'><p>$email</p></a>
                </div>
        ";
    }
    echo '</div>';
}

/**
 * layout_publications() - layout for publications,
 *
 * @param $json_obj object
 */
function layout_publications($json_obj){

    //offline debug
    $path = dirname(__DIR__);
    $get_json_sample = file_get_contents( $path . '/tests/sample2.json');
    $parse_json = json_decode($get_json_sample, JSON_PRETTY_PRINT);
    $json_obj = $parse_json;

    //url for publications database store.
    $pub_base_url = 'https://www.creol.ucf.edu/Research/Publications/';

    //begin loop for json object elements
    echo '<h4>Publications</h4>';
    foreach ($json_obj as $json_item){

        //section off json items for later use.
        $pdf_uri = $pub_base_url . $json_item['PublicationID'] . '.pdf';
        $pub_year = $json_item['PublicationYear'];
        $pub_month = DateTime::createFromFormat('!m', $json_item['PublicationMonth']);
        $month_name = $pub_month->format('F');
        $pub_authors = $json_item['Authors'];
        $pub_title = $json_item['Title'];
        $pub_ref = $json_item['Reference'];

        $check_pdf_result = check_header($pdf_uri);
        //var_dump($header['Content-Type']);

        //$check_url_status = check_uri($pdf_uri);
        if($check_pdf_result == 'application/pdf'){
            $pdf_button = "<button class='button btn-primary'><a href=\"$pdf_uri\">Download PDF</a></button>";
        } else {
            $pdf_button = '<button class="button disabled">Not Available</button>';
        }

        echo "
                <div class='row'>
                    <div class='col-sm-2'>
                        $pdf_button
                    </div>
                    <div class='col-sm-1'>
                        <h6>$pub_year</h6>
                    </div>
                    <div class='col'>
                        <p>
                        $pub_authors<br>
                        <i>$pub_title</i><br>
                        $pub_ref<br>
                        </p>
                    </div>
                </div>
                <p></p>
            ";
        //echo "$key = $value<br><br>";
    }
}

function display_search(){

}

function non_image_layout(){

}

function image_layout(){

}