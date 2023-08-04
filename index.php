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
                - Populate equipment list from SQL database (very slow!)
                - Get equip_link value from SQL database for each selected equipment on form submission
    -->
        <?php
        session_start();
        //get ENV values
        //Currently, for testing, R and S Database is hardcoded
        //TODO: Make this dynamic
        $_SESSION['urls'] = [];

        $env = parse_ini_file('.env');
        $hostname = $env["HOSTNAME"];
        $username = $env["USERNAME"];
        $password = $env["PASSWORD"];
        $database = $env["DATABASE"];
        $table = $env["TABLE"];
        $charset = $env["CHARSET"];
        $port = $env["PORT"];
        $production = $env["PRODUCTION"];

        $_SESSION['$bullet_points_path'] = $env["BULLET_POINTS_PATH"];

        $num_rows = 0;

        //These are set by John Deere- by storing them in an ENV, they be changed if John Deere updates their API
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

        $qu = 'SELECT * FROM '.$database.'.'.$table;
            //Logging (remove for production)
            echo '<p class="success">Query: '.$qu.'</p>';
            //End logging

        if (!isset($_SESSION['result']) || $_SESSION['result'] === []){
            $result = $db->query($qu, MYSQLI_USE_RESULT);
            $_SESSION['result'] = $result;
            if ($result){
                while ($row = $result->fetch_assoc()){
                    array_push($equipment, $row[$title_column]);
                    $urls[$row[$title_column]] = $row[$url_column];
                }
                $num_rows = $result->num_rows;
                $result->close();
                $db->next_result();
            } else {
                echo '<p class="error">Database query error</p>';
            }
        } else {
            $result = $_SESSION['result'];
            echo '<p class="success">Query returned: '.$num_rows.' rows</p>';
        }
        
        // Logging (remove for production)
        if ($equipment === []){
            echo '<p class="error">Query result is empty; query refused</p>';
            if ($db){
                echo '<p class="error">Database connection rate-limited</p>';
            }
            else {
                echo '<p class="error">Query error</p>';
            }
        } else {
            echo '<p class="success">Equipment list populated</p>';
        }
        // End logging

        $_SESSION['urls'] = $urls;

        //end connection
        mysqli_close($db);
        ?>

        <form action="submit.php" method="post">
        <input type="submit" value="Submit"><br/>
            <label for="equipment">Equipment:</label>
            <select name="equipment[]" id="equipment" multiple size=50>
                <?php
                foreach ($equipment as $key => $value){
                    echo '<option value="'.$value.'">'.$value.'</option>';
                }
                ?>
            </select>
            <input type="submit" value="Submit">
    </body>
</html>