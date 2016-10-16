<h1>Register</h1>
<?php if ($success): ?>
    Your account has been created!
<?php else: ?>
    <form method="post" enctype="multipart/form-data">
        <ol>
            <li>
                <label>
                    First name:
                    <input type="text" name="first" />
                    <?php echo form_error("first"); ?>
                </label>
            </li>
            <li>
                <label>
                    Last name:
                    <input type="text" name="last" />
                    <?php echo form_error("last"); ?>
                </label>
            </li>    
            <li>
                <label>
                    Email:
                    <input type="text" name="email" />
                    <?php echo form_error("email"); ?>
                </label>
            </li>
            <li>
                <label>
                    Password:
                    <input type="password" name="password" />
                    <?php echo form_error("password"); ?>
                </label>
            </li>
            <li>
                <label>
                    Photo:
                    <input type="file" name="photo" />
                </label>
            </li>
            <li>
                <input type="submit" name="save" value="register" />
            </li>
        </ol>
    </form>
<?php endif; ?>