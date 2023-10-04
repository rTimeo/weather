<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrez une ville</title>
</head>
<body>

<form method="post">
    <input type="text" name="ville">
    <input type="submit" name="submit" value="submit">
</form>

<?php

$token = "b609b585a7ecad3331b8cd0079c3d6c9";
    
if (isset($_POST['submit'])) {

    $ville = $_POST['ville'];

    // Getting geographical info for the city

    $meteo = file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=$ville&appid=$token");

    $donnees_meteo = json_decode($meteo, true);

    // Stocker les données nécessaires dans la session ou une autre méthode de stockage
    
    $_SESSION['name'] = $donnees_meteo["name"];
     $donnees_meteo["main"]["temp"] -= 273;
    $_SESSION['temp'] = round($donnees_meteo["main"]["temp"]) ;
    $_SESSION['humidity'] = round($donnees_meteo["main"]["humidity"]) ;



    // Redirigez vers la même page (ou une autre si nécessaire)
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Récupérer les données de la session (si disponibles)
$name = isset($_SESSION['name']) ? $_SESSION['name'] : '';
$temp = isset($_SESSION['temp']) ? $_SESSION['temp'] : '';
$humidity = isset($_SESSION['humidity']) ? $_SESSION['humidity'] : '';


if($name)   {
$humidity;
$temp;
}else{
    $temp= "";
    $humidity = "";

}


?>

<div>
<div>
        <?php 
        echo"$humidity <br>";
        echo $temp; 
        ?>
    </div>


</div>

</body>
</html>