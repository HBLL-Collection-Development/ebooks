# eBook Usage Database #

This is a database that can ingest [Project COUNTER][] Book Report #1 and Book Report #2 plain text CSV reports and allow you to search for and browse usage data for all your eBooks.

## Installing ##

1. Download [source files][].
2. Unzip files to desired location on your server.
3. Create a MySQL database called `book_usage` and give a user access to this new database.
4. Run the SQL commands found [here](documentation/schema.sql) to create tables and views needed for this application.
5. If you are part of an organization with a formal relationship with OCLC, register for the [XISBN service][] and get a [WorldCat Affiliate ID].
5. Change name of `config_example.php` to `config.php`.
6. Enter your database credentials in `config.php`.
7. Enter your [WorldCat Affiliate ID][], if you have one, in `config.php`. Enter `FALSE` otherwise.
7. Set up a cron job to regularly run the following command (every other minute is the suggested default):
        ```php -q /path/to/process.php >/dev/null 2>&1```
8. Adjust the [Twig templates][] in the [templates directory](templates) to your liking.

## Using ##

Reports must be plain text CSV files. The only supported reports are COUNTER Book Report #1 ([r1] and [r4]) and COUNTER Book Report #2 ([r1] and [r4]).

The CSV files must be in the following format with columns in the following order:

### Book Report #1 ###

#### Revision 1 ####

1. `Title`
2. `Publisher`
3. `Platform`
4. `ISBN`
5. `ISSN`
6. `Annual Usage`
7. `Usage Year`
8. `Vendor Name`

#### Revision 4 ####

1. `Title`
2. `Publisher`
3. `Platform`
4. `DOI`
5. `Proprietary Identifier`
6. `ISBN`
7. `ISSN`
8. `Annual Usage`
9. `Usage Year`
10. `Vendor Name`

### Book Report #2 ###

#### Revision 1 ####

1. `Title`
2. `Publisher`
3. `Platform`
4. `ISBN`
5. `ISSN`
6. `Annual Usage`
7. `Usage Year`
8. `Vendor Name`

#### Revision 4 ####

1. `Title`
2. `Publisher`
3. `Platform`
4. `DOI`
5. `Proprietary Identifier`
6. `ISBN`
7. `ISSN`
8. `Annual Usage`
9. `Usage Year`
10. `Vendor Name`

There should be no header row(s)—just the data.

## Adjusting Templates ##

[Twig](https://github.com/fabpot/Twig) is a PHP templating system with extensive [documentation](http://twig.sensiolabs.org/documentation). The eBook Usage Database comes with two templates:

1. `default`: A default template that is based on the [Flatly Bootswatch theme](http://bootswatch.com/flatly/).
2. `byu`: A theme made to look like BYU’s library pages. Most would be better off ignoring this theme and modding the `default` theme.

Templates are found in the templates directory. Templates that begin with an underscore (_) are child templates that are called by parent templates. You must have a CSS file in the templates directory called `styles.css`. Otherwise, you are free to change the templates any way you see fit for your needs.

## Known Issues ##

1. This program assumes that an individual book is only listed once per platform. If you have usage data where a book is listed multiple times per platform (as can happen with book series such as those found in BioOne), you will need to manually combine usage for that book into a single entry for that platform. Otherwise, the database will only show uses for one of the issues instead of total usage for the entire series.
2. Some vendors will show usage for all eBooks that you have access to. However, most only show usage for titles that received use. This means that the database does not show your eBook holdings. It just shows usage for the subset of titles that have received usage.
3. Because the database does not show your holdings, the database cannot show you what titles are still active or not. Because of this, when you browse by platform or vendor, it will only show you titles that have received usage in the past two years on the assumption that most of those will still be active titles.

[Project COUNTER]: http://www.projectcounter.org/
[source files]: https://github.com/jaredhowland/Ebook-Usage-Database/archive/master.zip
[Twig templates]: http://twig.sensiolabs.org/
[r1]: http://www.projectcounter.org/cop_books_ref.html
[r4]: http://www.projectcounter.org/code_practice.html
[XISBN service]: http://xisbn.worldcat.org/xisbnadmin/doc/api.htm
[WorldCat Affiliate ID]: http://www.worldcat.org/wcpa/do/AffiliateUserServices?method=initSelfRegister