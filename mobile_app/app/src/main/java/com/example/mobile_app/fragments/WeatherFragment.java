package com.example.mobile_app.fragments;

import android.Manifest;
import android.annotation.SuppressLint;
import android.content.pm.PackageManager;
import android.location.Address;
import android.location.Geocoder;
import android.location.Location;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.Fragment;

import com.example.mobile_app.R;
import com.example.mobile_app.models.weather.CurrentWeather;
import com.example.mobile_app.models.weather.HourlyForecast;
import com.example.mobile_app.models.weather.WeatherResponse;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;

import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;
import java.util.TimeZone;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class WeatherFragment extends Fragment {

    // UI Elements
    private ProgressBar progressBar;
    private LinearLayout weatherContentView;
    private TextView tvLocationName, tvTemperature, tvWeatherDescription, tvHumidity,
            tvPressure, tvVisibility, tvUvIndex, tvSunrise, tvSunset,
            tvAdverseWeatherWarning, tvHourlyForecast;

    private FusedLocationProviderClient fusedLocationClient;
    private ApiService apiService;

    public WeatherFragment() {
        // Required empty public constructor
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        return inflater.inflate(R.layout.fragment_weather, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        initializeViews(view);
        apiService = ApiClient.getApiService();
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(requireActivity());

        checkPermissionAndFetchWeather();
    }

    private void initializeViews(@NonNull View view) {
        progressBar = view.findViewById(R.id.progress_bar_weather);
        weatherContentView = view.findViewById(R.id.weather_content_view);
        tvLocationName = view.findViewById(R.id.tv_location_name);
        tvTemperature = view.findViewById(R.id.tv_temperature);
        tvWeatherDescription = view.findViewById(R.id.tv_weather_description);
        tvHumidity = view.findViewById(R.id.tv_humidity);
        tvPressure = view.findViewById(R.id.tv_pressure);
        tvVisibility = view.findViewById(R.id.tv_visibility);
        tvUvIndex = view.findViewById(R.id.tv_uv_index);
        tvSunrise = view.findViewById(R.id.tv_sunrise);
        tvSunset = view.findViewById(R.id.tv_sunset);
        tvAdverseWeatherWarning = view.findViewById(R.id.tv_adverse_weather_warning);
        tvHourlyForecast = view.findViewById(R.id.tv_hourly_forecast);
    }

    private void checkPermissionAndFetchWeather() {
        if (ContextCompat.checkSelfPermission(requireContext(), Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {
            getCurrentLocationAndFetchWeather();
        } else {
            // We rely on MainActivity to have asked for permission.
            // If we land here, it means it was denied.
            progressBar.setVisibility(View.GONE);
            Toast.makeText(getContext(), "Location permission is needed to show weather.", Toast.LENGTH_LONG).show();
        }
    }

    @SuppressLint("MissingPermission")
    private void getCurrentLocationAndFetchWeather() {
        progressBar.setVisibility(View.VISIBLE);
        weatherContentView.setVisibility(View.GONE);

        fusedLocationClient.getLastLocation().addOnSuccessListener(requireActivity(), location -> {
            if (location != null) {
                fetchWeatherData(location.getLatitude(), location.getLongitude());
                updateLocationName(location);
            } else {
                progressBar.setVisibility(View.GONE);
                Toast.makeText(getContext(), "Could not retrieve location. Please ensure GPS is enabled.", Toast.LENGTH_LONG).show();
            }
        });
    }

    private void fetchWeatherData(double lat, double lon) {
        apiService.getWeatherData(lat, lon).enqueue(new Callback<WeatherResponse>() {
            @Override
            public void onResponse(@NonNull Call<WeatherResponse> call, @NonNull Response<WeatherResponse> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null) {
                    weatherContentView.setVisibility(View.VISIBLE);
                    populateUI(response.body());
                } else {
                    Toast.makeText(getContext(), "Failed to fetch weather data.", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<WeatherResponse> call, @NonNull Throwable t) {
                progressBar.setVisibility(View.GONE);
                Toast.makeText(getContext(), "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void updateLocationName(Location location) {
        // Geocoder converts lat/lon to a human-readable address
        Geocoder geocoder = new Geocoder(getContext(), Locale.getDefault());
        try {
            List<Address> addresses = geocoder.getFromLocation(location.getLatitude(), location.getLongitude(), 1);
            if (addresses != null && !addresses.isEmpty()) {
                String cityName = addresses.get(0).getLocality();
                String countryCode = addresses.get(0).getCountryCode();
                if (cityName != null) {
                    tvLocationName.setText(cityName + ", " + countryCode);
                } else {
                    tvLocationName.setText("Current Location");
                }
            }
        } catch (IOException e) {
            tvLocationName.setText("Current Location");
        }
    }

    private void populateUI(WeatherResponse weatherData) {
        CurrentWeather current = weatherData.current;
        if (current == null) return;

        tvTemperature.setText(String.format(Locale.getDefault(), "%.0f°C", current.temp));
        if (current.weather != null && !current.weather.isEmpty()) {
            tvWeatherDescription.setText(current.weather.get(0).description);
        }
        tvHumidity.setText(String.format(Locale.getDefault(), "Humidity: %d%%", current.humidity));
        tvPressure.setText(String.format(Locale.getDefault(), "Pressure: %.0f hPa", current.pressure));
        tvVisibility.setText(String.format(Locale.getDefault(), "Visibility: %.1f km", current.visibility / 1000.0));
        tvUvIndex.setText(String.format(Locale.getDefault(), "UV Index: %.1f", current.uvi));
        tvSunrise.setText("Sunrise: " + formatTimestamp(current.sunrise, weatherData.timezone));
        tvSunset.setText("Sunset: " + formatTimestamp(current.sunset, weatherData.timezone));

        // Handle Hourly Forecast
        populateHourlyForecast(weatherData.hourly, weatherData.timezone);
    }

    private void populateHourlyForecast(List<HourlyForecast> hourly, String timezone) {
        if (hourly == null || hourly.isEmpty()) return;

        StringBuilder pastText = new StringBuilder("Last 24 hrs (coming soon)\n");
        StringBuilder futureText = new StringBuilder("Next 24 hrs\n");
        String adverseWeatherInfo = "";

        // OpenWeatherMap's 'hourly' array contains the past hour and the next 47 hours.
        // We'll just focus on the future for now.
        for (int i = 0; i < hourly.size() && i < 25; i += 4) {
            if (i == 0) continue; // Skip the current hour which is already displayed
            HourlyForecast forecast = hourly.get(i);
            String time = formatTimestamp(forecast.dt, timezone);
            String description = forecast.weather.get(0).description;
            futureText.append(String.format(Locale.getDefault(), "%s: %.0f°C, %s\n", time, forecast.temp, description));

            // Find the first instance of adverse weather to warn the user
            if (adverseWeatherInfo.isEmpty() && isAdverse(description)) {
                adverseWeatherInfo = "Adverse Weather Alert: " + description + " expected around " + time;
            }
        }

        tvHourlyForecast.setText(futureText.toString().trim());

        if (!adverseWeatherInfo.isEmpty()) {
            tvAdverseWeatherWarning.setText(adverseWeatherInfo);
            tvAdverseWeatherWarning.setVisibility(View.VISIBLE);
        } else {
            tvAdverseWeatherWarning.setVisibility(View.GONE);
        }
    }

    private String formatTimestamp(long timestamp, String timezone) {
        try {
            Date date = new Date(timestamp * 1000L);
            SimpleDateFormat sdf = new SimpleDateFormat("h:mm a", Locale.getDefault());
            sdf.setTimeZone(TimeZone.getTimeZone(timezone));
            return sdf.format(date);
        } catch (Exception e) {
            return "";
        }
    }

    private boolean isAdverse(String description) {
        if (description == null) return false;
        String desc = description.toLowerCase();
        return desc.contains("rain") || desc.contains("storm") || desc.contains("thunder") || desc.contains("snow") || desc.contains("squall");
    }
}