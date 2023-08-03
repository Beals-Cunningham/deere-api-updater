<html>
    <body>
        <!--
        -- Info --
            - Author: Joseph Hansen
            - License: GNU GPL v3.0

        -- Access -- 
            Developed as propietary software for Beals Cunningham Strategic Services. Share only with BCSS employees.

        -- Function --
            This script is designed to do the following:
            - Give a user a way to submit a list of John Deere equipment by name through a form
            - Query the John Deere API for the equipment (from a SQL database), looping through if needed
            - Parse the resulting JSON, looking for: 
                - "bullet points"
                - "features"
                - "specs"
                - "accessories"
            - Update the SQL database with the results

        -- Progress --
            - August 3rd
                - Initialize repository and roadmap
                - Using an hard-coded sample URL, parse the JSON
    -->
        <?php echo
        $production = false;

        if (!$production){
            // Testing
            echo '<p>Production: '.( $production ? 'true' : 'false').'</p>';
            $testing_url = 'https://www.deere.com/en/hay-forage/harvesting/self-propelled-forage-harvesters/9600-forage-harvester/index.json';
            $json = json_decode(file_get_contents($testing_url), true);
            var_dump($json);
            // Get JSON from the testing URL, and var_dump it
        }
        ?>
    </body>
</html>