<?php session_start(); ?>

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
    
    <body>
        
        <section id="overlay-container">
            
            <section id="box">
                
                <section id="progress">
                    
                    <!-- Progress bar links are generated automatically -->
                    <ul></ul>
                    
                </section>
                
                <section id="content">
                    
                    <div class="content-wrap">

                        <h1>Sorry, something went wrong.</h1>
                        <h2>A form should be presented here.  Press your <strong>Esc</strong> key to close this window.</h2>

                    </div><!-- /.content-wrap -->
                
                </section><!-- /#content -->
                
            </section><!-- /#box -->
            
        </section><!-- /#overlay-container -->

        <section id="wrapper">
            
            <section id="sidebar">
                
                <ul>
                    
                    <li class="sidebar-logo"><a href="policies.php"></a></li>
                    
                    <?php
                            
                        // Set the page title based on the file name.
                        if(basename($_SERVER['PHP_SELF']) == "policies.php") {
                            echo '<li class="sidebar-policies current-page-item"><a href="policies.php">Policies</a></li>';
                        } elseif(basename($_SERVER['PHP_SELF']) == "policy-entries.php") {
                            echo '<li class="sidebar-policies current-page-item"><a href="policies.php">Policies</a></li>';
                        } elseif(basename($_SERVER['PHP_SELF']) == "policy-test.php") {
                            echo '<li class="sidebar-policies current-page-item"><a href="policies.php">Policies</a></li>';
                        } else {
                            echo '<li class="sidebar-policies"><a href="policies.php">Policies</a></li>';
                        }
                    
                        if(basename($_SERVER['PHP_SELF']) == "networks.php") {
                            echo '<li class="sidebar-networks current-page-item"><a href="networks.php">Networks</a></li>';
                        } else {
                            echo '<li class="sidebar-networks"><a href="networks.php">Networks</a></li>';
                        }
                        
                        if (basename($_SERVER['PHP_SELF']) == "endpoints.php") {
                            echo '<li class="sidebar-endpoints current-page-item"><a href="endpoints.php">Endpoints</a></li>';
                        } else {
                            echo '<li class="sidebar-endpoints"><a href="endpoints.php">Endpoints</a></li>';
                        }

                    ?>
         
                </ul>
                
            </section>
            
            <section id="main">

                <section id="header">
                    
                    <div class="f-p-left">
                        
                        <?php
                                                
                            // Set the page title based on the file name.
                            if(basename($_SERVER['PHP_SELF']) == "policies.php") {
                                echo "<h1>Policies</h1>";
                            } elseif (basename($_SERVER['PHP_SELF']) == "networks.php") {
                                echo "<h1>Networks</h1>";
                            } elseif (basename($_SERVER['PHP_SELF']) == "endpoints.php") {
                                echo "<h1>Endpoints</h1>";
                            } elseif (isset($_GET['ChosenPolicy'])) {
                                $stmt = $pdo->prepare('SELECT ID,Name FROM Policies WHERE ID = :ChosenPolicy LIMIT 1');
                                $stmt->bindParam(':ChosenPolicy', $_GET['ChosenPolicy']);
                                $stmt->execute();
                                if($stmt->rowCount() == 1) {
                                    while ($row = $stmt->fetch())
                                    {
                                        echo "<h1>Policy Entries - " . $row['Name'] . "</h1>";
                                    }
                                } else {
                                    echo "<h1>Policy Entries</h1>";
                                }
                            } else {
                                echo "<h1>Policy Editor</h1>";
                            }
                        
                        ?>

                    </div>
                    
                    <div class="f-p-right">
                                        
                        <div class="button-dropdown dropdown">
                            <button>Add</button>
                            <div class="dropdown-content">
                                <a class="overlay-trigger-add-policy" href="javascript:void(0)">Add Policy</a>
                                <a class="overlay-trigger-add-policy-entry" href="javascript:void(0)">Add Policy Entry</a>
                                <a class="overlay-trigger-add-network" href="javascript:void(0)">Add Network</a>
                                <a class="overlay-trigger-add-endpoint" href="javascript:void(0)">Add Endpoint</a>
                            </div>
                        </div>

                        <div class="picker-dropdown t-r dropdown">
                            <span>Logged in as</span>
                            <p>Daniel Wiltshire</p>
                            <div class="dropdown-content">
                                <a href="#">Sign out</a>
                            </div>
                        </div>
                        
                    </div>
                    
                </section><!-- /#header -->