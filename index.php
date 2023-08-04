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
        $port = $env["PORT"];
        $production = $env["PRODUCTION"];

        echo ( !$production ? '<span class = "success">Using development environment</span>' : '<span class = "error">Using production environment- if you\'re seeing this, you\'ve done something very wrong</span>');

        if ($hostname){
            echo '<p class="success">Successfully read .ENV values<br/>Host: '.$hostname.':'.$port.'</p>';
        } else {
            echo '<p class="error">Failed to read "HOSTNAME" from .ENV: Hostname not set</p>';
        }

        echo '<p class = "success">'.'Client: '.$_SERVER['REMOTE_ADDR'].'</p>';

        //connect to database
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $db = mysqli_connect(
            $hostname,
            $username,
            $password,
            $database,
            $port,
        );
        $db->set_charset("utf8mb4");

        if ($db->connect_error){
            echo '<p class="error">Database connection error</p>';
        } else {
            echo '<p class="success">Database connection successful</p>';
        }

        //populate form from database
        $equipment = [];
        $urls = [];

        if (!isset($_SESSION['result']) || $_SESSION['result'] === []){
            $result = $db->query("SELECT * FROM randsdatabase.deere_equipment", MYSQLI_USE_RESULT);
            $_SESSION['result'] = $result;
            if ($result){
                while ($row = $result->fetch_assoc()){
                    array_push($equipment, $row['title']);
                    $urls[$row['title']] = $row['equip_link'];
                }
                $result->close();
                $db->next_result();
            } else {
                echo '<p class="error">Database query error</p>';
            }
        } else {
            $result = $_SESSION['result'];
        }
        
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