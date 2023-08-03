<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php
            //utility for_each wrapper
            function equipment_foreach($equipment, $function){
                foreach ($equipment as $key => $value){
                    $function($value);
                }
            }

            function get_json($url){
                $json = json_decode(file_get_contents($url), true);
                return $json;
            }

            function get_bullet_points($json){
                $bullet_points_path = 'Page.product-summary.ProductOverview';
                $bullet_points_path_array = explode('.', $bullet_points_path);
                $bullet_points = $json[$bullet_points_path_array[0]][$bullet_points_path_array[1]][$bullet_points_path_array[2]];
                return $bullet_points;
            }

            function equipment_getUrl($e){
                //TODO: Query SQL database for URL
                //for now, just return a sample URL
                $urls = [
                    '9600' => 'https://www.deere.com/en/hay-forage/harvesting/self-propelled-forage-harvesters/9600-forage-harvester/index.json',
                ];
                if (array_key_exists($e, $urls)){
                    $url = $urls[$e];
                    return $url;
                } else {
                    return 'https://example.com/'.$e.'.json';
                }
                return $url;
            }

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
            });

        ?>
    </body>
</html>