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

            // utility to get bullet points from JSON
            function get_bullet_points($json){
                $bullet_points_path_array = explode('.', $_SESSION['$bullet_points_path']);
                $bullet_points = $json[$bullet_points_path_array[0]][$bullet_points_path_array[1]][$bullet_points_path_array[2]];
                return $bullet_points;
            }


            //utility to convert HTML url to JSON url
            function htj($url){
                $url = str_replace('html', 'json', $url);
                return $url;
            }

            //utility to get specifications from JSON
            function get_specs($json){
                $specs_path_array = explode('.', $_SESSION['$specs_path']);
                $specs = $json[$specs_path_array[0]][$specs_path_array[1]][$specs_path_array[2]];
                $specs = htj($specs);
                return get_json("https://deere.com".$specs);
            }

            //utility to get features from JSON
            function get_features($json){
                $features_path_array = explode('.', $_SESSION['$features_path']);
                $features = $json[$features_path_array[0]][$features_path_array[1]][$features_path_array[2]];
                $features = htj($features);
                return get_json("https://deere.com".$features);
            }

            //utility to get accessories from JSON
            function get_accessories($json){
                $accessories_path_array = explode('.', $_SESSION['$accessories_path']);
                $accessories = $json[$accessories_path_array[0]][$accessories_path_array[1]][$accessories_path_array[2]];
                $accessories = htj($accessories);
                return get_json("https://deere.com".$accessories);
            }

            //utility to update bullet_points in SQL database
            function update_bullet_points($url, $bullet_points){
                global $db;
                $can_update = !strpos($bullet_points, 'Failed to get JSON: URL returned "404 Not Found"');
                if ($can_update){
                    $query = "UPDATE ".$_SESSION['database'].".".$_SESSION['table']." SET bullet_points = '$bullet_points' WHERE equip_link = '$url'";
                    $result = $db -> query($query);
                    if ($result){
                        return true;
                    } else {
                        return false;
                    }

                } else {
                    return false;}
            }

            //utility to update specifications in SQL database
            function update_specifications($url, $specs){
                global $db;
                $specs = str_replace("'", "\'", $specs);
                $query = "UPDATE ".$_SESSION['database'].".".$_SESSION['table']." SET specs = '$specs' WHERE equip_link = '$url'";

                $result = $db -> query($query);
                if ($result){
                    return true;
                } else {
                    return false;
                }
            }

            //utility to update features in SQL database
            function update_features($url, $features){
                global $db;
                $features = str_replace("'", "\'", $features);
                $query = "UPDATE ".$_SESSION['database'].".".$_SESSION['table']." SET features = '$features' WHERE equip_link = '$url'";
                $result = $db -> query($query);
                if ($result){
                    return true;
                } else {
                    return false;
                }
            }

            //utility to update accessories in SQL database
            function update_accessories($url, $accessories){
                global $db;
                $accessories = str_replace("'", "\'", $accessories);
                $query = "UPDATE ".$_SESSION['database'].".".$_SESSION['table']." SET accessories = '$accessories' WHERE equip_link = '$url'";
                $result = $db -> query($query);
                if ($result){
                    return true;
                } else {
                    return false;
                }

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
            $db = new mysqli($_SESSION['hostname'], $_SESSION['username'], $_SESSION['password'], $_SESSION['database']);
            $db->set_charset($_SESSION['charset']);

            // Logging (remove for production)
            if ($db->connect_error){
                echo '<p class="error">Database connection error</p>';
            } else {
                echo '<p class="success">Database connection successful</p>';
            }
            // End logging

            if (array_key_exists('equipment', $_POST)){
            $equipment = $_POST['equipment'];
            if (!gettype($equipment) === 'array'){
                $equipment = [$equipment];
            }
            equipment_foreach($equipment, function($value){

                // Get equipment URL
                $e_url = equipment_getUrl($value);
                // Logging (remove for production)
                echo '<h2>'.$value.'</h2>';
                echo '<p>URL: '.$e_url.'</p>';
                // End logging

                // URL to JSON
                $json = get_json($e_url);
                if ($json){
                // Get bullet points
                $bullet_points = get_bullet_points($json);

                //get specifications
                $specs = get_specs($json);

                //get features
                $features = get_features($json);

                //get accessories
                $accessories = get_accessories($json);

                // Update bullet points
                $update_bp = update_bullet_points($e_url, $bullet_points);
                $update_f = update_features($e_url, json_encode($features));
                $update_s = update_specifications($e_url, json_encode($specs));
                $update_a = update_accessories($e_url, json_encode($accessories));
                // Logging (remove for production)
                    if ($update_bp){
                        echo '<p class="success">Bullet points updated for '.$value.'</p>';
                    } else {
                        echo '<p class="error">Failed to update bullet points for '.$value.'</p>';
                    }
                    if ($update_f){
                        echo '<p class="success">Features updated for '.$value.'</p>';
                    } else {
                        echo '<p class="error">Failed to update features for '.$value.'</p>';
                    }
                    if ($update_s){
                        echo '<p class="success">Specifications updated for '.$value.'</p>';
                    } else {
                        echo '<p class="error">Failed to update specifications for '.$value.'</p>';
                    }
                    if ($update_a){
                        echo '<p class="success">Accessories updated for '.$value.'</p>';
                    } else {
                        echo '<p class="error">Failed to update accessories for '.$value.'</p>';
                    }
                // End logging
                
            } else {echo '<p class="error">Failed to get JSON for '.$value.': URL returned "404 Not Found"</p><p class = "error">No updates will be made</p>';};
            echo '<hr/>';}
            
        );}

            // No equipment selected; error
            else {
                echo '<p class="error">No equipment selected</p>';
            }

        ?>
    </body>
</html>