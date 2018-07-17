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
            $pdf_button = "<button class='button btn-primary'><a href=\"$pdf_uri\">Download PDF</a></button>";
        } else {
            $pdf_button = '';
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