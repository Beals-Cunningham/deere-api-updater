<html>
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
    -->
        <?php echo
        $production = false;
        $bullet_points_path = 'Page.product-summary.ProductOverview';
        $bullet_points_path_array = explode('.', $bullet_points_path);

        if (!$production){
            echo '<p>Production: '.( $production ? 'true' : 'false').'</p>';
            $testing_url = 'https://www.deere.com/en/hay-forage/harvesting/self-propelled-forage-harvesters/9600-forage-harvester/index.json';
            $json = json_decode(file_get_contents($testing_url), true);

            // Bullet points
            $bullet_points = $json[$bullet_points_path_array[0]][$bullet_points_path_array[1]][$bullet_points_path_array[2]];
            echo '<p>'.$bullet_points.'</p>';
        }
        ?>

        <form action="submit.php" method="post">
            <label for="equipment">Equipment:</label>
            <select name="equipment[]" id="equipment" multiple>

                <option value="9600">9600</option>
                <option value="9700">9700</option>
                <option value="9800">9800</option>
                <option value="9900">9900</option>
            </select>
            <input type="submit" value="Submit">
    </body>
</html>