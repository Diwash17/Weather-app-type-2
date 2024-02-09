/*
 Student Name = Diwash Adhikari
 Student ID = 2407736 
 */


// Asynchrous function to check weather for a given city
async function checkweather(city) {
    try {
        // Fetch weather data from the server using the provided city name
        const response = await fetch(`http://localhost/PORT_2/Diwash_Adhikari_2407736.php?city=${city}`);
        // Extract JSON data from the response
        const data = await response.json();
        console.log(data)
        // checks if response indicates an error
        if (data.error !== "City not found" && data.error !== "Please enter a city") {
            document.querySelector(".loc").textContent = `${data[0].city},${data[0].country}`
            document.querySelector(".Temp").textContent = `${data[0].temp} °C`;
            document.querySelector(".weath").textContent = (data[0].description);
            document.querySelector("#mainimg").src = `https://openweathermap.org/img/wn/${data[0].icon}@2x.png`
            document.querySelector(".Press").textContent = `${data[0].pressure} hPa`
            document.querySelector(".WindSpeed").textContent = `${data[0].windspeed} m/s`
            document.querySelector(".Humidity").textContent = `${data[0].humidity} %`
            //  function to display date and time
            // function to display date and time
            const displayDate = () => {
                let dateObject = new Date(data[0].timestamp * 1000);
                // Format the date  to a localized string with specific options
                let localDate = dateObject.toLocaleString('en-US', {
                    weekday: 'short',
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                });
                document.querySelector(".Time").textContent = localDate;
            }

            displayDate();



        }
        else {
            alert(data.error)
        }
    } catch (error) {
        // Display an alert if there's an error fetching or processing the weather data
        alert(`Sorry some error occured: ${error}`)
    }

}
// Event listener for the button click, triggers the weather check for the city entered in the input field
document.querySelector("button").addEventListener('click', () => {
    let value = document.querySelector("input")
    checkweather(value.value)
    weatherforsevendays(value.value)
})
// Event listener for the "keydown" event, triggers the weather check when the Enter key is pressed
document.querySelector("input").addEventListener('keydown', (e) => {
    if (e.key == 'Enter') {
        let enterSearch = document.querySelector("input")
        checkweather(enterSearch.value)
        weatherforsevendays(enterSearch.value)
    }
})

//leads weather of ongole when page is waether app is opened or refreshed.
window.onload = () => {
    checkweather('Ongole')
    weatherforsevendays('Ongole')
}
// This async function fetches weather data for the specified city for the next seven days
async function weatherforsevendays(city) {
    try {
        // Fetch weather data from a local server running at http://localhost/PORT_2/weather.php
        const response = await fetch(`http://localhost/PORT_2/Diwash_Adhikari_2407736.php?city=${city}`);
         // Convert the response to JSON format
        const data = await response.json();
        // log to retrive the weather data
        console.log(data); 
        // Access the table body element in the HTML document
        let tbody = document.querySelector("#tablebody");
        tbody.innerHTML = ""; // Clear existing table rows
        // to iterate over weather data for the next seven days
        for (let k = 0; k <=6; k++) {
            // Create a table row element for each day's weather data
            let tr = document.createElement("tr");
            // Populate the table row with weather information for the respective day
            tr.innerHTML = `
            
                <td>${data[k].date}</td>
                <td>${data[k].country}</td>
                <td>${data[k].city}</td>
                <td>${data[k].temp}°C</td>
                <td>${data[k].pressure}hPa</td>
                <td>${data[k].humidity}%</td>
                <td>${data[k].windspeed}m/s</td>
                <td>${data[k].description}<img src="https://openweathermap.org/img/wn/${data[k].icon}.png" alt="Weather Icon"></td>
                
            `;
             // Append the table row to the table body
            tbody.appendChild(tr);
        }
        // shows errors that occcur during fetch and processing of data
    } catch{
        console.log("Error");
    }
}




