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



function layout_people($json){

    //offline debug
    $path = dirname(__DIR__);
    $get_json_sample = file_get_contents( $path . '/tests/sample1.json');
    $parse_json = json_decode($get_json_sample, JSON_PRETTY_PRINT);
    $json = $parse_json;



    echo "<div class='row'>";
    foreach ($json as $item){
        $photo_url = 'https://www.creol.ucf.edu/People/images/200x300Portrait/' . $item['PeopleID'] . '.jpg';
        $site_url = 'https://www.creol.ucf.edu/People/Details.aspx?PeopleID=' . $item['PeopleID'];
        $name = $item['FullName'];
        $room_no = $item['Location'];
        $email = $item['Email'];
        $phone = $item['Phone'];
        $position = $item['Position'];

        $check_photo = check_header($photo_url);
        if($check_photo != 'image/jpeg'){
            $photo_url = 'https://www.creol.ucf.edu/People/images/100x150Portrait/NoImage.jpg';
        }

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

function layout_publications($json_obj){
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
            $pdf_button = "<button class='button btn-primary'><a href=\"$pdf_uri\">Download PDF</a></button>";
        } else {
            $pdf_button = '<button class="disabled">PDF not available</button>';
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