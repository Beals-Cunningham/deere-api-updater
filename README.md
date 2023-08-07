# deere-api-updater
A script to update the SQL database with John Deere equipment information from the John Deere API.
Find additional info in `index.php` comments

## Preview
* Download with `git clone` or download the zip file.
* Run with `php -S localhost:9000` and navigate to `localhost:9000`
* Add an .env file to the directory - get from https://github.com/josephhansen-bcss , as it's not safe to upload (contains password in plain text)
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
![screenshot of the form, with searching](https://i.postimg.cc/bNk5BQQ1/Screenshot-2023-08-07-at-9-39-42-AM.png)
* Processes selected equipment, updating the `bullet_points` column in the database
![screenshot of updated results](https://i.postimg.cc/8c0TXLW2/Screenshot-2023-08-07-at-9-41-29-AM.png)

## Blockers
* ### Requested columns other than `bullet_points` can't be updated, because the API doesn't return the information they use.
    - Until the data source for these columns can be identified, no further significant progress can be made. See Issue https://github.com/Beals-Cunningham/deere-api-updater/issues/2 for more information.

