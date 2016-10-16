<h1>Search</h1>
<form method="post">
    <ol>
        <li>
            <label>
                Query:
                <input type="text" name="query" value="<?php echo $query; ?>" />
            </label>
        </li>
        <li>
            <label>
                Order:
                <select name="order">
                    <option <?php if ($order == "created"): ?>selected="selected"<?php endif; ?> value="created">Created</option>
                    <option <?php if ($order == "modified"): ?>selected="selected"<?php endif; ?> value="modified">Modified</option>
                    <option <?php if ($order == "first"): ?>selected="selected"<?php endif; ?> value="first">First name</option>
                    <option <?php if ($order == "last"): ?>selected="selected"<?php endif; ?> value="last">Last name</option>
                </select>
            </label>
        </li>    
        <li>
            <label>
                Direction:
                <select name="direction">
                    <option <?php if ($direction == "asc"): ?>selected="selected"<?php endif; ?> value="asc">Ascending</option>
                    <option <?php if ($direction == "desc"): ?>selected="selected"<?php endif; ?> value="desc">Descending</option>
                </select>
            </label>
        </li>
        <li>
            <label>
                Page:
                <select name="page">
                    <?php if ($count == 0): ?>
                        <option value="1">1</option>
                    <?php else: ?>
                        <?php for ($i = 1; $i < ceil($count / $limit); $i++): ?>
                            <option <?php if ($page == $i): ?>selected="selected"<?php endif; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    <?php endif; ?>
                </select>
            </label>
        </li>
        <li>
            <label>
                Limit:
                <select name="limit">
                    <option <?php if ($limit == 10): ?>selected="selected"<?php endif; ?> value="10">10</option>
                    <option <?php if ($limit == 20): ?>selected="selected"<?php endif; ?> value="20">20</option>
                    <option <?php if ($limit == 30): ?>selected="selected"<?php endif; ?> value="30">30</option>
                </select>
            </label>
        </li>
        <li>
            <input type="submit" name="search" value="search" />
        </li>
    </ol>
</form>
<?php if ($users): ?>
    <table>
        <tr>
            <th>Name</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user->first; ?> <?php echo $user->last; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>