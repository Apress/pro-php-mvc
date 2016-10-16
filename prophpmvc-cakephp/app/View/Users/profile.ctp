<?php if ($photo): ?>
    <img src="/uploads/<?php echo $photo; ?>" />
<?php endif; ?>
<h1><?php echo $user["User"]["first"]; ?> <?php echo $user["User"]["last"]; ?></h1>
This is a profile page!