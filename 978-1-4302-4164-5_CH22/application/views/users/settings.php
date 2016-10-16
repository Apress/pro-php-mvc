<h1>Settings</h1>
<?php if ($success): ?>
    Your account has been updated!
<?php else: ?>
    <form method="post" enctype="multipart/form-data">
        <ol>
            <li>
                <label>
                    First name:
                    <input type="text" name="first" value="<?php echo $user->first; ?>" />
                    <?php echo form_error("first"); ?>
                </label>
            </li>
            <li>
                <label>
                    Last name:
                    <input type="text" name="last" value="<?php echo $user->last; ?>" />
                    <?php echo form_error("last"); ?>
                </label>
            </li>    
            <li>
                <label>
                    Email:
                    <input type="text" name="email" value="<?php echo $user->email; ?>" />
                    <?php echo form_error("email"); ?>
                </label>
            </li>
            <li>
                <label>
                    Password:
                    <input type="password" name="password" value="<?php echo $user->password; ?>" />
                    <?php echo form_error("password"); ?>
                </label>
            </li>
            <li>
                <input type="submit" name="save" value="save" />
            </li>
        </ol>
    </form>
<?php endif; ?>