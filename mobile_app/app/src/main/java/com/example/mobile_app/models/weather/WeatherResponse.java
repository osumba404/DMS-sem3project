package com.example.mobile_app.models.weather;
// Top-level response object
import java.util.List;
public class WeatherResponse {
    public String timezone;
    public CurrentWeather current;
    public List<HourlyForecast> hourly;
}