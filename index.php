<?php
session_start();

date_default_timezone_set('UTC');



const API_TOKEN = "b609b585a7ecad3331b8cd0079c3d6c9";
const API_ENDPOINT = "https://api.openweathermap.org/data/2.5/weather";

function getWeatherData($city = "Paris") {
    $url = API_ENDPOINT . "?q=" . urlencode($city) . "&lang=fr&appid=" . API_TOKEN;
    $weather_json = file_get_contents($url);
    return json_decode($weather_json, true);
}



function processWeatherData($weather_data) {

    if ($weather_data['cod'] != 200) {
        return [];
    }
    $weather_translation = [
        'clear' => 'Ensoleillé',
        'snow' => 'Neige',
        'clouds' => 'Nuageux',
        'rain' => 'Pluie',
        'drizzle' => 'Bruine',
    ];

    $weather_images = [
        'Ensoleillé' => 'clear.png',
        'Neige' => 'snow.png',
        'Nuageux' => 'cloudy.png',
        'Pluie' => 'rain.png'
    ];
    
    $description_english = strtolower($weather_data['weather'][0]['main']);
    $description_french = $weather_translation[$description_english] ?? $description_english;  
    $image_path = $weather_images[$description_french] ?? ''; 

    return [
        'name' => $weather_data["name"],
        'temp' => round($weather_data["main"]["temp"] - 273.15),
        'humidity' => round($weather_data["main"]["humidity"]),
        'temp_max' => round($weather_data["main"]["temp_max"] - 273.15),
        'temp_min' => round($weather_data["main"]["temp_min"] - 273.15),
        'description' => $description_french,
        'timezone' => $weather_data["timezone"],
        'ressentie' => round($weather_data["main"]["feels_like"] - 273.15),
        'speed' => round($weather_data["wind"]["speed"] * 3.6),
        'sunrise' => date("H:i", $weather_data['sys']['sunrise'] + $weather_data["timezone"]),
        'sunset' => date("H:i", $weather_data['sys']['sunset'] + $weather_data["timezone"]),
        'image' => $image_path

    ];
}



function getCurrentCityTime($timezone_offset) {
    $months_mapping = [
        "January" => "janvier",
        "February" => "février",
        "March" => "mars",
        "April" => "avril",
        "May" => "mai",
        "June" => "juin",
        "July" => "juillet",
        "August" => "août",
        "September" => "septembre",
        "October" => "octobre",
        "November" => "novembre",
        "December" => "décembre"
    ];
    
    $timezone_offset = $timezone_offset ?? 0;  
    $hours = intval($timezone_offset / 3600);
    $minutes = ($timezone_offset % 3600) / 60;
    $city_time = new DateTime('now', new DateTimeZone("UTC"));
    $interval = new DateInterval(sprintf("PT%dH%dM", abs($hours), abs($minutes)));
    
    if ($timezone_offset < 0) {
        $city_time->sub($interval);
    } else {
        $city_time->add($interval);
    }

    $month_english = $city_time->format('F');
    $month_french = $months_mapping[$month_english] ?? $month_english; 
    return $city_time->format('d ') . $month_french . $city_time->format(' Y - H:i');
}

if (!isset($_SESSION['weather']) || isset($_POST['submit'])) {
    $city = !empty($_POST['ville']) ? $_POST['ville'] : "Paris";
    $weather_data_raw = getWeatherData($city);
    $_SESSION['weather'] = processWeatherData($weather_data_raw);
    
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
$feel_like = $weather_data['ressentie'] ?? "";
$speed =  $weather_data['speed'] ?? "";
$sunrise =  $weather_data['sunrise'] ?? "";
$sunset =  $weather_data['sunset'] ?? "";
$image =  $weather_data['image'] ?? "";

$city_time_str = isset($weather_data['timezone']) ? getCurrentCityTime($weather_data['timezone']) : "";

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" type="text/css" href="style.css">
    <title>Entrez une ville</title>
</head>
<body>



<header>
        <img src="images/wall.jpg" alt="">
    </header>
    <main>
        <section class="left">
            <div class="bloc-left">
            <p class="left-one-p"><?php echo $temp?><span>°C</span></p>
                <div>
                    <p class="a"> <?php echo $name?></p>
                    <p class="b"><?php echo $city_time_str ?></p>
                    
                </div>
                <div>                    
                    <img src="images/<?php echo $image; ?>">

                   <p> <?php echo $description_temp ?></p>
                </div>


             
            </div>
        </section>
        <section class="right">
            <form method="post">
                <input type="text" name="ville" placeholder="Ville">
                <input type="submit" name="submit">
            </form>
                <h2> détails météo</h2>
            <div class="details">
                <ul>
                    <li>Ressentie</li>
                    <li>Temps max</li>
                    <li>Temps min</li>
                    <li>Humidité</li>
                    <li>Vent</li>
                    <li>Lever du soleil</li>
                    <li>Coucher de soleil</li>
                </ul>

                <ul>
                    <li><?php echo $feel_like ?>°</li>
                    <li> <?php echo $temp_max ?>°</li>
                    <li><?php echo $temp_min ?>°</li>
                    <li><?php echo $humidity ?>°</li>
                    <li><?php echo $speed ?> km/h</li>
                    <li><?php echo $sunrise ?></li>
                    <li><?php echo $sunset ?></li>
                </ul>


            </div>
        </section>
    </main>
</body>
</html>