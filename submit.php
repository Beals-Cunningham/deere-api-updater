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

            function equipment_getUrl($e){
                //TODO: Query SQL database for URL
                $url = 'https://example.com/'.$e.'.json';
                return $url;
            }

            $equipment = $_POST['equipment'];
            if (gettype($equipment) === 'array'){
                equipment_foreach($equipment, function($value){
                    echo '<p>'.$value.' '.equipment_getUrl($value).'</p>';
                });
            } else if (gettype($equipment) === 'array'){
                $equipment = array($equipment);
                equipment_foreach($equipment, function($value){
                    echo '<p>'.$value.' '.equipment_getUrl($value).'</p>';
                });
            } else {
                echo '<p>Cannot parse equipment selection.</p>';
            }

        ?>
    </body>
</html>