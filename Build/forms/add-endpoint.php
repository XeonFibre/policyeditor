                    <!--
                        Multi-step form
                        From: https://codepunk.io/validating-a-single-page-multi-step-html-form/
                    -->
                    <form id="form-add-endpoint" method="POST" action="endpoints.php" data-step="1">  
                        <section data-step="1">
                            <h1 class="section-title">Welcome</h1>
                            <h2 class="section-subtitle">This wizard will help you create an Endpoint that you can use in Policy Entries.</h2>
                        </section>
                        <section data-step="2">
                            <h1 class="section-title">Name and Scope</h1>
                            <fieldset>
                                <label for="name">Name</label>
                                <span>What is the name of this Endpoint?</span>
                                <input type="text" name="Name" placeholder="Name">
                            </fieldset>
                            <fieldset>
                                <label for="name">IP Address</label>
                                <span>What is the Endpoints' IP address?</span>
                                <input type="text" name="Network" placeholder="IP address e.g. 192.168.1.62">
                            </fieldset>
                        </section>
                        <input type="hidden" name="add-endpoint" value="add-endpoint">
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