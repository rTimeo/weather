<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" type="text/css" href="style.css">
    <title>Entrez une ville</title>
</head>
<body>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


    
if (isset($_POST['submit'])) {
    if (!empty($_POST['ville'])) {
        $city = urlencode($_POST['ville']);
    } else{
        $city = "paris";
    } 

    $token = "b609b585a7ecad3331b8cd0079c3d6c9";
    $weather = file_get_contents("https://api.openweathermap.org/data/2.5/weather?q=$city&lang=fr&appid=$token");
    $weather_data = json_decode($weather, true);
    $city = "Paris";

    if ($weather_data['cod'] == 200) {
        $_SESSION['weather'] = [
            'name' => $weather_data["name"],
            'temp' => round($weather_data["main"]["temp"] - 273.15),
            'humidity' => round($weather_data["main"]["humidity"]),
            'temp_max' => round($weather_data["main"]["temp_max"] - 273.15),
            'temp_min' => round($weather_data["main"]["temp_min"] - 273.15),
            'description' => $weather_data['weather'][0]['description'],
            'timezone' => $weather_data["timezone"]
            ];
    } else{
        $_SESSION['error'] = "error";
        unset($_SESSION['weather']);

    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$weather_data = $_SESSION['weather'] ?? [];

$name = $weather_data['name'] ?? "";
$temp = $weather_data['temp'] ?? "";
$humidity = $weather_data['humidity'] ?? "";
$temp_max = $weather_data['temp_max'] ?? "";
$temp_min = $weather_data['temp_min'] ?? "";
$description_temp = $weather_data['description'] ?? "";




$error = $_SESSION['error'] ?? "";
unset($_SESSION['error']);

$imageHTML = '';  
if($description_temp == "peu nuageux"){
    $imagePath = "test.png";  
    $imageHTML = "<img src='$imagePath' alt='' />";
}

$city_time_str = "";

if($name){
    $timezone_offset = $weather_data['timezone'] ?? 0;  
    $hours = intval($timezone_offset / 3600);
    $minutes = ($timezone_offset % 3600) / 60;
    $city_time = new DateTime('now', new DateTimeZone("UTC"));
    $interval = new DateInterval(sprintf("PT%dH%dM", abs($hours), abs($minutes)));

    if ($timezone_offset < 0) {
        $city_time->sub($interval);
    } else {
        $city_time->add($interval);
    }

    $city_time_str = $city_time->format('d/m/Y - H:i');

} else{

}



?>



    <header>
        <img src="images/wallpaper.jpg" alt="wp">
    </header>
<main> 
    <section class="left">
        <div class="bloc-left">
            <p> <?php echo htmlspecialchars($temp) ?></p>
            <div class="name-date">
            <p> <?php echo htmlspecialchars($name) ?></p>

                <div class="date">

                <p> <?php echo htmlspecialchars($city_time_str) ?></p>
                </div>
            </div>
            <div class="image-temp">
                <p></p>
            </div>
        </div>

    </section>

    <section class="right">
        <div>
        <form method="post">
            <input type="text" name="ville">
            <input type="submit" name="submit" value="submit">
        </form>
              <?php 
    echo $error;
                echo $name;
                echo $temp;
                echo $humidity;
                echo $temp_max;
                echo $temp_min;

            ?>

        </div>
          
        
    </section>
</main>

    
</body>
</html>