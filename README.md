# PHP MVC Core

## Introduction
This project facilitates work when you want to build an MVC pattern in PHP. it contains all the core files. For example, the Application class which is the main class which ties the whole MVC application together, the database connection files found in the 'db' folder, middleswares for access control, exception handling files in the 'exceptions' folder, base classes to be extended by all models and conrollers in the application and most imortantly, the routing class. This PHP MVC Core files are required everytime you build an MVC application in PHP. By simply pulling from this repository, you don't have to rewrite these files over and over.

## Installation
1. Create a folder in your project home for these files
2. Download the files from PHP-MVC-core project into the just created folder.

## Usage
1. Intialize the Application Class in your index file: e.g
   
    $app = new Application($directory, $config);
   
   - $directory is a string, the root path of your project directory
   - $config is an associative array containing your database PDO connection details like, dsn, username, and password e.g
     $config = [
        'userClass' => User::class,
        'db' => [
            "dsn" => "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};",
            "user" => $_ENV['DB_USER'],
            "pass" => $_ENV['DB_PASS']
        ]
    ];
    - 'userClass' is what ever name you call the class that represents a user in your application. Leave it out if you don't have such a class
  
2. Add routes to your application. For example:
   $app->router->get('/', [SiteController::class, 'home']);

   - 'get' is the request method. could be 'post'
   - '/' is the path
   - SiteController is the class to be called and 'home' is the method of that class which will be executed everytime this path is requested by the specified method.

