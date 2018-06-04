<?php require('header.php'); ?>

                <section id="nav-sub">
                    
                    <ul>
                        <li class="current-page-item"><a href="#">Test Results</a></li>
                    </ul>
                    
                </section><!-- /#nav-sub -->

                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                    
                    <?php
                        
                        if(is_array($_POST['checkbox'])) {

                            if(count($_POST['checkbox']) == 1) {

                                if($core->isValidInt($_POST['checkbox'][0])) {
                                    
                                    ?>
                    
                                        <section id="context-info">

                                            <div>

                                                <img class="action policy-test-action" src="img/policytest-waiting.png">

                                                <?php

                                                    // Get the name and IP of the source endpoint for testing
                                                    $TestedSrcHost = 0;
                                                    $stmt = $pdo->prepare('SELECT ID,Name,INET6_NTOA(Network) AS Network
                                                                    FROM NetworksEndpoints
                                                                    WHERE ID = :SrcNetwork
                                                                    LIMIT 1');
                                                    $stmt->bindParam(':SrcNetwork', $_POST['TestedSrcHost']);
                                                    $stmt->execute();

                                                    ?>
                                                        <img src="img/policyentries-endpoint.png">
                                                    <?

                                                    while ($row = $stmt->fetch())
                                                    {
                                                        $TestedSrcHost = $row['Network'];
                                                        echo $row['Name'].' ('.$row['Network'].')';
                                                    }

                                                    ?>
                                                        <img class="large-spacing" src="img/policyentries-arrowright.png">
                                                    <?

                                                    // Get the name and IP of the destination endpoint for testing
                                                    $TestedDstHost = 0;
                                                    $stmt = $pdo->prepare('SELECT ID,Name,INET6_NTOA(Network) AS Network
                                                                    FROM NetworksEndpoints
                                                                    WHERE ID = :DstNetwork
                                                                    LIMIT 1');
                                                    $stmt->bindParam(':DstNetwork', $_POST['TestedDstHost']);
                                                    $stmt->execute();

                                                    ?>
                                                        <img src="img/policyentries-endpoint.png">
                                                    <?

                                                    while ($row = $stmt->fetch())
                                                    {
                                                        $TestedDstHost = $row['Network'];
                                                        echo $row['Name'].' ('.$row['Network'].')';
                                                    }

                                                ?>

                                            </div>

                                        </section><!-- /#context-info -->
                    
                                    <?

                                }

                            }

                        }

                    ?>

                    <div class="content">
                        
                        <?php
                        
                            if(is_array($_POST['checkbox'])) {
                                
                                if(count($_POST['checkbox']) == 1) {
                        
                                    if($core->isValidInt($_POST['checkbox'][0])) {

                                        // Get all policy entries for $_POST['checkbox'][0]
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
                                        $stmt->bindParam(':ChosenPolicy', $_POST['checkbox'][0]);
                                        $stmt->execute();
                                        if($stmt->rowCount() > 0) {

                                            ?>

                                                <table class="t-l">
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Comment</th>
                                                        <th>Source</th>
                                                        <th></th>
                                                        <th>Destination</th>    
                                                    </tr>

                                            <?

                                            // Get the policy entries
                                            $i = 0;
                                            $affectedByPolicy = false;
                                            while ($row = $stmt->fetch())
                                            {
                                                
                                                // If the policy entry affects the tested source and destination endpoint, highlight the first occurence green/red respectively
                                                if($i == 0) {
                                                    if($TestedSrcHost == $row['SrcNetwork'] || $ipInRange->ipv4_in_range($TestedSrcHost, $row['SrcNetwork']."/".$row['SrcCIDR'])) {
                                                        if($TestedDstHost == $row['DstNetwork'] || $ipInRange->ipv4_in_range($TestedDstHost, $row['DstNetwork']."/".$row['DstCIDR'])) {
                                                            $i++;
                                                            if($row['Action'] == "0") {
                                                                echo '<tr class="test-policy-denied">';
                                                                $affectedByPolicy = true;
                                                            } elseif($row['Action'] == "1") {
                                                                echo '<tr class="test-policy-permitted">';
                                                                $affectedByPolicy = true;
                                                            }
                                                        } else {
                                                            echo '<tr>';
                                                        }
                                                    } else {
                                                        echo '<tr>';
                                                    }
                                                } else {
                                                    echo '<tr>';
                                                }
                                                
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
                                                    <tr class="implicit <?php if(!$affectedByPolicy) { echo 'test-policy-denied'; } ?>">
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

                                    // $_POST['ChosenPolicy'] is not a valid integer
                                    } else {

                                        ?>

                                            <div class="content-wrap">

                                                <h2>Sorry, something went wrong.</h2>
                                                <h3>We were expecting an integer for $_POST["checkbox"].</h3>
                                                <p><a href="javascript:history.back();">&laquo; Go back</a></p>

                                            </div><!-- /.content-wrap -->

                                        <?

                                    }
                                    
                                } else {

                                    ?>

                                        <div class="content-wrap">

                                            <h2>Sorry, something went wrong.</h2>
                                            <h3>You can only test a single Policy at a time.</h3>
                                            <p><a href="javascript:history.back();">&laquo; Go back</a></p>

                                        </div><!-- /.content-wrap -->

                                    <?

                                }
                                
                            } else {

                                ?>

                                    <div class="content-wrap">

                                        <h2>Sorry, something went wrong.</h2>
                                        <h3>We were expecting an array for $_POST["checkbox"].</h3>
                                        <p><a href="javascript:history.back();">&laquo; Go back</a></p>

                                    </div><!-- /.content-wrap -->

                                <?

                            }
    
                        ?>

                    </div><!-- /.content -->
                    
                </form>

                <?php require('footer.php'); ?>