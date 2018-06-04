<?php require('header.php'); ?>

                <section id="nav-sub">
                    
                    <ul>
                        <li class="current-page-item"><a href="#">Exported Policy</a></li>
                    </ul>
                    
                </section><!-- /#nav-sub -->

                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">

                    <div class="content">
                        
                        <?php
                        
                            if($core->isValidInt($_POST['ChosenPolicy'])) {

                                    // Get all policy entries for $_POST['ChosenPolicy']
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
                                    $stmt->bindParam(':ChosenPolicy', $_POST['ChosenPolicy']);
                                    $stmt->execute();
                                    if($stmt->rowCount() > 0) {
                                        
                                        ?>
                                
                                            <div class="content-wrap">
                                                <h2>Copy and paste the following into a new Cisco Extended Access List.</h2>
                                                <code>
                                                
                                        <?
                                           
                                            // Get the policy entries
                                            while ($row = $stmt->fetch())
                                            {
                                                echo 'remark - ' . $row['Comment'] . '<br>';
                                                if($row['Action'] == 0) {
                                                    echo "deny ";
                                                } else {
                                                    echo "permit ";
                                                }
                                                if($row['InternetProtocolName'] == 0) {
                                                    echo "tcp ";
                                                } else {
                                                    echo "udp ";
                                                }
                                                if($row['SrcCIDR'] == 32) {
                                                    echo "host " . $row['SrcNetwork'] . ' ';
                                                } elseif($row['SrcNetwork'] == '0.0.0.0') {
                                                    echo "any ";
                                                } else {
                                                    echo $row['SrcNetwork'] . ' ' . $core->getWildcardMask($row['SrcCIDR']) . ' ';
                                                }
                                                if($row['SrcStartPort'] == $row['SrcEndPort']) {
                                                    echo "eq " . $row['SrcStartPort'];
                                                } elseif($row['SrcStartPort'] < $row['SrcEndPort'] && !($row['SrcStartPort'] == 1 && $row['SrcEndPort'] == 65535)) {
                                                    echo "range " . $row['SrcStartPort'] . ' ' . $row['SrcEndPort'] . ' ';
                                                }
                                                if($row['DstCIDR'] == 32) {
                                                    echo "host " . $row['DstNetwork'] . ' ';
                                                } elseif($row['DstNetwork'] == '0.0.0.0') {
                                                    echo "any ";
                                                } else {
                                                    echo $row['DstNetwork'] . ' ' . $core->getWildcardMask($row['DstCIDR']) . ' ';
                                                }
                                                if($row['DstStartPort'] == $row['DstEndPort']) {
                                                    echo "eq " . $row['DstStartPort'];
                                                } elseif($row['DstStartPort'] < $row['DstEndPort'] && !($row['DstStartPort'] == 1 && $row['DstEndPort'] == 65535)) {
                                                    echo "range " . $row['DstStartPort'] . ' ' . $row['DstEndPort'];
                                                }
                                                echo '<br>';
                                            }

                                        ?>
                                                
                                                </code>
                                            </div><!-- /.content-wrap -->
                        
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
                                            <h3>We were expecting an integer for $_POST["ChosenPolicy"].</h3>
                                            <p><a href="javascript:history.back();">&laquo; Go back</a></p>

                                        </div><!-- /.content-wrap -->

                                    <?

                                }
    
                        ?>

                    </div><!-- /.content -->
                    
                </form>

                <?php require('footer.php'); ?>