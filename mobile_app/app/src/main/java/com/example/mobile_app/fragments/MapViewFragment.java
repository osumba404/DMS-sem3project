package com.example.mobile_app.fragments;

import android.Manifest;
import android.annotation.SuppressLint;
import android.content.pm.PackageManager;
import android.graphics.Color;
import android.location.Location;
import android.os.Bundle;
import android.os.Looper;
import android.util.Log;
import android.view.View;
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
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.android.gms.maps.model.PolygonOptions;

import java.util.ArrayList;
import java.util.List;
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
        apiService.getMapData(lat, lon).enqueue(new Callback<MapDataResponse>() {
            @Override
            public void onResponse(@NonNull Call<MapDataResponse> call, @NonNull Response<MapDataResponse> response) {
                if (mMap == null) return;
                mMap.clear();
                if (response.isSuccessful() && response.body() != null) {
                    MapData mapData = response.body().getData();
                    if (mapData != null) {
                        if (mapData.getShelters() != null) addSheltersToMap(mapData.getShelters());
                        if (mapData.getDisasters() != null) addDisastersToMap(mapData.getDisasters());
                    }
                } else {
                    Toast.makeText(getContext(), "Failed to fetch map data.", Toast.LENGTH_SHORT).show();
                }
            }
            @Override
            public void onFailure(@NonNull Call<MapDataResponse> call, @NonNull Throwable t) {
                Toast.makeText(getContext(), "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void addSheltersToMap(List<Shelter> shelters) {
        if (mMap == null) return;
        for (Shelter shelter : shelters) {
            LatLng shelterLocation = new LatLng(shelter.getLatitude(), shelter.getLongitude());
            mMap.addMarker(new MarkerOptions().position(shelterLocation).title(shelter.getName()).snippet("Status: " + shelter.getStatus()).icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_AZURE)));
        }
    }

    private void addDisastersToMap(List<DisasterAlert> disasters) {
        if (mMap == null) return;
        for (DisasterAlert disaster : disasters) {
            List<LatLng> polygonPoints = parseWktPolygon(disaster.getAffectedArea());
            if (polygonPoints != null && !polygonPoints.isEmpty()) {
                PolygonOptions polygonOptions = new PolygonOptions().addAll(polygonPoints).strokeColor(Color.RED).strokeWidth(5).fillColor(0x33FF0000);
                mMap.addPolygon(polygonOptions);
                LatLng center = getPolygonCenter(polygonPoints);
                mMap.addMarker(new MarkerOptions().position(center).title(disaster.getName()).snippet("Type: " + disaster.getType()).icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_RED)));
            }
        }
    }

    // --- THE MISSING METHODS ARE HERE ---
    private List<LatLng> parseWktPolygon(String wkt) {
        if (wkt == null || !wkt.toUpperCase().startsWith("POLYGON")) return null;
        List<LatLng> points = new ArrayList<>();
        Pattern pattern = Pattern.compile("(-?\\d+\\.?\\d*)\\s(-?\\d+\\.?\\d*)");
        Matcher matcher = pattern.matcher(wkt);
        while (matcher.find()) {
            double lon = Double.parseDouble(matcher.group(1));
            double lat = Double.parseDouble(matcher.group(2));
            points.add(new LatLng(lat, lon));
        }
        return points;
    }

    private LatLng getPolygonCenter(List<LatLng> polygon) {
        if (polygon == null || polygon.isEmpty()) return new LatLng(0,0);
        double lat = 0.0;
        double lon = 0.0;
        for (LatLng point : polygon) {
            lat += point.latitude;
            lon += point.longitude;
        }
        return new LatLng(lat / polygon.size(), lon / polygon.size());
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