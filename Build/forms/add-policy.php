                    <!--
                        Multi-step form
                        From: https://codepunk.io/validating-a-single-page-multi-step-html-form/
                    -->
                    <form id="form-add-policy" method="POST" action="policies.php" data-step="1">  
                        <section data-step="1">
                            <h1 class="section-title">Welcome</h1>
                            <h2 class="section-subtitle">This wizard will help you create a Policy.</h2>
                        </section>
                        <section data-step="2">
                            <h1 class="section-title">Name</h1>
                            <fieldset>
                                <label for="name">Name</label>
                                <span>What is the name of this Policy?</span>
                                <input type="text" name="Name" placeholder="Name e.g. Protect Servers" required>
                            </fieldset>
                        </section>
                        <input type="hidden" name="add-policy" value="add-endpoint">
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