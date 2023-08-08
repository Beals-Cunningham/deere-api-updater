# deere-api-updater
A script to update the SQL database with John Deere equipment information from the John Deere API.
Find additional info in `index.php` comments

## Preview
* Download with `git clone` or download the zip file.
* Run with `php -S localhost:9000` and navigate to `localhost:9000`
* Add an .env file to the directory - get from @josephhansen-bcss , it's not safe to upload (contains password in plain text)
    - USERNAME
    - PASSWORD
    - DATABASE
    - TABLE
    - CHARSET
    - PORT
    - HOSTNAME
    - PRODUCTION
    - TITLE_COLUMN
    - URL_COLUMN
    - BULLET_POINTS_PATH

## Status
Currently, this script:
* Connects to the database (currently hard-coded through environment variables to the R and S database)
* Pulls the list of equipment from the John Deere API and populates a (searchable) multi-select form
* Processes selected equipment, updating the database

## Blockers

## Limitations
* Selecting more than 1000 items gives what *looks* like a fatal error while the submission is processing; it's just a warning that doesn't actually affect anything, but end users should be aware of this limitation 
