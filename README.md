# Observe teaching and learning practices

Development has moved to https://codeberg.org/jeppebundsgaard/observe. Github is no longer safe in the hands of Microsoft.

Observe allows researchers and practitioners to systematically observe teaching and learning practices.

Go to [observe.education](https://observe.education) to use the system, or download your own version here. 

## Install

Observe has been tested with PHP7.4 and PHP8.1, MySQL8.0, and Apache 2.4. 

1. Pull the code from github. 

2. Create a database and a user. Create the file .htdatabase in the settings-folder. Write one line with the credentials of the database user on this form: 

   - ```
     localhost,observe,PASSWORD,observe 
     ```

3. Use phpmyadmin or similar to import the database structure, an admin organization, and a admin user from the file `observe.sql`.

4. Log in using the user `observe`, password `zCESSSH`. 

5. Change password for the observe user.

   

   



## Contribute

Translate the sysem into your language using [poedit](https://poedit.net/). Share your translation by comitting to github (or send it to me).

## Help
Don't hessitate to reach out to [Jeppe Bundsgaard](mailto:jebu@edu.au.dk) for help or introduction to the system.
