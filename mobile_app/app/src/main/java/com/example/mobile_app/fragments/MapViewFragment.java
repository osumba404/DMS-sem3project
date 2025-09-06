package com.example.mobile_app.fragments;

import android.Manifest;
import android.annotation.SuppressLint;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.graphics.Canvas;
import android.graphics.drawable.Drawable;
import android.net.Uri;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.Fragment;

import com.example.mobile_app.BuildConfig;
import com.example.mobile_app.R;
import com.example.mobile_app.models.DisasterAlert;
import com.example.mobile_app.models.MapData;
import com.example.mobile_app.models.MapDataResponse;
import com.example.mobile_app.models.Shelter;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.dialog.MaterialAlertDialogBuilder;
import com.google.android.material.floatingactionbutton.FloatingActionButton;

import org.osmdroid.api.IMapController;
import org.osmdroid.config.Configuration;
import org.osmdroid.tileprovider.tilesource.TileSourceFactory;
import org.osmdroid.util.GeoPoint;
import org.osmdroid.views.MapView;
import org.osmdroid.views.overlay.ItemizedIconOverlay;
import org.osmdroid.views.overlay.OverlayItem;
import org.osmdroid.views.overlay.mylocation.GpsMyLocationProvider;
import org.osmdroid.views.overlay.mylocation.MyLocationNewOverlay;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.List;
import java.util.Locale;
import java.time.OffsetDateTime;
import java.util.Date;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MapViewFragment extends Fragment {

    private MapView map;
    private FusedLocationProviderClient fusedLocationClient;
    private ApiService apiService;
    private IMapController mapController;
    private MyLocationNewOverlay myLocationOverlay;
    private ProgressBar progressBar;
    private ItemizedIconOverlay<OverlayItem> shelterOverlay;
    private ItemizedIconOverlay<OverlayItem> disasterOverlay;
    private static final int REQUEST_LOCATION_PERMISSION = 1;
    private List<Shelter> shelters;
    private List<DisasterAlert> disasters;

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        // Initialize OSMDroid configuration
        Configuration.getInstance().load(requireContext(), requireContext().getSharedPreferences("osm_prefs", 0));
        Configuration.getInstance().setUserAgentValue(BuildConfig.APPLICATION_ID);
    }

    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_map_view, container, false);

        // Initialize map
        map = view.findViewById(R.id.map);
        progressBar = view.findViewById(R.id.progressBar);

        // Initialize services
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(requireActivity());
        apiService = ApiClient.getApiService();

        // Initialize FAB with the correct ID from your layout
        FloatingActionButton fabMyLocation = view.findViewById(R.id.fab_my_location);
        fabMyLocation.setOnClickListener(v -> centerOnMyLocation());

        initializeMap();
        return view;
    }

    private void initializeMap() {
        // Configure map
        map.setTileSource(TileSourceFactory.MAPNIK);
        map.setMultiTouchControls(true);
        mapController = map.getController();
        mapController.setZoom(12.0);

        // Add my location overlay
        myLocationOverlay = new MyLocationNewOverlay(new GpsMyLocationProvider(requireContext()), map);
        myLocationOverlay.enableMyLocation();
        myLocationOverlay.enableFollowLocation();
        myLocationOverlay.runOnFirstFix(this::centerOnMyLocation);
        map.getOverlays().add(myLocationOverlay);

        // Request location permission if not granted
        if (checkLocationPermission()) {
            centerOnMyLocation();
        } else {
            requestLocationPermission();
        }
    }

    private boolean checkLocationPermission() {
        return ContextCompat.checkSelfPermission(requireContext(),
                Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED;
    }

    private void requestLocationPermission() {
        requestPermissions(
                new String[]{Manifest.permission.ACCESS_FINE_LOCATION},
                REQUEST_LOCATION_PERMISSION
        );
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        if (requestCode == REQUEST_LOCATION_PERMISSION) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                centerOnMyLocation();
            } else {
                // Default to Nairobi if permission denied
                mapController.setCenter(new GeoPoint(-1.286389, 36.817223));
                mapController.setZoom(10.0);
            }
        }
    }

    @SuppressLint("MissingPermission")
    private void centerOnMyLocation() {
        if (checkLocationPermission()) {
            fusedLocationClient.getLastLocation().addOnSuccessListener(requireActivity(), location -> {
                if (location != null) {
                    GeoPoint myLocation = new GeoPoint(location.getLatitude(), location.getLongitude());
                    mapController.animateTo(myLocation);
                    fetchMapData(location.getLatitude(), location.getLongitude());
                }
            });
        }
    }

    private void fetchMapData(double lat, double lon) {
        progressBar.setVisibility(View.VISIBLE);

        int searchRadiusKm = 50; // Default search radius of 50km
        apiService.getMapData(lat, lon, searchRadiusKm).enqueue(new Callback<MapDataResponse>() {
            @Override
            public void onResponse(@NonNull Call<MapDataResponse> call, @NonNull Response<MapDataResponse> response) {
                progressBar.setVisibility(View.GONE);

                if (response.isSuccessful() && response.body() != null) {
                    MapData mapData = response.body().getData();
                    if (mapData != null) {
                        shelters = mapData.getShelters();
                        disasters = mapData.getDisasters();
                        if (shelters != null && !shelters.isEmpty()) {
                            addShelterMarkers(shelters);
                        }
                        if (disasters != null && !disasters.isEmpty()) {
                            addDisasterMarkers(disasters);
                        }
                    }
                }
            }

            @Override
            public void onFailure(@NonNull Call<MapDataResponse> call, @NonNull Throwable t) {
                progressBar.setVisibility(View.GONE);
                Toast.makeText(requireContext(), "Failed to load map data", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void addShelterMarkers(List<Shelter> shelters) {
        if (shelters == null || map == null) return;

        List<OverlayItem> items = new ArrayList<>();
        for (Shelter shelter : shelters) {
            OverlayItem item = new OverlayItem(shelter.getName(), shelter.getAddress(), new GeoPoint(shelter.getLatitude(), shelter.getLongitude()));

            // Set the shelter marker icon
            Drawable marker = ContextCompat.getDrawable(requireContext(), R.drawable.ic_shelter_marker);
            if (marker != null) {
                marker = marker.mutate();
                marker.setBounds(0, 0, marker.getIntrinsicWidth(), marker.getIntrinsicHeight());
                item.setMarker(marker);
            }

            items.add(item);
        }

        // Remove existing overlay if it exists
        if (shelterOverlay != null) {
            map.getOverlays().remove(shelterOverlay);
        }

        // Create and add new overlay
        shelterOverlay = new ItemizedIconOverlay<>(items, new ItemizedIconOverlay.OnItemGestureListener<OverlayItem>() {
            @Override
            public boolean onItemSingleTapUp(int index, OverlayItem item) {
                return false;
            }

            @Override
            public boolean onItemLongPress(int index, OverlayItem item) {
                showShelterDetails(shelters.get(index));
                return true;
            }
        }, requireContext().getApplicationContext());

        map.getOverlays().add(shelterOverlay);
        map.invalidate();
    }

    private void addDisasterMarkers(List<DisasterAlert> disasters) {
        if (disasters == null || map == null) return;

        List<OverlayItem> items = new ArrayList<>();
        for (DisasterAlert disaster : disasters) {
            // Assuming DisasterAlert has getLatitude() and getLongitude() methods
            // If not, you'll need to parse them from the WKT string in affectedArea
            double lat = disaster.getLatitude();
            double lon = disaster.getLongitude();

            OverlayItem item = new OverlayItem(disaster.getName(), disaster.getType(), new GeoPoint(lat, lon));

            // Set appropriate icon based on disaster type
            int iconResId;
            switch (disaster.getType().toLowerCase()) {
                case "flood":
                    iconResId = R.drawable.ic_baseline_flood_24;
                    break;
                case "landslide":
                    iconResId = R.drawable.ic_baseline_terrain_24;
                    break;
                case "fire":
                    iconResId = R.drawable.ic_baseline_local_fire_department_24;
                    break;
                default:
                    iconResId = R.drawable.ic_baseline_warning_24;
            }

            item.setMarker(ContextCompat.getDrawable(requireContext(), iconResId));
            items.add(item);
        }

        // Remove existing overlay if it exists
        if (disasterOverlay != null) {
            map.getOverlays().remove(disasterOverlay);
        }

        // Create and add new overlay
        disasterOverlay = new ItemizedIconOverlay<>(items, new ItemizedIconOverlay.OnItemGestureListener<OverlayItem>() {
            @Override
            public boolean onItemSingleTapUp(int index, OverlayItem item) {
                return false;
            }

            @Override
            public boolean onItemLongPress(int index, OverlayItem item) {
                showDisasterDetails(disasters.get(index));
                return true;
            }
        }, requireContext().getApplicationContext());

        map.getOverlays().add(disasterOverlay);
        map.invalidate();
    }

    private void showShelterDetails(Shelter shelter) {
        View dialogView = LayoutInflater.from(requireContext())
            .inflate(R.layout.dialog_shelter_details, null);

        // Initialize views
        TextView nameView = dialogView.findViewById(R.id.shelterName);
        TextView capacityView = dialogView.findViewById(R.id.shelterCapacity);
        TextView locationView = dialogView.findViewById(R.id.shelterLocation);
        ImageView iconFood = dialogView.findViewById(R.id.iconFood);
        ImageView iconWater = dialogView.findViewById(R.id.iconWater);
        ImageView iconMedical = dialogView.findViewById(R.id.iconMedical);
        ImageView iconBlankets = dialogView.findViewById(R.id.iconBlankets);
        MaterialButton btnGetDirections = dialogView.findViewById(R.id.btnGetDirections);

        // Set data
        nameView.setText(shelter.getName());
        
        // Calculate occupancy percentage safely
        double occupancyPercentage = shelter.getCapacity() > 0 ? 
            (shelter.getCurrentOccupancy() * 100.0) / shelter.getCapacity() : 0;
            
        capacityView.setText(String.format(Locale.getDefault(), 
            "%d/%d people (%.0f%% full)", 
            shelter.getCurrentOccupancy(), 
            shelter.getCapacity(),
            occupancyPercentage));
            
        locationView.setText(shelter.getAddress());

        // Show/hide supply icons based on availability
        if (shelter.getFoodSupply() != null && !shelter.getFoodSupply().isEmpty()) {
            iconFood.setVisibility(View.VISIBLE);
        }
        if (shelter.getWaterSupply() != null && !shelter.getWaterSupply().isEmpty()) {
            iconWater.setVisibility(View.VISIBLE);
        }
        if (shelter.getMedicalSupply() != null && !shelter.getMedicalSupply().isEmpty()) {
            iconMedical.setVisibility(View.VISIBLE);
        }
        if (shelter.getBlanketsAvailable() > 0) {
            iconBlankets.setVisibility(View.VISIBLE);
        }

        // Set up directions button
        btnGetDirections.setOnClickListener(v -> {
            // Open Google Maps with directions to the shelter
            String uri = String.format(Locale.ENGLISH,
                "google.navigation:q=%f,%f",
                shelter.getLatitude(),
                shelter.getLongitude());

            Intent mapIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(uri));
            mapIntent.setPackage("com.google.android.apps.maps");

            // Verify that the intent will resolve to an activity
            if (mapIntent.resolveActivity(requireActivity().getPackageManager()) != null) {
                startActivity(mapIntent);
            } else {
                // If Google Maps is not installed, open in browser
                String url = String.format(Locale.ENGLISH,
                    "https://www.google.com/maps/dir/?api=1&destination=%f,%f",
                    shelter.getLatitude(),
                    shelter.getLongitude());
                Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
                startActivity(browserIntent);
            }
        });

        // Show dialog
        new MaterialAlertDialogBuilder(requireContext())
            .setView(dialogView)
            .setPositiveButton("Close", null)
            .show();
    }

    private void showDisasterDetails(DisasterAlert disaster) {
        View dialogView = LayoutInflater.from(requireContext())
            .inflate(R.layout.dialog_disaster_details, null);

        // Initialize views
        TextView titleView = dialogView.findViewById(R.id.disasterTitle);
        TextView severityView = dialogView.findViewById(R.id.disasterSeverity);
        ImageView iconView = dialogView.findViewById(R.id.disasterIcon);
        TextView locationView = dialogView.findViewById(R.id.disasterLocation);
        TextView timeView = dialogView.findViewById(R.id.disasterTime);
        TextView descriptionView = dialogView.findViewById(R.id.disasterDescription);
        MaterialButton btnViewOnMap = dialogView.findViewById(R.id.btnViewOnMap);

        // Set disaster data
        titleView.setText(disaster.getName());
        String severity = disaster.getSeverity().toLowerCase();
        severityView.setText(severity);

        // Set severity background color
        int bgColorResId;
        switch (severity) {
            case "high":
                bgColorResId = R.color.accent_danger;
                break;
            case "medium":
                bgColorResId = R.color.accent_warning;
                break;
            case "low":
            default:
                bgColorResId = R.color.accent_info;
        }
        severityView.setBackgroundColor(ContextCompat.getColor(requireContext(), bgColorResId));

        // Set icon based on disaster type (you can expand this switch with more types)
        int iconResId;
        switch (disaster.getType().toLowerCase()) {
            case "flood":
                iconResId = R.drawable.ic_baseline_flood_24;
                break;
            case "earthquake":
                iconResId = R.drawable.ic_baseline_terrain_24;
                break;
            case "fire":
                iconResId = R.drawable.ic_baseline_local_fire_department_24;
                break;
            default:
                iconResId = R.drawable.ic_baseline_warning_24;
        }
        iconView.setImageResource(iconResId);

        locationView.setText(String.format("%.6f, %.6f",
                disaster.getLatitude(),
                disaster.getLongitude()));

        // Format the timestamp
        SimpleDateFormat sdf = new SimpleDateFormat("MMM d, yyyy h:mm a", Locale.getDefault());
        if (disaster.getCreatedAt() != null && !disaster.getCreatedAt().isEmpty()) {
            try {
                // Parse the ISO 8601 timestamp
                OffsetDateTime dateTime = OffsetDateTime.parse(disaster.getCreatedAt());
                timeView.setText(sdf.format(Date.from(dateTime.toInstant())));
            } catch (Exception e) {
                timeView.setText("N/A");
                e.printStackTrace();
            }
        } else {
            timeView.setText("Time not available");
        }

        descriptionView.setText(disaster.getDescription() != null ?
                disaster.getDescription() : "No additional details available.");

        // Create and show the dialog
        AlertDialog dialog = new MaterialAlertDialogBuilder(requireContext())
                .setView(dialogView)
                .setPositiveButton("Close", null)
                .create();

        // Handle view on map button click
        btnViewOnMap.setOnClickListener(v -> {
            // Center the map on the disaster location
            GeoPoint point = new GeoPoint(disaster.getLatitude(), disaster.getLongitude());
            mapController.animateTo(point);
            mapController.setZoom(15.0);
            dialog.dismiss();
        });

        dialog.show();
    }

    @Override
    public void onResume() {
        super.onResume();
        map.onResume();
    }

    @Override
    public void onPause() {
        super.onPause();
        map.onPause();
    }
}