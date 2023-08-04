<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php
            session_start();

//------BEGIN utility functions-------

            //utility for_each wrapper
            function equipment_foreach($equipment, $function){
                foreach ($equipment as $key => $value){
                    $function($value);
                }
            }

            //utility for getting JSON from URL
            function get_json($url){
                $json = json_decode(file_get_contents($url), true);
                return $json;
            }

            //utility to get "bullet points" from JSON
            function get_bullet_points($json){
                $bullet_points_path = 'Page.product-summary.ProductOverview';
                $bullet_points_path_array = explode('.', $bullet_points_path);
                $bullet_points = $json[$bullet_points_path_array[0]][$bullet_points_path_array[1]][$bullet_points_path_array[2]];
                return $bullet_points;
            }

            function equipment_getUrl($e){
                //Should get $urls from GLOBALS
                if (array_key_exists($e, $_SESSION['urls'])){
                    $url = $_SESSION['urls'][$e];
                    return $url;
                } else {
                    return 'Failed to fetch URL for '.$e;
                }
                return $url;
            }

//------END utility functions-------
//------BEGIN main script-----------

            if (array_key_exists('equipment', $_POST)){
            $equipment = $_POST['equipment'];
            if (!gettype($equipment) === 'array'){
                $equipment = [$equipment];
            }
            equipment_foreach($equipment, function($value){

                // Equipment URL
                $e_url = equipment_getUrl($value);
                echo '<p>'.$value.' - '.$e_url.'</p>';

                // URL to JSON
                $json = get_json($e_url);
    
                // Bullet points
                $bullet_points = get_bullet_points($json);
                echo '<p>'.$bullet_points.'</p>';
            });} 

            //No equipment selected; error
            else {
                echo '<p class="error">No equipment selected</p>';
            }

        ?>
    </body>
</html>