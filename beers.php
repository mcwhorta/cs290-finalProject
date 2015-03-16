<!-- Name: Allyce McWhorter -->
<!-- Email: mcwhorta@onid.oregonstate.edu -->
<!-- Class: CS290 -->
<!-- Assignment: Final Project-->

<?php
//MYSQL connection
$mysqli = new mysqli('oniddb.cws.oregonstate.edu', 'mcwhorta-db', 'OOunihvLeeTmSdmf', 'mcwhorta-db');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Check if a user is signed on
if (isset($_SESSION['username']) && $_SESSION['username'] === '') {
  echo "userNotLoggedOn";
  die();
}

//Get list of beers
function getBeers($mysqli) {
    return $mysqli->query("SELECT * FROM Beer;");
}
//Add beer to list
function addBeer($Name, $Brewery, $Style, $mysqli) {
    return $mysqli->query("INSERT INTO Beer (Name, Brewery, Style) VALUES ('$Name', '$Brewery', '$Style');");
}
//Delete beer from list
function deleteBeer($id, $mysqli) {
    return $mysqli->query("DELETE FROM Beer WHERE id=$id LIMIT 1;");
}
//Delete all beers from list
function deleteAllBeers($mysqli) {
    return $mysqli->query("DELETE FROM Beer WHERE 1;");
}

if (array_key_exists('deleteAll', $_POST)) {
	deleteAllBeers($mysqli);
}
if (array_key_exists('deleteOne', $_POST)) {
	deleteBeer($_POST['deleteOne'], $mysqli);
}
if (array_key_exists('addBeer', $_POST)) {
    addBeer($_POST['Name'], $_POST['Brewery'], $_POST['Style'], $mysqli);
}

$beers = getBeers($mysqli);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Beer Log</title>
	<link href="bootstrap.min.css" rel="stylesheet">
	<link href="beers.css" rel="stylesheet">
    <script src='login.js'></script>
</head>
<body onload="checkIfSignedInForAppPage();">
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div id="navbar">
          <ul class="nav navbar-nav navbar-right">
            <li><button type="button" class="btn btn-default navbar-btn" id="logout-button" onclick="logout();">Logoff</button></li>
          </ul>
        </div>
      </div>
    </nav>
<div>
	<h1>Welcome to Beer Log</h1>
	<p>Please log any beers you have tried on your quest to becoming a beer connoisseur!</p>
    <form action="beers.php" method="POST">
        Name<input type="text" name="Name" required>
        Brewery<input type="text" name="Brewery">
        Style<input type="text" name="Style">
        <input type="hidden" name="addBeer" value="true">
        <input type="submit" value="Add Beer">
    </form>
<?php
    if (!empty($beers))
    {
        ?>
    <hr>
    <?php
    }
?>
</div>
<h3>Your Current Beer Log:</h3>
<?php
if (!empty($beers))
{
    echo '<div><table border="2">';
    echo '<tr><th>Name</th><th>Brewery</th><th>Style</th></tr>';
    foreach ($beers as $key => $beer)
    {
        echo '<tr><td>' .
        $beer['Name'] .
        '</td><td>' .
        $beer['Brewery'] .
        '</td><td>' .
        $beer['Style'] .
		'</td><td>';
        
		echo '<form action="beers.php" method="POST">
		<input type="hidden" name="deleteOne" value="' . $beer['id'] . '">
		<input type="submit" value="Delete">
		</form>
        </td></tr>';
    }
    echo '</table></div>';
}
else
{
    echo '<hr><div>No beers.</div>';
}
?>
</body>
</html>