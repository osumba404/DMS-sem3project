package com.example.mobile_app.fragments;

import android.Manifest;
import android.annotation.SuppressLint; // <-- IMPORTANT: Make sure this is imported
import android.content.pm.PackageManager;
import android.location.Location;
import android.os.Bundle;
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
import com.example.mobile_app.models.Shelter;
import com.example.mobile_app.models.SheltersResponse;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MarkerOptions;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MapViewFragment extends Fragment implements OnMapReadyCallback {

    private GoogleMap mMap;
    private FusedLocationProviderClient fusedLocationClient;
    private ApiService apiService;

    private final ActivityResultLauncher<String> requestPermissionLauncher =
            registerForActivityResult(new ActivityResultContracts.RequestPermission(), isGranted -> {
                if (isGranted) {
                    getCurrentLocation();
                } else {
                    Toast.makeText(getContext(), "Location permission is required to show the map.", Toast.LENGTH_LONG).show();
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

        SupportMapFragment mapFragment = (SupportMapFragment) getChildFragmentManager().findFragmentById(R.id.map);
        if (mapFragment != null) {
            mapFragment.getMapAsync(this);
        }
    }

    @Override
    public void onMapReady(@NonNull GoogleMap googleMap) {
        mMap = googleMap;
        checkLocationPermission();
    }

    private void checkLocationPermission() {
        if (ContextCompat.checkSelfPermission(requireContext(), Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {
            getCurrentLocation();
        } else {
            requestPermissionLauncher.launch(Manifest.permission.ACCESS_FINE_LOCATION);
        }
    }

    // THE FIX: Add this annotation to tell the linter we know what we're doing.
    @SuppressLint("MissingPermission")
    private void getCurrentLocation() {
        // The previous check is still good practice, so we keep it.
        if (ContextCompat.checkSelfPermission(requireContext(), Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            return;
        }

        mMap.setMyLocationEnabled(true); // Warning will now be gone.

        fusedLocationClient.getLastLocation() // Warning will now be gone.
                .addOnSuccessListener(requireActivity(), location -> {
                    if (location != null) {
                        LatLng userLocation = new LatLng(location.getLatitude(), location.getLongitude());
                        mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(userLocation, 12f));
                        fetchNearbyShelters(location.getLatitude(), location.getLongitude());
                    } else {
                        Toast.makeText(getContext(), "Could not get current location. Please ensure location is on.", Toast.LENGTH_LONG).show();
                    }
                });
    }

    private void fetchNearbyShelters(double lat, double lon) {
        apiService.getNearbyShelters(lat, lon).enqueue(new Callback<SheltersResponse>() {
            @Override
            public void onResponse(@NonNull Call<SheltersResponse> call, @NonNull Response<SheltersResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    List<Shelter> shelters = response.body().getData();
                    if (shelters != null) {
                        addSheltersToMap(shelters);
                    }
                } else {
                    Toast.makeText(getContext(), "Failed to fetch shelters.", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<SheltersResponse> call, @NonNull Throwable t) {
                Toast.makeText(getContext(), "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void addSheltersToMap(List<Shelter> shelters) {
        if (mMap == null) return;
        mMap.clear();
        for (Shelter shelter : shelters) {
            LatLng shelterLocation = new LatLng(shelter.getLatitude(), shelter.getLongitude());
            mMap.addMarker(new MarkerOptions()
                    .position(shelterLocation)
                    .title(shelter.getName())
                    .snippet("Capacity: " + shelter.getCurrentOccupancy() + "/" + shelter.getCapacity())
                    .icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_GREEN)));
        }
    }
}