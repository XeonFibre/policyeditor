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
                    <form id="form-add-policy-entry" method="POST" action="policy-export.php" data-step="1">  
                        <section data-step="1">
                            <h1 class="section-title">Welcome</h1>
                            <h2 class="section-subtitle">This wizard will help you export a Policy for a specific vendor of network devices.</h2>
                        </section>
                        <section data-step="2">
                            <h1 class="section-title">Vendor</h1>
                            <h2 class="section-subtitle">Select the vendor that this policy will be used with.</h2>
                            <fieldset>
                                <!--<div class="rich-button vendor-cisco">
                                    Cisco
                                </div>-->
                                <select name="vendor">
                                    <option value="cisco">Cisco (Extended ACL)</option>
                                </select>
                            </fieldset>
                        </section>
                        <input class="clone-value-dst" type="hidden" name="ChosenPolicy" value="">
                        <input type="hidden" name="export-policy" value="export-policy">
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