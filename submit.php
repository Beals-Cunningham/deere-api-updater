<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php
            session_start();

//------BEGIN utility functions-------

            // utility for_each wrapper
            function equipment_foreach($equipment, $function){
                foreach ($equipment as $key => $value){
                    $function($value);
                }
            }

            // utility for getting JSON from URL
            function get_json($url){
                if (!$url){
                    return false;
                }
                $json = json_decode(file_get_contents($url), true);
                if ($json){
                    return $json;
                } else {
                    return false;
                }
            }

            // utility to get "bullet points" from JSON
            function get_bullet_points($json){
                $bullet_points_path_array = explode('.', $_SESSION['$bullet_points_path']);
                $bullet_points = $json[$bullet_points_path_array[0]][$bullet_points_path_array[1]][$bullet_points_path_array[2]];
                return $bullet_points;
            }

            // utility to get URL from _SESSION
            function equipment_getUrl($e){
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
                if ($json){
                // Bullet points
                $bullet_points = get_bullet_points($json);
                echo '<p>'.$bullet_points.'</p>';
                }
                else {echo '<p class="error">Failed to get JSON: URL returned "404 Not Found"</p>';}
            });} 

            // No equipment selected; error
            else {
                echo '<p class="error">No equipment selected</p>';
            }

        ?>
    </body>
</html>