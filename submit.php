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

            //utility to update bullet_points in SQL database
            function update_bullet_points($url, $bullet_points){
                $can_update = !strpos($bullet_points, 'Failed to get JSON: URL returned "404 Not Found"');
                if ($can_update){
                    $query = "UPDATE ".$_SESSION['database'].".".$_SESSION['table']." SET bullet_points = '$bullet_points' WHERE equip_link = '$url'";
                    echo '<p> Query: '.$query.'</p>';
                    $result = $db -> query($query);
                    if ($result){
                        return true;
                    } else {
                        return false;
                    }

                } else {
                    return false;}
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
            
            // Connect to database
            $db = new mysqli($_SESSION['host'], $_SESSION['user'], $_SESSION['pass'], $_SESSION['database']);

            if (array_key_exists('equipment', $_POST)){
            $equipment = $_POST['equipment'];
            if (!gettype($equipment) === 'array'){
                $equipment = [$equipment];
            }
            equipment_foreach($equipment, function($value){

                // Get equipment URL
                $e_url = equipment_getUrl($value);
                echo '<p>'.$value.' - '.$e_url.'</p>';

                // URL to JSON
                $json = get_json($e_url);
                if ($json){
                // Get bullet points
                $bullet_points = get_bullet_points($json);

                // Update bullet points
                $update_bp = update_bullet_points($e_url, $bullet_points);
                // Logging (remove for production)
                    if ($update_bp){
                        echo '<p class="success">Bullet points updated for '.$value.'</p>';
                    } else {
                        echo '<p class="error">Failed to update bullet points for '.$value.'</p>';
                    }
                // End logging
                
            }else {echo '<p class="error">Failed to get JSON: URL returned "404 Not Found"</p>';}});}

            // No equipment selected; error
            else {
                echo '<p class="error">No equipment selected</p>';
            }

        ?>
    </body>
</html>