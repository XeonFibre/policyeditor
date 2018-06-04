                    <?php

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

                    <!--
                        Multi-step form
                        From: https://codepunk.io/validating-a-single-page-multi-step-html-form/
                    -->
                    <form id="form-add-policy-entry" method="POST" action="policies.php" data-step="1">  
                        <section data-step="1">
                            <h1 class="section-title">Welcome</h1>
                            <h2 class="section-subtitle">This wizard will help you create a Policy Entry that will Permit or Deny traffic.</h2>
                        </section>
                        <section data-step="2">
                            <h1 class="section-title">Protocol and Action</h1>
                            <section class="left">
                                <fieldset>
                                    <label for="policy">Policy</label>
                                    <span>Select an existing Policy for this Policy Entry.</span>
                                    <select name="Policy" required="required">
                                        <?php
                                            $stmt = $pdo->query('SELECT ID,Name FROM Policies');
                                            while ($row = $stmt->fetch())
                                            {
                                                echo '<option value="'.$row['ID'].'">'.$row['Name'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </fieldset>
                                <fieldset>
                                    <label for="protocol">Protocol</label>
                                    <span>What Protocol is this Policy Entry going to use?</span>
                                    <select name="InternetProtocol" required="required">
                                        <?php
                                            $stmt = $pdo->query('SELECT ID,Name FROM InternetProtocols');
                                            while ($row = $stmt->fetch())
                                            {
                                                echo '<option value="'.$row['ID'].'">'.$row['Name'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </fieldset>
                            </section>
                            <section class="right">
                                <fieldset>
                                    <label for="comment">Comment</label>
                                    <span>What will this Policy Entry do?</span>
                                    <input type="text" name="Comment" placeholder="Comment" required>
                                </fieldset>
                                <fieldset>
                                    <label for="action">Action</label>
                                    <span>Will this Policy Entry Permit or Deny traffic?</span>
                                    <select name="Action" required="required">
                                        <option value="1">Permit</option>
                                        <option value="0">Deny</option>
                                    </select>
                                </fieldset>
                            </section>
                        </section>
                        <section data-step="3">
                            <h1 class="section-title">Source and Destination</h1>
                            <fieldset>
                                <label for="SrcNetwork">Source</label>
                                <span>Select an existing Network or Endpoint.</span>
                                <select name="SrcNetwork">
    
                                    <option value="1">All (everything)</option>
                                    
                                    <optgroup label="Networks">
                                    <?php

                                        $stmt = $pdo->query('SELECT ID,Name,INET6_NTOA(Network) AS Network,CIDRMask
                                                        FROM NetworksEndpoints
                                                        WHERE CIDRMask IS NOT NULL AND NOT CIDRMask = "32" AND NOT CIDRMask = "0"');

                                        while ($row = $stmt->fetch())
                                        {
                                            echo '<option value="'.$row['ID'].'">'.$row['Name'].' ('.$row['Network'].'/'.$row['CIDRMask'].')</option>';
                                        }

                                    ?>
                                    </optgroup>

                                    <optgroup label="Endpoints">
                                    <?php

                                        $stmt = $pdo->query('SELECT ID,Name,INET6_NTOA(Network) AS Network,CIDRMask
                                                        FROM NetworksEndpoints
                                                        WHERE CIDRMask IS NULL OR CIDRMask = "32"');

                                        while ($row = $stmt->fetch())
                                        {
                                            echo '<option value="'.$row['ID'].'">'.$row['Name'].' ('.$row['Network'].')</option>';
                                        }

                                    ?>
                                    </optgroup>

                                </select>
                            </fieldset>
                            <fieldset>
                                <label for="DstNetwork">Destination</label>
                                <span>Select an existing Network or Endpoint.</span>
                                <select name="DstNetwork">
                                    
                                    <option value="1">All (everything)</option>
    
                                    <optgroup label="Networks">
                                    <?php

                                        $stmt = $pdo->query('SELECT ID,Name,INET6_NTOA(Network) AS Network,CIDRMask
                                                        FROM NetworksEndpoints
                                                        WHERE CIDRMask IS NOT NULL AND NOT CIDRMask = "32" AND NOT CIDRMask = "0"');

                                        while ($row = $stmt->fetch())
                                        {
                                            echo '<option value="'.$row['ID'].'">'.$row['Name'].' ('.$row['Network'].'/'.$row['CIDRMask'].')</option>';
                                        }

                                    ?>
                                    </optgroup>

                                    <optgroup label="Endpoints">
                                    <?php

                                        $stmt = $pdo->query('SELECT ID,Name,INET6_NTOA(Network) AS Network,CIDRMask
                                                        FROM NetworksEndpoints
                                                        WHERE CIDRMask IS NULL OR CIDRMask = "32"');

                                        while ($row = $stmt->fetch())
                                        {
                                            echo '<option value="'.$row['ID'].'">'.$row['Name'].' ('.$row['Network'].')</option>';
                                        }

                                    ?>
                                    </optgroup>

                                </select>
                            </fieldset>
                        </section>
                        <section data-step="4">
                            <h1 class="section-title">Services</h1>
                            <h2 class="section-subtitle">Specify source and destination ports.</h2>
                            <fieldset>
                                <label for="option5">Source ports</label>
                                <span>Select All ports or specify a single port or range (usually All for dynamic port services such as SSH).</span>
                                <section class="radio-or-input">
                                    <section class="left">
                                        <input type="radio" name="SrcPortsAll"><span class="radio-name">All</span>
                                    </section>
                                    <section class="right">
                                        <input type="text" placeholder="Ports e.g. 445 or 1812-1813" spellcheck="false" name="SrcPorts">
                                    </section>
                                </section>
                            </fieldset>
                            <fieldset>
                                <label for="option4">Destination ports</label>
                                <span>Select All ports or specify a single port or range.</span>
                                <section class="radio-or-input">
                                    <section class="left">
                                        <input type="radio" name="DstPortsAll"><span class="radio-name">All</span>
                                    </section>
                                    <section class="right">
                                        <input type="text" placeholder="Ports e.g. 445 or 1812-1813" spellcheck="false" name="DstPorts">
                                    </section>
                                </section>
                            </fieldset>
                        </section>
                        <input type="hidden" name="add-policy-entry" value="add-policy-entry">
                        <div class="button-container">
                            <div class="f-p-left">
                                <button class="cancel">Cancel</button>
                            </div>
                            <div class="f-p-right">
                                <button class="button button-outline">Back</button>
                                <input class="button button-primary" type="submit" value="Next">
                            </div>
                        </div>
                    </form>