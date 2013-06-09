# eBook Usage Database #

This is a database that can ingest [Project COUNTER][] Book Report #1 and Book Report #2 plain text CSV reports and allow you to search for and browse usage data for all your eBooks.

## Get Started ##

1. Download [source files][].
2. Untar files to desired location on your server.
3. Create a MySQL database called `book_usage` and give desired user access to this new database.
4. Run the SQL commands found [here](sql.md) to create tables and views needed for this application.
5. Change name of config_example.php to config.php
6. Enter your database credentials in the config.php file
7. Set up a cron job on your server to the following command at desired interval (every other minute or so is usually a good start):
        php -q /path/to/process.php >/dev/null 2>&1
8. Adjust [Twig templates][] in the templates directory for desired HTML output.

[Project COUNTER]: http://www.projectcounter.org/
[source files]: https://github.com/jaredhowland/Ebook-Usage-Database/archive/v1.0.tar.gz
[Twig templates]: http://twig.sensiolabs.org/