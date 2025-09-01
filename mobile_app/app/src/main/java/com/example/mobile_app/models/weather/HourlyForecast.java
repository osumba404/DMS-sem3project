package com.example.mobile_app.models.weather;
// Corresponds to each object in the 'hourly' array
import java.util.List;
public class HourlyForecast {
    public long dt; // timestamp
    public double temp;
    public List<Weather> weather;
}