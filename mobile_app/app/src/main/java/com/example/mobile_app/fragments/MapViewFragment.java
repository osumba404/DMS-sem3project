package com.example.mobile_app.fragments;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.content.pm.PackageManager;
import android.graphics.Color;
import android.location.Location;
import android.os.Bundle;
import android.os.Looper;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.Fragment;

import com.example.mobile_app.R;
import com.example.mobile_app.models.DisasterAlert;
import com.example.mobile_app.models.MapData;
import com.example.mobile_app.models.MapDataResponse;
import com.example.mobile_app.models.Shelter;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationCallback;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.location.LocationResult;
import com.google.android.gms.location.LocationServices;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.android.gms.maps.model.Polygon;
import com.google.android.gms.maps.model.PolygonOptions;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;

import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.List;
import java.util.Locale;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MapViewFragment extends Fragment {

    private GoogleMap mMap;
    private FusedLocationProviderClient fusedLocationClient;
    private ApiService apiService;
    private boolean isFirstLocationUpdate = true;
    private LocationCallback locationCallback;

    private final ActivityResultLauncher<String> requestPermissionLauncher =
            registerForActivityResult(new ActivityResultContracts.RequestPermission(), isGranted -> {
                if (isGranted) {
                    initializeMap();
                } else {
                    Toast.makeText(getContext(), "Location permission is required for the map.", Toast.LENGTH_LONG).show();
                }
            });

    public MapViewFragment() {
        super(R.layout.fragment_map_view);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        apiService = ApiClient.getApiService();
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(requireActivity());

        initializeMap();
    }

    private void initializeMap() {
        SupportMapFragment mapFragment = (SupportMapFragment) getChildFragmentManager().findFragmentById(R.id.map);
        if (mapFragment != null) {
            mapFragment.getMapAsync(googleMap -> {
                mMap = googleMap;
                checkLocationPermission();
            });
        } else {
            Log.e("MapViewDebug", "SupportMapFragment not found!");
        }
    }

    private void checkLocationPermission() {
        if (ContextCompat.checkSelfPermission(requireContext(), Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {
            startLocationUpdates();
        } else {
            requestPermissionLauncher.launch(Manifest.permission.ACCESS_FINE_LOCATION);
        }
    }

    @SuppressLint("MissingPermission")
    private void startLocationUpdates() {
        if (mMap == null) return;
        mMap.setMyLocationEnabled(true);
        mMap.getUiSettings().setMyLocationButtonEnabled(true);

        LocationRequest locationRequest = LocationRequest.create();
        locationRequest.setInterval(10000);
        locationRequest.setFastestInterval(5000);
        locationRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);

        locationCallback = new LocationCallback() {
            @Override
            public void onLocationResult(@NonNull LocationResult locationResult) {
                if (locationResult.getLastLocation() != null) {
                    Location location = locationResult.getLastLocation();
                    if (isFirstLocationUpdate) {
                        isFirstLocationUpdate = false;
                        LatLng userLocation = new LatLng(location.getLatitude(), location.getLongitude());
                        mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(userLocation, 10f));
                        fetchMapData(location.getLatitude(), location.getLongitude());
                    }
                }
            }
        };
        fusedLocationClient.requestLocationUpdates(locationRequest, locationCallback, Looper.getMainLooper());
    }

    private void fetchMapData(double lat, double lon) {
        if (mMap == null) return;
        
        // Show loading indicator
        if (getView() != null) {
            ProgressBar progressBar = getView().findViewById(R.id.progressBar);
            if (progressBar != null) {
                progressBar.setVisibility(View.VISIBLE);
            }
        }

        int searchRadiusKm = 50; // Default search radius of 50km
        apiService.getMapData(lat, lon, searchRadiusKm).enqueue(new Callback<MapDataResponse>() {
            @Override
            public void onResponse(@NonNull Call<MapDataResponse> call, @NonNull Response<MapDataResponse> response) {
                // Hide loading indicator
                if (getView() != null) {
                    ProgressBar progressBar = getView().findViewById(R.id.progressBar);
                    if (progressBar != null) {
                        progressBar.setVisibility(View.GONE);
                    }
                }
                
                if (mMap == null) return;
                mMap.clear();
                
                if (response.isSuccessful() && response.body() != null) {
                    MapData mapData = response.body().getData();
                    if (mapData != null) {
                        if (mapData.getShelters() != null && !mapData.getShelters().isEmpty()) {
                            addSheltersToMap(mapData.getShelters());
                        }
                        if (mapData.getDisasters() != null && !mapData.getDisasters().isEmpty()) {
                            addDisastersToMap(mapData.getDisasters());
                        }
                    }
                } else {
                    String errorMessage = "Failed to load map data";
                    if (response.errorBody() != null) {
                        try {
                            errorMessage = response.errorBody().string();
                        } catch (IOException e) {
                            Log.e("MapViewFragment", "Error reading error body", e);
                        }
                    }
                    Toast.makeText(getContext(), errorMessage, Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<MapDataResponse> call, @NonNull Throwable t) {
                // Hide loading indicator
                if (getView() != null) {
                    ProgressBar progressBar = getView().findViewById(R.id.progressBar);
                    if (progressBar != null) {
                        progressBar.setVisibility(View.GONE);
                    }
                }
                
                String errorMessage = "Network error: " + t.getMessage();
                Log.e("MapViewFragment", errorMessage, t);
                Toast.makeText(getContext(), errorMessage, Toast.LENGTH_LONG).show();
            }
        });
    }

    private void addSheltersToMap(List<Shelter> shelters) {
        if (mMap == null) return;
        
        // Set a custom info window adapter for better styling
        mMap.setInfoWindowAdapter(new GoogleMap.InfoWindowAdapter() {
            @Override
            public View getInfoWindow(Marker marker) {
                return null; // Use default info window frame
            }

            @Override
            public View getInfoContents(Marker marker) {
                View infoWindow = getLayoutInflater().inflate(R.layout.custom_shelter_info_window, null);
                
                TextView title = infoWindow.findViewById(R.id.title);
                TextView snippet = infoWindow.findViewById(R.id.snippet);
                TextView availability = infoWindow.findViewById(R.id.availability);
                
                title.setText(marker.getTitle());
                snippet.setText(marker.getSnippet());
                
                // Set availability text with color coding
                Object tag = marker.getTag();
                if (tag != null && tag instanceof Shelter) {
                    Shelter shelter = (Shelter) tag;
                    String availabilityText = String.format("%.0f%% available • %.1f km away", 
                        shelter.getAvailabilityPercentage(), 
                        shelter.getDistanceKm());
                    availability.setText(availabilityText);
                    
                    // Set text color based on availability
                    int color = ContextCompat.getColor(requireContext(), 
                        shelter.getAvailabilityPercentage() > 20 ? 
                        R.color.success : R.color.warning);
                    availability.setTextColor(color);
                }
                
                return infoWindow;
            }
        });
        
        // Add markers for each shelter
        for (Shelter shelter : shelters) {
            LatLng shelterLocation = new LatLng(shelter.getLatitude(), shelter.getLongitude());
            Marker marker = mMap.addMarker(new MarkerOptions()
                .position(shelterLocation)
                .title(shelter.getName())
                .snippet(shelter.getInfoSnippet())
                .icon(BitmapDescriptorFactory.defaultMarker(
                    shelter.getStatus().equalsIgnoreCase("Open") ? 
                    BitmapDescriptorFactory.HUE_GREEN : 
                    BitmapDescriptorFactory.HUE_RED
                ))
            );
            
            // Store the shelter object in the marker's tag for later reference
            if (marker != null) {
                marker.setTag(shelter);
            }
        }
        
        // Set a click listener for the info window
        mMap.setOnInfoWindowClickListener(marker -> {
            // Handle marker click if needed
            Object tag = marker.getTag();
            if (tag != null && tag instanceof Shelter) {
                Shelter shelter = (Shelter) tag;
                // You can add navigation to a detailed view here
                Toast.makeText(requireContext(), "Selected: " + shelter.getName(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void addDisastersToMap(List<DisasterAlert> disasters) {
        if (mMap == null) return;
        
        // Set a custom info window adapter for disaster markers
        mMap.setInfoWindowAdapter(new GoogleMap.InfoWindowAdapter() {
            @Override
            public View getInfoWindow(Marker marker) {
                return null; // Use default info window frame
            }

            @Override
            public View getInfoContents(Marker marker) {
                View infoWindow = getLayoutInflater().inflate(R.layout.custom_disaster_info_window, null);
                
                TextView title = infoWindow.findViewById(R.id.title);
                TextView type = infoWindow.findViewById(R.id.type);
                TextView severity = infoWindow.findViewById(R.id.severity);
                TextView description = infoWindow.findViewById(R.id.description);
                TextView date = infoWindow.findViewById(R.id.date);
                
                title.setText(marker.getTitle());
                
                Object tag = marker.getTag();
                if (tag != null && tag instanceof DisasterAlert) {
                    DisasterAlert disaster = (DisasterAlert) tag;
                    
                    type.setText(String.format("Type: %s", disaster.getType()));
                    severity.setText(String.format("Severity: %s", disaster.getSeverity()));
                    description.setText(disaster.getDescription());
                    
                    // Format date
                    if (disaster.getCreatedAt() != null) {
                        SimpleDateFormat sdf = new SimpleDateFormat("MMM d, yyyy hh:mm a", Locale.getDefault());
                        String dateStr = sdf.format(disaster.getCreatedAt());
                        date.setText(dateStr);
                    } else {
                        date.setVisibility(View.GONE);
                    }
                    
                    // Set severity color
                    int severityColor = getSeverityColor(disaster.getSeverity());
                    severity.setTextColor(severityColor);
                }
                
                return infoWindow;
            }
        });
        
        // Add markers and polygons for each disaster
        for (DisasterAlert disaster : disasters) {
            List<LatLng> polygonPoints = parseWktPolygon(disaster.getAffectedArea());
            if (polygonPoints != null && !polygonPoints.isEmpty()) {
                // Create a polygon for the affected area
                int strokeColor = getSeverityColor(disaster.getSeverity());
                int fillColor = Color.argb(70, Color.red(strokeColor), Color.green(strokeColor), Color.blue(strokeColor));
                
                PolygonOptions polygonOptions = new PolygonOptions()
                    .addAll(polygonPoints)
                    .strokeColor(strokeColor)
                    .strokeWidth(5)
                    .fillColor(fillColor);
                
                mMap.addPolygon(polygonOptions);
                
                // Add a marker at the center of the affected area
                LatLng center = getPolygonCenter(polygonPoints);
                Marker marker = mMap.addMarker(new MarkerOptions()
                    .position(center)
                    .title(disaster.getName())
                    .snippet(disaster.getDescription())
                    .icon(BitmapDescriptorFactory.defaultMarker(
                        getSeverityHue(disaster.getSeverity())
                    ))
                );
                
                if (marker != null) {
                    marker.setTag(disaster);
                }
            }
        }
        
        // Set a click listener for the info window
        mMap.setOnInfoWindowClickListener(marker -> {
            Object tag = marker.getTag();
            if (tag != null && tag instanceof DisasterAlert) {
                DisasterAlert disaster = (DisasterAlert) tag;
                // You can add navigation to a detailed view here
                showDisasterDetails(disaster);
            }
        });
    }
    
    private int getSeverityColor(String severity) {
        if (severity == null) return Color.GRAY;
        
        switch (severity.toLowerCase(Locale.US)) {
            case "high":
                return ContextCompat.getColor(requireContext(), R.color.accent_danger);
            case "medium":
                return ContextCompat.getColor(requireContext(), R.color.warning);
            case "low":
                return ContextCompat.getColor(requireContext(), R.color.success);
            default:
                return Color.GRAY;
        }
    }
    
    private float getSeverityHue(String severity) {
        if (severity == null) return BitmapDescriptorFactory.HUE_YELLOW;
        
        switch (severity.toLowerCase(Locale.US)) {
            case "high":
                return BitmapDescriptorFactory.HUE_RED;
            case "medium":
                return BitmapDescriptorFactory.HUE_ORANGE;
            case "low":
                return BitmapDescriptorFactory.HUE_GREEN;
            default:
                return BitmapDescriptorFactory.HUE_YELLOW;
        }
    }
    
    private void showDisasterDetails(DisasterAlert disaster) {
        if (disaster == null || getContext() == null) return;
        
        try {
            // Create and show a dialog with disaster details
            AlertDialog.Builder builder = new AlertDialog.Builder(requireContext());
            builder.setTitle(disaster.getName())
                .setMessage(String.format(Locale.US,
                    "Type: %s\n" +
                    "Severity: %s\n" +
                    "Status: %s\n" +
                    "\n%s",
                    disaster.getType() != null ? disaster.getType() : "N/A",
                    disaster.getSeverity() != null ? disaster.getSeverity() : "N/A",
                    disaster.getStatus() != null ? disaster.getStatus() : "N/A",
                    disaster.getDescription() != null ? disaster.getDescription() : "No description available"
                ))
                .setPositiveButton("OK", (dialog, which) -> dialog.dismiss())
                .show();
        } catch (Exception e) {
            Log.e("MapViewFragment", "Error showing disaster details", e);
            Toast.makeText(getContext(), "Error showing disaster details", Toast.LENGTH_SHORT).show();
        }
    }

    private List<LatLng> parseWktPolygon(String wkt) {
        List<LatLng> points = new ArrayList<>();
        if (wkt == null || wkt.isEmpty()) return points;
        
        try {
            // Handle both POLYGON and MULTIPOLYGON formats
            String processedWkt = wkt.toUpperCase(Locale.US);
            if (!processedWkt.contains("POLYGON")) return points;
            
            // Extract coordinates string
            Pattern pattern = Pattern.compile("\\(([^)]+)\\)");
            Matcher matcher = pattern.matcher(wkt);
            
            while (matcher.find()) {
                String coords = matcher.group(1);
                // Split by comma and optional whitespace
                String[] coordPairs = coords.split("\\s*,\\s*");
                
                for (String pair : coordPairs) {
                    String[] xy = pair.trim().split("\\s+");
                    if (xy.length >= 2) {
                        try {
                            double lon = Double.parseDouble(xy[0]);
                            double lat = Double.parseDouble(xy[1]);
                            points.add(new LatLng(lat, lon));
                        } catch (NumberFormatException e) {
                            Log.e("MapViewFragment", "Error parsing coordinate: " + pair, e);
                        }
                    }
                }
                
                // For now, only process the first polygon if it's a MULTIPOLYGON
                if (processedWkt.startsWith("MULTIPOLYGON")) {
                    break;
                }
            }
        } catch (Exception e) {
            Log.e("MapViewFragment", "Error parsing WKT polygon", e);
        }
        
        return points;
    }

    private LatLng getPolygonCenter(List<LatLng> polygon) {
        if (polygon == null || polygon.isEmpty()) return new LatLng(0, 0);
        
        try {
            double lat = 0.0;
            double lon = 0.0;
            int count = 0;
            
            for (LatLng point : polygon) {
                if (point != null) {
                    lat += point.latitude;
                    lon += point.longitude;
                    count++;
                }
            }
            
            if (count > 0) {
                return new LatLng(lat / count, lon / count);
            }
        } catch (Exception e) {
            Log.e("MapViewFragment", "Error calculating polygon center", e);
        }
        
        // Return a default location if something goes wrong
        return new LatLng(0, 0);
    }

    @Override
    public void onPause() {
        super.onPause();
        if (fusedLocationClient != null && locationCallback != null) {
            fusedLocationClient.removeLocationUpdates(locationCallback);
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        if (mMap != null) {
            isFirstLocationUpdate = true;
            // We re-check permission here in case the user revoked it while the app was paused
            checkLocationPermission();
        }
    }
}