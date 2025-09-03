package com.example.mobile_app.activities;

import android.Manifest;
import android.content.pm.PackageManager;
import android.location.Location;
import android.os.Bundle;
import android.view.MenuItem;
import android.webkit.JavascriptInterface;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.app.ActivityCompat;

import com.example.mobile_app.R;
import com.example.mobile_app.models.AlertsResponse;
import com.example.mobile_app.models.DisasterAlert;
import com.example.mobile_app.models.MapDataResponse;
import com.example.mobile_app.models.MapData;
import com.example.mobile_app.models.Shelter;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;
import com.google.gson.Gson;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MapActivity extends AppCompatActivity {
    
    private static final int LOCATION_PERMISSION_REQUEST_CODE = 1001;
    
    private WebView webView;
    private FusedLocationProviderClient fusedLocationClient;
    private ApiService apiService;
    
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_map);
        
        setupToolbar();
        setupWebView();
        setupLocationClient();
        setupApiService();
        
        loadMapData();
    }
    
    private void setupToolbar() {
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle("Disaster Map");
        }
    }
    
    private void setupWebView() {
        webView = findViewById(R.id.webView);
        
        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);
        webSettings.setDomStorageEnabled(true);
        webSettings.setGeolocationEnabled(true);
        webSettings.setAllowFileAccess(true);
        webSettings.setAllowContentAccess(true);
        
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                // Map is loaded, now get user location and load data
                getUserLocation();
            }
        });
        
        // Add JavaScript interface for communication between WebView and Android
        webView.addJavascriptInterface(new WebAppInterface(), "Android");
        
        // Load the Leaflet map HTML file
        webView.loadUrl("file:///android_asset/leaflet_map.html");
    }
    
    private void setupLocationClient() {
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this);
    }
    
    private void setupApiService() {
        apiService = ApiClient.getApiService();
    }
    
    private void getUserLocation() {
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) 
                != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(this, 
                new String[]{Manifest.permission.ACCESS_FINE_LOCATION}, 
                LOCATION_PERMISSION_REQUEST_CODE);
            return;
        }
        
        fusedLocationClient.getLastLocation()
            .addOnSuccessListener(this, location -> {
                if (location != null) {
                    // Update map with user location
                    webView.evaluateJavascript(
                        String.format("AndroidInterface.setUserLocation(%f, %f)", 
                            location.getLatitude(), location.getLongitude()), 
                        null
                    );
                }
            })
            .addOnFailureListener(this, e -> {
                Toast.makeText(this, "Failed to get location", Toast.LENGTH_SHORT).show();
            });
    }
    
    private void loadMapData() {
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) 
                == PackageManager.PERMISSION_GRANTED) {
            
            fusedLocationClient.getLastLocation()
                .addOnSuccessListener(this, location -> {
                    if (location != null) {
                        // Default radius of 50km
                        int searchRadiusKm = 50;
                        
                        // Use map data API that includes shelters
                        Call<MapDataResponse> call = apiService.getMapData(
                            location.getLatitude(), 
                            location.getLongitude(),
                            searchRadiusKm
                        );
                        
                        call.enqueue(new Callback<MapDataResponse>() {
                            @Override
                            public void onResponse(Call<MapDataResponse> call, Response<MapDataResponse> response) {
                                if (response.isSuccessful() && response.body() != null) {
                                    MapDataResponse mapData = response.body();
                                    MapData data = mapData.getData();
                                    
                                    // Load shelters from map data
                                    if (data != null && data.getShelters() != null) {
                                        Gson gson = new Gson();
                                        String sheltersJson = gson.toJson(data.getShelters());
                                        
                                        webView.evaluateJavascript(
                                            String.format("AndroidInterface.setRescueCenters(%s)", sheltersJson), 
                                            null
                                        );
                                    }
                                    
                                    // Handle disasters if needed
                                    if (data != null && data.getDisasters() != null) {
                                        createAffectedZones(data.getDisasters());
                                    }
                                } else {
                                    Toast.makeText(MapActivity.this, 
                                        "Failed to load map data", 
                                        Toast.LENGTH_SHORT).show();
                                }
                            }
                            
                            @Override
                            public void onFailure(Call<MapDataResponse> call, Throwable t) {
                                Toast.makeText(MapActivity.this, 
                                    "Network error: " + t.getMessage(), 
                                    Toast.LENGTH_SHORT).show();
                            }
                        });
                    }
                });
        } else {
            // Request location permission if not granted
            ActivityCompat.requestPermissions(this,
                new String[]{Manifest.permission.ACCESS_FINE_LOCATION},
                LOCATION_PERMISSION_REQUEST_CODE);
        }
    }
    
    private void createAffectedZones(List<DisasterAlert> disasters) {
        // This method can be enhanced later when coordinate data is available
        // For now, we'll just log the disasters
        if (disasters != null && !disasters.isEmpty()) {
            Gson gson = new Gson();
            String disastersJson = gson.toJson(disasters);
            
            // Pass empty array for now, can be updated when we have zone data
            webView.evaluateJavascript(
                String.format("AndroidInterface.setAffectedZones(%s)", "[]"), 
                null
            );
        }
    }
    
    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, 
                                         @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        
        if (requestCode == LOCATION_PERMISSION_REQUEST_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                getUserLocation();
            } else {
                Toast.makeText(this, "Location permission required for map features", Toast.LENGTH_LONG).show();
            }
        }
    }
    
    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        if (item.getItemId() == android.R.id.home) {
            onBackPressed();
            return true;
        }
        return super.onOptionsItemSelected(item);
    }
    
    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            super.onBackPressed();
        }
    }
    
    // JavaScript interface for WebView communication
    public class WebAppInterface {
        @JavascriptInterface
        public void showToast(String message) {
            runOnUiThread(() -> Toast.makeText(MapActivity.this, message, Toast.LENGTH_SHORT).show());
        }
        
        @JavascriptInterface
        public void requestLocation() {
            runOnUiThread(() -> getUserLocation());
        }
    }
}
