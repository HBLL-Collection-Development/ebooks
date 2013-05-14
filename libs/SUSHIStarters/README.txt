CONTENTS OF THIS FILE ---------------------
* Requirements and notes 
* Installation 
* SUSHIStarters Extensions  
* More information 

REQUIREMENTS AND NOTES ----------------------
 
SUSHIStarters requires:
* A web server. Apache (version 2.0 or greater) is recommended. 
* PHP 5.1.6 (or greater) (http://www.php.net/). 
* Access to the internet to harvest information

Security issues
* SUSHIStarters stores and exposes sensitive/confidential information, including: Requestor IDs, Customer IDs, usernames, passwords, usage statistics. 
* It is VERY IMPORTANT that you secure the application from prying eyes!
    * Best option: install SUSHIStarters on a server in your internal network and give it internet access via a firewall
    * Or, if you have to: install SUSHIStarters on a public access server and secure it using HTTP Basic Authentication, SSL, Shibboleth etc. (Consult your IT security experts!)
 
INSTALLATION ------------
 
1. Download and extract the SUSHIStarters distribution file
 
You can obtain the latest SUSHIStarters release from http://?????????? -- the files are available in .tar.gz and .zip formats and can be extracted using most compression tools.
 
Extracting the distribution file will create a new directory SUSHIStarters-x.y.z/ containing three main directories:

    * SSIncludes
    * SSwebclient
    * SushiStarters

2. Put the three SUSHIStarters directories and contents into required locations

    * Copy or move the SSIncludes directory and contents into your PHP includes directory (e.g. /usr/local/php/lib/php or /usr/share/pear)
    * Copy or move the SSwebclient directory and contents into a web accessible directory (e.g. /var/www/html)
    * (Optionally) Copy or move the SushiStarters directory and contents into a location you find comfortable or convenient (e.g. /usr/local/SushiStarters)

3. Set directory ownership and permissions

The web server needs write permissions on two directories and their contents, namely:

    * SushiStarters/params/
    * SushiStarters/filestore/
 
4. Configure SUSHIStarters 

You just need to set two variables to configure SUSHIStarters!

Using your preferred text editor, open up helpers.inc.php which is in the SSIncludes directory, and set:

    * $SSRoot  : the URL of the webclient
    * $SSPath  : Path to SUSHIStarters directory containing the filestore, params and CLI scripts

5. Verify that the client is working.

Open up the web client in your favorite browser!
 
SUSHIStarters Extensions ----------------------------------
 
To do...
 
MORE INFORMATION ----------------

To do... 