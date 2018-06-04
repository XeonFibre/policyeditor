<?php

    include "utils/ipInRange.php";
    include "utils/core.php";

    $ipInRange = new ipInRange;
    $core = new core;

    // Database details
    $host = 'database_host';
    $db   = 'database';
    $user = 'database_user';
    $pass = 'database_pass';
    $charset = 'utf8';

    // Connect to the database and create a new PDO object ($pdo)
    // PDO ERRMODE should be silent for production use.
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);

?>

<!DOCTYPE html>

<html lang="en">

    <head>
        
        <title>Policy Editor</title>
        
        <link rel="stylesheet" type="text/css" href="css/screen.css">
                
        <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,700" rel="stylesheet">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        
        <script src="js/functions.js"></script>
    
    </head>
    
    <body class="panel-login">

        <section id="main" class="panel">
             
            <section id="panel-r">
                
                <div class="content-wrap">

                    <div id="branding">
                        <h1>Policy Editor</h1>
                        <h2>Company Name</h2>
                    </div><!-- /#branding -->
                    
                    <div class="form">
                        <p class="form-intro">Sign in with your Policy Editor account.</p>
                        <form>
                            <div class="fields">
                                <input type="email" placeholder="Email address" tabindex="1">
                                <input type="password" placeholder="Password" tabindex="2">
                            </div><!-- /.fields -->
                            <input type="submit" value="Sign in" tabindex="3">
                        </form>
                    </div><!-- /.form -->
                    
                    <div class="bottom">
                        <p class="small">Not for production use.</p>
                        <p><a href="#">Privacy Policy</a><a href="#">Changelog</a></p>
                    </div>
                    
                </div><!-- /.content-wrap -->
                
            </section><!-- /#panel-r -->
            
        </section><!-- /#main -->
        
    </body>
    
</html>