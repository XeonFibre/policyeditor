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
                    <form id="form-test-policy" method="POST" action="policy-test.php" data-step="1">  
                        <section data-step="1">
                            <h1 class="section-title">Welcome</h1>
                            <h2 class="section-subtitle">This wizard will help you test Endpoint to Endpoint connectivity when the chosen Policy is applied.</h2>
                        </section>
                        <section data-step="2">
                            <h1 class="section-title">Source and Destination</h1>
                            <fieldset>
                                <label for="TestedSrcHost">Source</label>
                                <span>Select an existing Endpoint.</span>
                                <select name="TestedSrcHost">
                                        
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
                                <label for="TestedDstHost">Destination</label>
                                <span>Select an existing Endpoint.</span>
                                <select name="TestedDstHost">
                                    
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
                        <input class="clone-value-dst" type="hidden" name="checkbox[]" value="">
                        <input type="hidden" name="test-policy" value="test-policy">
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