<?php require('header.php'); ?>

                <?php

                    // If the add network form has been submitted, enter the data.
                    if($_POST['add-network']) {
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
                        } elseif(!($_POST["CIDRMask"] >= 1 && $_POST["CIDRMask"] <= 128)) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your CIDR Mask to be a number between 1-128.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } else {
                            $stmt = $pdo->prepare("INSERT INTO NetworksEndpoints (Name,Network,CIDRMask) VALUES (:Name,INET6_ATON(:Network),:CIDRMask)");
                            $stmt->bindParam(':Name', $_POST["Name"]);
                            $stmt->bindParam(':Network', $_POST["Network"]);
                            $stmt->bindParam(':CIDRMask', $_POST["CIDRMask"]);
                            $stmt->execute();
                        }
                    }

                    // Delete policy logic
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
                        <li class="current-page-item"><a href="#">All Networks</a></li>
                    </ul>
                    
                </section><!-- /#nav-sub -->

                <form action="networks.php" method="POST">

                    <section id="context-buttons">
                        <button type="submit" class="button-delete" name="delete" value="delete">Delete</button>
                    </section>

                    <div class="content">

                            <?php

                                // Get all networks
                                $stmt = $pdo->query('SELECT ID,Name,INET6_NTOA(Network) AS Network,CIDRMask
                                                    FROM NetworksEndpoints
                                                    WHERE CIDRMask IS NOT NULL AND NOT CIDRMask = "32" AND NOT CIDRMask = "0"');
                                // If there are records, display a table with the results
                                if($stmt->rowCount() > 0) {

                                    ?>

                                        <table class="t-l">

                                            <tr>
                                                <th></th>
                                                <th>Name</th>
                                                <th>Network</th>
                                                <th>CIDR Prefix</th>
                                                <th>Subnet Mask</th>
                                            </tr>

                                    <?

                                        while ($row = $stmt->fetch())
                                        {
                                            echo '<tr>';
                                            echo '<td class="small"><input type="checkbox" name="checkbox[]" value="' . $row['ID'] . '">';
                                            echo '<td>' . $row['Name'] . "</td>";
                                            echo '<td>' . $row['Network'] . "</td>";
                                            echo '<td>' . $row['CIDRMask'] . "</td>";
                                            echo '<td>' . $core->convertCIDRToSubnetMask($row['CIDRMask']) . "</td>";
                                            echo '</tr>';
                                        }

                                    ?>

                                        </table>

                                    <?

                                // There are no networks
                                } else {

                                    ?>

                                        <div class="content-wrap">

                                            <h2>There aren't any Networks yet.</h2>
                                            <p>Maybe you would like to <a class="overlay-trigger-add-network" href="javascript:void(0)">Add a Network</a>?</p>

                                        </div><!-- /.content-wrap -->

                                    <?

                                }

                            ?>

                    </div><!-- /.content -->
                    
                </form>

                <?php require('footer.php'); ?>