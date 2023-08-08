<html>
    <head>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <!--
        -- Info --
            - Author: Joseph Hansen
            - License: GNU GPL v3.0
            - Contact: joseph.h@bealscunningham.com

        -- Access -- 
            Developed as propietary software for Beals Cunningham Strategic Services. Do not distribute.

        -- Function --
            This script is designed to do the following:
            - Populate the form with a list of John Deere equipment from(?)
            - Give a user a way to submit a list of John Deere equipment by name through a form
                - This could be a text area, a multi-select, a dropdown-to-text, or a two-column-jumper.
                - For now, I'm leaning towards multi-select.
            - Look up and identify the URL for each piece of equipment in the SQL database
            - Query the John Deere API for the equipment (getting the URL from the SQL database), looping through if needed
            - Parse the resulting JSON, looking for: 
                - "bullet points"
                - "features"
                - "specs"
                - "accessories"
            - Update the SQL database with the results

        -- Progress --
            - August 3rd
                - Initialize repository and roadmap
                - Using an hard-coded sample URL, parse the JSON (bullet points)
                - Get sample equipment selection and "fetch URLs"
            - August 4th
                - Populate equipment list from SQL database (very slow! 90-120s, usually. If needed, a loader visualizer could be added, but it would require AJAX)
                - Get equip_link value from SQL database for each selected equipment on form submission
                - Get bullet points for selected equipment and display it client-side
            - August 7th
                - Update bullet_points column in SQL database with bullet points from John Deere API
            - August 8th
                - Meeting with project manager to discuss next steps and add them to organization [Beals Cunningham](https://github.com/Beals-Cunningham)
                - Reduce initial SQL query, reducing load time to ~2s (from ~120s) (a 60x improvement)
    -->
        <?php
        session_start();
        //get ENV values
        //Currently, for testing, R and S Database is hardcoded
        //TODO: Make this dynamic
        $_SESSION['urls'] = [];

        $env = parse_ini_file('.env');
        $hostname = 'p:'.$env["HOSTNAME"];
        $_SESSION['hostname'] = $hostname;
        $username = $env["USERNAME"];
        $_SESSION['username'] = $username;
        $password = $env["PASSWORD"];
        $_SESSION['password'] = $password;
        $database = $env["DATABASE"];
        $table = $env["TABLE"];
        $_SESSION['table'] = $table;
        $_SESSION['database'] = $database;
        $charset = $env["CHARSET"];
        $_SESSION['charset'] = $charset;
        $port = $env["PORT"];
        $_SESSION['port'] = $port;
        $production = $env["PRODUCTION"];
        $_SESSION['production'] = $production;

        $_SESSION['$bullet_points_path'] = $env["BULLET_POINTS_PATH"];
        $_SESSION['$features_path'] = $env["FEATURES_PATH"];
        $_SESSION['$specs_path'] = $env["SPECIFICATIONS_PATH"];
        $_SESSION['$accessories_path'] = $env["ACCESSORIES_PATH"];

        $num_rows = 0;

        //These are set by John Deere- by storing them in an ENV, they can be changed if John Deere updates their API
        $url_column = $env["URL_COLUMN"];
        $title_column = $env["TITLE_COLUMN"];

        //Logging (remove for production)
        echo ( !$production ? '<span class = "success">Using development environment</span>' : '<span class = "error">Using production environment- if you\'re seeing this, you\'ve done something very wrong</span>');

        if ($hostname){
            echo '<p class="success">Successfully read .ENV values</p>';
            echo '<p class="success">Host: '.$hostname.':'.$port.'</p>';
            echo '<p class="success">Database: '.$database.'</p>';
        } else {
            echo '<p class="error">Failed to read "HOSTNAME" from .ENV: Hostname not set</p>';
        }

        echo '<p class = "success">'.'Client: '.$_SERVER['REMOTE_ADDR'].'</p>';
        echo '<p class = "success">'.'Protocol: '.$_SERVER['SERVER_PROTOCOL'].'</p>';
        //End logging


        //connect to database
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $db = mysqli_connect(
            $hostname,
            $username,
            $password,
            $database,
            $port,
        );
        $db->set_charset($charset);

        //Logging (remove for production)
        if ($db->connect_error){
            echo '<p class="error">Database connection error</p>';
        } else {
            echo '<p class="success">Database connection successful</p>';
        }
        //End logging

        //populate form from database
        $equipment = [];
        $urls = [];

        $qu = 'SELECT '.$title_column.', '.$url_column.' FROM '.$database.'.'.$table;
        //Logging (remove for production)
        echo '<p class="success">Query: '.$qu.'</p>';
        //End logging

        $result = $db->query($qu, MYSQLI_USE_RESULT);

        if ($result){
            while ($row = $result->fetch_assoc()){
                array_push($equipment, $row[$title_column]);
                $urls[$row[$title_column]] = $row[$url_column];
            }
            $num_rows = $result->num_rows;

        } else {
            echo '<p class="error">Database query error</p>';
        }
        
        
        // Logging (remove for production)
        if ($equipment === []){
            echo '<p class="error">Query result is empty; query refused</p>';
            if ($db){
                echo '<p class="error">Database connection presumably rate-limited</p>';
            }
            else {
                echo '<p class="error">Query error</p>';
            }
        } else {
            echo '<p class="success">Equipment list populated</p>';
        }
        // End logging

        $_SESSION['urls'] = $urls;
        ?>

        <form action="submit.php" method="post">
        <input type="submit" value="Submit"><br/>
            <label for="equipment">Equipment:</label>
            <select name="equipment[]" id="equipment" multiple size=<?php if ($production){echo 60;} else {echo 40;}?>>
                <?php
                foreach ($equipment as $key => $value){
                    echo '<option value="'.$value.'">'.$value.'</option>';
                }
                ?>
            </select>
            <input type="submit" value="Submit">
    </body>
</html>