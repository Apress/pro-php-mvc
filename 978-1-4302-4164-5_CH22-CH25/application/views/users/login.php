<h1>Login</h1>
<form method="post">
    <ol> 
        <li>
            <label>
                Email:
                <input type="text" name="email" />
            </label>
        </li>
        <li>
            <label>
                Password:
                <input type="password" name="password" />
                <?php if ($errors): ?><?php echo $errors; ?><?php endif; ?>
            </label>
        </li>
        <li>
            <input type="submit" name="login" value="login" />
        </li>
    </ol>
</form>