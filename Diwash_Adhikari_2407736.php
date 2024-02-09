 
<?php
/* Student Name = Diwash Adhikari
Student ID = 2407736
*/
/*
 * PHP Script of Weather App
 * This script fetches weather data for a specified city from OpenWeatherMap API,
 * caches it in a MySQL database, and serves the data as JSON.
 */

// Set necessary headers for CORS and JSON content type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
//Function to fetch weather data from OpenWeatherMap API
function fetch_openweather($city)
{
    // OpenWeatherMap API key 
    $apiId = "d50a28a0c1f21285fc6216026df6fb70";
    // Construct API URL for the specified city
    $url = 'https://api.openweathermap.org/data/2.5/weather?q=' . $city . '&appid=' . $apiId . '&units=metric';
    // Fetch data from the API
    $raw = @file_get_contents($url);
    // Decode JSON response into an associative array
    $data = json_decode($raw, true);
    return $data;
}
// Function to fetch weather data from OpenWeatherMap API
function fetch_from_mysql_database($conn, $city)
{
    // Query to fetch weather data for the specified city, grouped by date and ordered by timestamp
    try {
        $response = $conn->query("SELECT * FROM `weatherdata` WHERE city='$city' GROUP BY `date` ORDER BY `timestamp` DESC LIMIT 7");
        if ($response) {
            // Fetch all rows as an associative array
            $data = $response->fetch_all(MYSQLI_ASSOC);
            return $data;

        } else {
            return NULL;
        }
    } catch (Exception $th) {
        return NULL;
    }
}
// Function to establish a connection to the MySQL database
function connection_database($server, $username, $password, $db)
{
    try {
        // Create a new MySQLi connection
        $conn = new mysqli($server, $username, $password, $db);
        // Check for connection errors
        if ($conn->connect_errno) {
            return $conn->connect_errno;
        } else {
            return $conn;
        }
    } catch (Exception $th) {
        return NULL;
    }
}
// Function to add weather data to MySQL database
function add_to_mysql_database($conn, $data)
{
    try {
        // Extract relevant weather data from the response array
        $city = $data["name"];
        $country = $data["sys"]["country"];
        $temp = $data["main"]["temp"];
        $pressure = $data["main"]["pressure"];
        $humidity = $data["main"]["humidity"];
        $windspeed = $data["wind"]["speed"];
        $timestamp = $data["dt"];
        $description = $data["weather"][0]["description"];
        $icon = $data["weather"][0]["icon"];
        // Format timestamp to date
        $date = date('Y-m-d', $timestamp);

        // Insert weather data into the database
        $conn->query("INSERT INTO `weatherdata` (`city`, `country`, `temp`, `pressure`, `humidity`, `windspeed`, `timestamp`, `description`, `icon`,`date`) VALUES ('$city', '$country', '$temp', '$pressure', '$humidity', '$windspeed', '$timestamp', '$description', '$icon','$date'); ");

    } catch (Exception $th) {
        return NULL;
    }

}

//The main function to fetch and process data
function fetch_data()
{
    // Establish connection to the MySQL database
    $connection = connection_database("localhost", "root", "", "main");
    if (isset($_GET["city"])) {
        if ($_GET["city"] == null) {
            echo '{"error": "Please enter a city"}';
        } else {
            $city = $_GET["city"];
            $response_d = fetch_from_mysql_database($connection, $city);
            if (count($response_d) == 0) {
                // If no data found in the database, fetch from OpenWeatherMap API
                $response = fetch_openweather($city);
                if ($response) {
                    // Add fetched data to the database
                    add_to_mysql_database($connection, $response);
                    // Fetch newly added data from the database
                    $data = fetch_from_mysql_database($connection, $city);
                    echo (json_encode($data));
                } else {
                    // Return error message if city not found in OpenWeatherMap
                    echo '{"error":"City not found"}';
                }
            } else {
                // If data found in the database
                if (time() - $response_d[0]["timestamp"] > 10800) {
                    // If data is older than 3 hours, fetch fresh data from OpenWeatherMap API
                    $response = fetch_openweather($city);
                    // Add fresh data to the database
                    add_to_mysql_database($connection, $response);
                    // Fetch newly added data from the database
                    $data = fetch_from_mysql_database($connection, $city);
                    echo (json_encode($data));
                } else {
                    // If data is within 3 hours, return data from the database
                    echo (json_encode($response_d));
                }
            }
        }
    }
}
// call the main function to fetch and process weather data
fetch_data();




