<?php require('header.php'); ?>

                <?php

                    if(is_array($_POST['checkbox'])){
                        if($_POST['delete']) {
                            $stmt = $pdo->prepare("DELETE FROM PolicyEntries WHERE ID = ?");
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
                        <li class="current-page-item"><a href="#">All Policy Entries</a></li>
                    </ul>
                    
                </section><!-- /#nav-sub -->

                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                    
                    <section id="context-buttons">
                        <button type="submit" class="button-delete" name="delete" value="delete">Delete</button>
                    </section>

                    <div class="content">
                        
                        <?php
                        
                            if($core->isValidInt($_GET['ChosenPolicy'])) {

                                    // Get all policy entries for $_GET['ChosenPolicy']
                                    $stmt = $pdo->prepare('SELECT PolicyEntries.ID AS ID,
                                        PolicyEntries.Comment,
                                        INET6_NTOA(NetworksEndpoints.Network) SrcNetwork,
                                        NetworksEndpoints.CIDRMask SrcCIDR,
                                        INET6_NTOA(a.Network) DstNetwork,
                                        e.CIDRMask DstCIDR,
                                        InternetProtocols.Name AS InternetProtocolName,
                                        Ports.Start SrcStartPort,
                                        b.Start DstStartPort,
                                        c.End SrcEndPort,
                                        d.End DstEndPort,
                                        PolicyEntries.Action
                                    FROM `Policies-PolicyEntries`,InternetProtocols,PolicyEntries
                                    JOIN NetworksEndpoints
                                    ON PolicyEntries.SrcNetwork = NetworksEndpoints.ID
                                    JOIN NetworksEndpoints a
                                    ON PolicyEntries.DstNetwork = a.ID
                                    JOIN NetworksEndpoints e
                                    ON PolicyEntries.DstNetwork = e.ID
                                    JOIN Ports
                                    ON PolicyEntries.SrcPorts = Ports.ID
                                    JOIN Ports b
                                    ON PolicyEntries.DstPorts = b.ID
                                    JOIN Ports c
                                    ON PolicyEntries.SrcPorts = c.ID
                                    JOIN Ports d
                                    ON PolicyEntries.DstPorts = d.ID
                                    WHERE `Policies-PolicyEntries`.PolicyID = :ChosenPolicy
                                    AND `Policies-PolicyEntries`.PolicyEntryID = PolicyEntries.ID
                                    AND PolicyEntries.InternetProtocol = InternetProtocols.ID');
                                    $stmt->bindParam(':ChosenPolicy', $_GET['ChosenPolicy']);
                                    $stmt->execute();
                                    if($stmt->rowCount() > 0) {
                                        
                                        ?>
                                
                                            <table class="t-l">
                                                <tr>
                                                    <th></th>
                                                    <th>Action</th>
                                                    <th>Comment</th>
                                                    <th>Source</th>
                                                    <th></th>
                                                    <th>Destination</th>    
                                                </tr>
                                                
                                        <?
                                           
                                            // Get the policy entries
                                            while ($row = $stmt->fetch())
                                            {
                                                echo '<tr>';

                                                    echo '<td class="small"><input type="checkbox" name="checkbox[]" value="' . $row['ID'] . '">';
                                                    // If the policy entry is deny
                                                    if($row['Action'] == "0") {
                                                        echo '<td class="small"><img src="img/policyentries-deny.png" alt="Deny Icon"></td>';
                                                    // If the policy entry is permit
                                                    } else {
                                                        echo '<td class="small"><img src="img/policyentries-permit.png" alt="Permit Icon"></td>';
                                                    }

                                                    echo '<td>' . $row['Comment'] . "</td>";

                                                    if($row['SrcCIDR'] == "32" || is_null($row['SrcCIDR'])) {
                                                        echo '<td><img src="img/policyentries-endpoint.png">' . $row['SrcNetwork'] . " (" . $row['SrcStartPort'] . "-" . $row['SrcEndPort'] . "/" . $row['InternetProtocolName'] . ")</td>";
                                                    } else {
                                                        echo '<td><img src="img/policyentries-network.png">' . $row['SrcNetwork'] . '/' . $row['SrcCIDR'] . " (" . $row['SrcStartPort'] . "-" . $row['SrcEndPort'] . "/" . $row['InternetProtocolName'] . ")</td>";
                                                    }

                                                    echo '<td><img src="img/policyentries-arrowright.png"></td>';

                                                    if($row['DstCIDR'] == "32" || is_null($row['DstCIDR'])) {
                                                        echo '<td><img src="img/policyentries-endpoint.png">' . $row['DstNetwork'] . " (" . $row['DstStartPort'] . "-" . $row['DstEndPort'] . "/" . $row['InternetProtocolName'] . ")</td>";
                                                    } else {
                                                        echo '<td><img src="img/policyentries-network.png">' . $row['DstNetwork'] . '/' . $row['DstCIDR'] . " (" . $row['DstStartPort'] . "-" . $row['DstEndPort'] . "/" . $row['InternetProtocolName'] . ")</td>";
                                                    }
                                                echo '</tr>';
                                            }

                                        ?>
                                                
                                                <tr class="implicit">
                                                    <td></td>
                                                    <td class="small"><img src="img/policyentries-deny.png" alt="Deny Icon"></td>
                                                    <td>Implicit deny</td>
                                                    <td><img src="img/policyentries-network.png">0.0.0.0/0 (All Ports)</td>
                                                    <td><img src="img/policyentries-arrowright.png"></td>
                                                    <td><img src="img/policyentries-network.png">0.0.0.0/0 (All Ports)</td>
                                                </tr>

                                            </table>
                        
                                        <?
                                        
                                    // There are no policy entries for the chosen policy
                                    } else {
                                        
                                        ?>

                                            <div class="content-wrap">

                                                <h2>There aren't any Policy Entries for this Policy yet.</h2>
                                                <p>Maybe you would like to <a class="overlay-trigger-add-policy-entry" href="javascript:void(0)">Add a Policy Entry</a>?</p>

                                            </div><!-- /.content-wrap -->

                                        <?
                                        
                                    }
                                
                                // $_GET['ChosenPolicy'] is not a valid integer
                                } else {

                                    ?>

                                        <div class="content-wrap">

                                            <h2>Sorry, something went wrong.</h2>
                                            <h3>We were expecting an integer for $_GET["ChosenPolicy"].</h3>
                                            <p><a href="javascript:history.back();">&laquo; Go back</a></p>

                                        </div><!-- /.content-wrap -->

                                    <?

                                }
    
                        ?>

                    </div><!-- /.content -->
                    
                </form>

                <?php require('footer.php'); ?>