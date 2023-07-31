# Code challenge

Using the latest version of [Laravel][6], create a page that shows an interactive map (e.g. [LeafletJS][4]) of the weather stations available in Washington State (see [stations.json][1]), and for each station display the appropriate [weather icon][5] and max/min temperatures for the day.
In addition, the user should be able to click on any point on the map and see the current average temperature of the closest 3 stations - at that location - in a 25 miles radius.

The app should collect the weather data from the [current weather][2] endpoint of the free [OpenWeather][3] API.

Please include clear instructions on how to install and run your app locally.

###### Extras:

- Store the following information for every map click: location coordinates, user agent, time, IP.
- For better usability, the app should store/cache the data (ideally asynchronously) for the current day
- Implement the UI using React or Vue
- Add tests on either or both server and client code
- Make somehow your app available online - though we don't expect you to pay any extra money for this

### What we are looking for

- Clean and easy-to-read code
- Usage of best practices
- Knowledge of the language features available in the latest versions of PHP and JS


[1]: stations.json
[2]: https://openweathermap.org/current
[3]: https://openweathermap.org
[4]: https://leafletjs.com
[5]: https://openweathermap.org/weather-conditions
[6]: https://laravel.com/docs/10.x