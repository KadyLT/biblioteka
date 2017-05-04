<?php

require_once 'database.php';

//id parameter
//var_dump($_GET);
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    $id = null;
}

if (empty($id)) {
    echo "Reikalingas parametras";
    exit;
}
//užklausa informacijai atspausdinti
$query = "SELECT id, title, year, author, genre FROM books_info where id=" . $id; // tegu buna ID=$id
$search_result = mysqli_query($connect, $query);

if (mysqli_num_rows($search_result) == 0) {
    echo 'Rezultato nėra';
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Biblioteka</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>
<a href="index.php">
    <div class="jumbotron text-center">
        <h1>Biblioteka</h1>
    </div>
</a>
<div class="container">

    <div>
        <ul class="nav nav-pills">
            <li><a href="index.php">Grįžti</a></li>
        </ul>
    </div>
    <div>
        <?php $row = mysqli_fetch_array($search_result); ?>
        <h4> Informacija apie knygą:<strong> <?php echo $row['title']; ?></strong></h4>
        <p><strong>Nr: </strong><?php echo $row['id']; ?></p>
        <p><strong>Autorius: </strong><?php echo $row['author']; ?></p>
        <p><strong>Leidimo metai: </strong><?php echo $row['year']; ?></p>
        <p><strong>Žanras: </strong><?php echo $row['genre']; ?></p>
    </div>
</div>
</body>
</html>