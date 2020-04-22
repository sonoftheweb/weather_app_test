This project is ready to use, find all you need for the frontend in the "public/ressource" folder, and built your API respecting the question definition.

Basic controllers and models for this questions have been pre-generated.

If you encountered a permission error, just run sudo chmod -R 777. on the root folder of your project (projects/challenge)

You will be evaluated on the quality of your code, the result, the logic you implemented , and the functionalities you successfully implemented.

At first launch, the project will intialized to install the Laravel/Js environnement, it will take 3-5 minutes. 
When finish a file named "install.ready", will appear on the left menu at the root of the directory. 
Once you see this file you will be able to launch your app by clicking the button "Run"->Run on the topbar.

If you don't use any code migration to create the schema of your database, provide us a DUMP of the mysql database to be sure we can replicate your schema. Only your code (files in the challenge directory not included into the .gitignore files) is saved in this environnement, mysql data is wiped out after the test. 

If you are retrying this question because you had some extra time allowed, you must delete the install.ready file or/and the install.lock file at the root of the project.
Then you can click on the "Run"->Install button on the topbar and wait for the process to finish. This will rebuild the Laravel environnement and setup a server.

You have root (sudo) access to this environnement.

# SETUP

Install all dependencies

`composer install && npm install`

Make sure you have the DB already setup in .env. Run the script in `/database/run_this_in_mysql.sql`. This 
sets up the mysql function to calculate the distance between two coordinates. You may use MySQL workbench for this. 

Then run migrations (and seeder if you like)

`php artisan migrate` or `php artisan migrate --seed`

Build the assets for frontend

`npm run dev`

Finally, run `php artisan serve` to serve up the application. You may visit the link provided in console after that to review
the application in the frontend.
