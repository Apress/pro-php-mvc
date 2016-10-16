<h1>Search</h1>
<?php
    echo $this->Form->create("Search");
    
    echo $this->Form->input("query");

    echo $this->Form->input("order", array(
        "options" => array(
            "id" => "id",
            "first" => "first",
            "last" => "last"
        )
    ));

    echo $this->Form->input("direction", array(
        "options" => array(
            "id" => "id",
            "first" => "first",
            "last" => "last"
        )
    ));

    echo $this->Form->input("limit", array(
        "options" => array(
            10 => 10,
            20 => 20,
            30 => 30,
            40 => 40
        )
    ));

    $range = range(1, ceil($count / $limit));
    $options = array_combine($range, $range);

    echo $this->Form->input("page", array(
        "options" => $options
    ));

    echo $this->Form->end("search");
?>

<table>
    <tr>
        <th>id</th>
        <th>name</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?php echo $user["User"]["id"]; ?></td>
        <td><?php echo $user["User"]["first"]; ?> <?php echo $user["User"]["last"]; ?></td>
    </tr>
    <?php endforeach; ?>
</table>