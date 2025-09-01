package com.example.mobile_app.models.weather;
// Corresponds to the 'current' object
import java.util.List;
public class CurrentWeather {
    public long sunrise;
    public long sunset;
    public double temp;
    public double pressure;
    public int humidity;
    public double uvi;
    public int visibility;
    public List<Weather> weather;
}