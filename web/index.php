<?php

require_once 'database.php';

function linkTo($parametrai)
{
    $parametrai = array_merge($_GET, $parametrai);
    $mas = array();
    foreach ($parametrai as $key => $value) {
        $mas[] = urlencode($key) . "=" . urlencode($value);
    }
    if (count($mas) > 0) {
        return "index.php?" . implode("&", $mas);
    }
    return "index.php";
}


$results_per_page = 15; // items limit in one page

if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = intval($_GET['page']);
}
$page = max(1, $page);

$this_page_first_result = ($page - 1) * $results_per_page; // pusliapiavimo pradine reiksme 1 1-1=0


$galimi_laukai = array('id', 'title', 'year', 'author', 'genre');
if (isset($_GET['laukas']) && in_array($_GET['laukas'], $galimi_laukai)) {
    $rusiavimas = $_GET['laukas'];
} else {
    $rusiavimas = 'id';
}
if (isset($_GET['kryptis']) && $_GET['kryptis'] == 'desc') {
    $kryptis = 'desc';
} else {
    $kryptis = 'asc';
}

$sql = "SELECT * FROM books_info ";
$sqlSkaic = "SELECT count(*) as kiekis FROM books_info ";

$searchString = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchString = trim($_GET['search']);
    $sql .= " WHERE CONCAT(id, title, year, author, genre) LIKE '%" . mysqli_escape_string($connect, $searchString) . "%' ";
    $sqlSkaic .= " WHERE CONCAT(id, title, year, author, genre) LIKE '%" . mysqli_escape_string($connect, $searchString) . "%' ";
}

$sql .= " ORDER BY " . $rusiavimas . ' ' . $kryptis . " LIMIT " . $this_page_first_result . "," . $results_per_page;

//skaiciavimai
$mysqlResult = mysqli_query($connect, $sqlSkaic);
$row = mysqli_fetch_assoc($mysqlResult);
$number_of_pages = ceil($row['kiekis'] / $results_per_page); // kiek bus psl isviso

// rezultatai
$mysqlResult = mysqli_query($connect, $sql);

//---------------------


?>
<!DOCTYPE html>
<html lang="en">
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
    <h2>Knygų sąrašas</h2>
    <form action="index.php" method="get" class="navbar-form navbar-left">
        <div class="form-group">
            <input type="text" class="form-control" name="search" placeholder="Ieškoti knygos"
                   value="<?php echo htmlentities($searchString); ?>">
            <input class="form-control" type="hidden" name="laukas" value="<?php echo htmlentities($rusiavimas); ?>">
            <input class="form-control" type="hidden" name="kryptis" value="<?php echo htmlentities($kryptis); ?>">
        </div>
        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
    </form>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>id <a href="<?php echo linkTo(["laukas" => "id", "kryptis" => "asc"]); ?>">&#x25B2</a><a
                        href="<?php echo linkTo(["laukas" => "id", "kryptis" => "desc"]); ?>">&#x25BC</th>
            <th>Pavadinimas <a href="<?php echo linkTo(["laukas" => "title", "kryptis" => "asc"]); ?>">&#x25B2</a><a
                        href="<?php echo linkTo(["laukas" => "title", "kryptis" => "desc"]); ?>">&#x25BC</th>
            <th>Metai <a href="<?php echo linkTo(["laukas" => "year", "kryptis" => "asc"]); ?>">&#x25B2</a><a
                        href="<?php echo linkTo(["laukas" => "year", "kryptis" => "desc"]); ?>">&#x25BC</th>
            <th>Autorius <a href="<?php echo linkTo(["laukas" => "author", "kryptis" => "asc"]); ?>">&#x25B2</a><a
                        href="<?php echo linkTo(["laukas" => "id", "kryptis" => "desc"]); ?>">&#x25BC</th>
            <th>Žanras <a href="<?php echo linkTo(["laukas" => "genre", "kryptis" => "asc"]); ?>">&#x25B2</a><a
                        href="<?php echo linkTo(["laukas" => "genre", "kryptis" => "desc"]); ?>">&#x25BC</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($mysqlResult)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><a href="more_info.php?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['author']; ?></td>
                <td><?php echo $row['genre']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <div>
        <ul class="pagination">
            <?php if ($page != 1) { ?>
                <li><a href="<?php echo linkTo(["page" => $page - 1]); ?>">Atgal</a></li>
            <?php } ?>

            <?php for ($pageNo = 1; $pageNo <= $number_of_pages; $pageNo++) {
                if ($page == $pageNo) {
                    echo '<li class="active">';
                } else {
                    echo '<li>';
                }
                echo '<a href="' . linkTo(['page' => $pageNo]) . '">' . $pageNo . '</a></li>';
            }
            ?>
            <?php if (($page != $number_of_pages) && ($number_of_pages > 1)) { ?>
                <li><a href="<?php echo linkTo(["page" => $page + 1]); ?>">pirmyn</a></li>
            <?php } ?>
        </ul>
    </div>
</div>
<footer class="panel-footer text-center">&copy; 2017</footer>
</body>
</html>
