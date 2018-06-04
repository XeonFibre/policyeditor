<?php require('header.php'); ?>

                <?php

                    // If the add network form has been submitted, enter the data.
                    if($_POST['add-endpoint']) {
                        if(!(strlen($_POST["Name"]) >= 1 && strlen($_POST["Name"]) <= 64)) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Name to be 64 characters or less in length.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } elseif(!preg_match("/^[a-zA-Z0-9 \-\s]+$/", $_POST["Name"])) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Name to only contain 0-9, a-z, A-Z and hyphens.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } elseif(!$core->isValidIP($_POST["Network"])) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Network to be a network IP address.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } else {
                            $endpointCIDRIdentifier = '32';
                            $stmt = $pdo->prepare("INSERT INTO NetworksEndpoints (Name,Network,CIDRMask) VALUES (:Name,INET6_ATON(:Network),:CIDRMask)");
                            $stmt->bindParam(':Name', $_POST["Name"]);
                            $stmt->bindParam(':Network', $_POST["Network"]);
                            $stmt->bindParam(':CIDRMask', $endpointCIDRIdentifier, PDO::PARAM_INT);
                            $stmt->execute();
                        }
                    }

                    // Delete endpoint logic
                    if(is_array($_POST['checkbox'])){
                        if($_POST['delete']) {
                            $stmt = $pdo->prepare("DELETE FROM NetworksEndpoints WHERE ID = ?");
                            $i = 0;
                            foreach($_POST["checkbox"] as $ID) {
                                if(isset($ID)) {
                                    $stmt->execute([$ID]);
                                    $i++;
                                }
                            }
                        }
                    }

                ?>

                <section id="nav-sub">
                    
                    <ul>
                        <li class="current-page-item"><a href="#">All Endpoints</a></li>
                    </ul>
                    
                </section><!-- /#nav-sub -->

                <form action="endpoints.php" method="POST">

                    <section id="context-buttons">
                        <button type="submit" class="button-delete" name="delete" value="delete">Delete</button>
                    </section>

                    <div class="content">                  

                            <?php

                                // Get all endpoints
                                $stmt = $pdo->query('SELECT ID,Name,INET6_NTOA(Network) AS Network,CIDRMask
                                                    FROM NetworksEndpoints
                                                    WHERE CIDRMask IS NULL OR CIDRMask = "32"');
                                // If there are records, display a table with the results
                                if($stmt->rowCount() > 0) {

                                    ?>

                                        <table class="t-l">

                                            <tr>
                                                <th></th>
                                                <th>Name</th>
                                                <th>IP Address</th>
                                            </tr>

                                    <?

                                        while ($row = $stmt->fetch())
                                        {
                                            echo '<tr>';
                                            echo '<td class="small"><input type="checkbox" name="checkbox[]" value="' . $row['ID'] . '">';
                                            echo '<td>' . $row['Name'] . "</td>";
                                            echo '<td>' . $row['Network'] . "</td>";
                                            echo '</tr>';
                                        }

                                        ?>

                                        </table>

                                    <?

                                // There are no endpoints
                                } else {

                                    ?>

                                        <div class="content-wrap">

                                            <h2>There aren't any Endpoints yet.</h2>
                                            <p>Maybe you would like to <a class="overlay-trigger-add-endpoint" href="javascript:void(0)">Add an Endpoint</a>?</p>

                                        </div><!-- /.content-wrap -->

                                    <?

                                }

                            ?>

                    </div><!-- /.content -->
                    
                </form>

                <?php require('footer.php'); ?>