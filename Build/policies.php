<?php require('header.php'); ?>

                <?php

                    // If the add network form has been submitted, enter the data.
                    if($_POST['add-policy']) {
                        if(!(strlen($_POST["Name"]) >= 1 && strlen($_POST["Name"]) <= 64)) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Name to be 1-64 characters.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } elseif(!preg_match("/^[a-zA-Z0-9 \-\s]+$/", $_POST["Name"])) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Name to only contain 0-9, a-z, A-Z and hyphens.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } else {
                            $stmt = $pdo->prepare("INSERT INTO Policies (Name) VALUES (:Name)");
                            $stmt->bindParam(':Name', $_POST["Name"]);
                            $stmt->execute();
                        }
                    }

                    // Delete policy logic
                    if(is_array($_POST['checkbox'])){
                        if($_POST['delete']) {
                            $stmt = $pdo->prepare("DELETE FROM Policies WHERE ID = ?");
                            $i = 0;
                            foreach($_POST["checkbox"] as $ID) {
                                if(isset($ID)) {
                                    $stmt->execute([$ID]);
                                    $i++;
                                }
                            }
                        }
                    }

                    // If the add policy entry form has been submitted, enter the data.
                    if($_POST['add-policy-entry']) {
                        // If the user specifies all ports for src/dst, set ports to 1-65535.
                        if($_POST['SrcPortsAll'] == "on") {
                            $_POST['SrcPorts'] = "1-65535";
                        }
                        if($_POST['DstPortsAll'] == "on") {
                            $_POST['DstPorts'] = "1-65535";
                        }
                        // Parse port range into an array
                        $SrcPortsRange = $core->getValidPortRange($_POST["SrcPorts"]);
                        $DstPortsRange = $core->getValidPortRange($_POST["DstPorts"]);
                        // Input validation
                        if(!(strlen($_POST["Comment"]) >= 1 && strlen($_POST["Comment"]) <= 64)) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Comment to be 64 characters or less in length.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } elseif(!preg_match("/^[a-zA-Z0-9 \-\s]+$/", $_POST["Comment"])) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Comment to only contain 0-9, a-z, A-Z and hyphens.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } elseif(!$core->isValidInt($_POST["Policy"])) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Policy to be an existing Policy.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } elseif (!$core->isValidInt($_POST["SrcNetwork"])) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Source to be an existing Network or Endpoint.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } elseif (!$core->isValidInt($_POST["DstNetwork"])) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Destination to be an existing Network or Endpoint.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } elseif (!$core->isValidInt($_POST["InternetProtocol"])) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Protocol to be an existing Protocol.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } elseif (!$SrcPortsRange) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Source ports to be All, between 1-65535, or a range e.g. 1812-1813.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } elseif (!$DstPortsRange) {
                            ?>
                                <div class="content-wrap">
                                    <p>Sorry, something went wrong.</p>
                                    <p>We were expecting your Destination ports to be All, between 1-65535, or a range e.g. 1812-1813.</p>
                                </div><!-- /.content-wrap -->
                            <?
                        } else {
                            try {
                                $pdo->beginTransaction();
                                // Insert the source ports
                                $stmt = $pdo->prepare("INSERT INTO Ports (Start,End) VALUES (:Start,:End)");
                                $stmt->bindParam(':Start', $SrcPortsRange[0]);
                                $stmt->bindParam(':End', $SrcPortsRange[1]);
                                $stmt->execute();
                                $SrcPortsID = $pdo->lastInsertId();
                                // Insert the destination ports
                                $stmt = $pdo->prepare("INSERT INTO Ports (Start,End) VALUES (:Start,:End)");
                                $stmt->bindParam(':Start', $DstPortsRange[0]);
                                $stmt->bindParam(':End', $DstPortsRange[1]);
                                $stmt->execute();
                                $DstPortsID = $pdo->lastInsertId();
                                // Insert the policy information with relations with the source and destination network information
                                $stmt = $pdo->prepare("INSERT INTO PolicyEntries (Comment,SrcNetwork,DstNetwork,InternetProtocol,SrcPorts,DstPorts,Action) VALUES (:Comment,:SrcNetworkID,:DstNetworkID,:InternetProtocol,:SrcPorts,:DstPorts,:Action)");
                                $stmt->bindParam(':Comment', $_POST["Comment"]);
                                $stmt->bindParam(':SrcNetworkID', $_POST['SrcNetwork'], PDO::PARAM_INT);
                                $stmt->bindParam(':DstNetworkID', $_POST['DstNetwork'], PDO::PARAM_INT);
                                $stmt->bindParam(':InternetProtocol', $_POST["InternetProtocol"], PDO::PARAM_INT);
                                $stmt->bindParam(':SrcPorts', $SrcPortsID, PDO::PARAM_INT);
                                $stmt->bindParam(':DstPorts', $DstPortsID, PDO::PARAM_INT);
                                $stmt->bindParam(':Action', $_POST["Action"], PDO::PARAM_INT);
                                $stmt->execute();
                                $PolicyEntryID = $pdo->lastInsertId();
                                // Insert link between the specified policy and new policy entry
                                $stmt = $pdo->prepare("INSERT INTO `Policies-PolicyEntries` (PolicyEntryID,PolicyID) VALUES (:PolicyEntryID,:PolicyID)");
                                $stmt->bindParam(':PolicyEntryID', $PolicyEntryID);
                                $stmt->bindParam(':PolicyID', $_POST["Policy"]);
                                $stmt->execute();
                                // Commit
                                $pdo->commit();
                            } catch (Exception $e) {
                                // Something went wrong with the try block, rollback.
                                $pdo->rollback();
                                echo "The information failed to be inserted!  Here is the PDO exception:<br>";
                                echo $e->getMessage();
                            }
                        }
                    }

                ?>

                <section id="nav-sub">
                    
                    <ul>
                        <li class="current-page-item"><a href="#">All Policies</a></li>
                    </ul>
                    
                </section><!-- /#nav-sub -->

                <form action="policies.php" method="POST">

                    <section id="context-buttons">
                        <button type="submit" class="button-delete" name="delete" value="delete">Delete</button>
                        <button type="button" class="button-test overlay-trigger-test-policy show-on-single-checkbox" name="test" value="test">Test</button>
                        <button type="button" class="button-export overlay-trigger-export-policy show-on-single-checkbox" name="export" value="export">Export</button>
                    </section>

                    <div class="content">

                        <?php

                            // Get all policies
                            $stmt = $pdo->query('SELECT ID,Name FROM Policies');
                            // If there are records, display a table with the results
                            if($stmt->rowCount() > 0) {

                                ?>

                                    <table class="t-l">
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                        </tr>

                                <?

                                    while ($row = $stmt->fetch())
                                    {
                                        echo '<tr>';
                                        echo '<td class="small"><input class="clone-value-src" type="checkbox" name="checkbox[]" value="' . $row['ID'] . '">';
                                        echo '<td><a href="policy-entries.php?ChosenPolicy=' . $row['ID'] . '">' . $row['Name'] . "</a></td>";
                                        echo '</tr>';
                                    }

                                ?>

                                    </table>

                                <?

                            // There are no policies
                            } else {

                                ?>

                                    <div class="content-wrap">

                                        <h2>There aren't any Policies yet.</h2>
                                        <p>Maybe you would like to <a class="overlay-trigger-add-policy" href="javascript:void(0)">Add a Policy</a>?</p>

                                    </div><!-- /.content-wrap -->

                                <?

                            }

                        ?>

                    </div><!-- /.content -->
                    
                </form>

                <?php require('footer.php'); ?>