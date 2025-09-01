package com.example.mobile_app.activities;

import android.Manifest;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.Fragment;

import com.example.mobile_app.R;
import com.example.mobile_app.fragments.AlertsFragment;
import com.example.mobile_app.fragments.HomeFragment;
import com.example.mobile_app.fragments.MapViewFragment;
import com.example.mobile_app.fragments.WeatherFragment;
import com.example.mobile_app.models.Notification;
import com.example.mobile_app.models.NotificationsResponse;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.example.mobile_app.util.SessionManager;
import com.google.android.material.badge.BadgeDrawable;
import com.google.android.material.bottomnavigation.BottomNavigationView;
import com.google.android.material.dialog.MaterialAlertDialogBuilder;

import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MainActivity extends AppCompatActivity {

    // UI Elements
    private TextView toolbarTitle;
    private BottomNavigationView bottomNav;

    // Session & Networking
    private SessionManager sessionManager;
    private ApiService apiService;

    // Permission Handling
    private final ActivityResultLauncher<String> requestPermissionLauncher =
            registerForActivityResult(new ActivityResultContracts.RequestPermission(), isGranted -> {
                if (isGranted) {
                    Toast.makeText(this, "Location permission granted.", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(this, "Location permission was denied.", Toast.LENGTH_LONG).show();
                }
            });

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // Initialize Session and API Service first
        sessionManager = new SessionManager(getApplicationContext());
        apiService = ApiClient.getApiService();

        // Setup UI components
        setupToolbar();
        setupBottomNavigation();

        // Load initial state
        if (savedInstanceState == null) {
            getSupportFragmentManager().beginTransaction().replace(R.id.fragment_container, new HomeFragment()).commit();
            toolbarTitle.setText("Home");
        }

        // Check for necessary permissions on startup
        checkAndRequestLocationPermission();
    }

    @Override
    protected void onResume() {
        super.onResume();
        // Check for new notifications every time the user brings the app to the foreground
        fetchNotifications();
    }

    private void setupToolbar() {
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayShowTitleEnabled(false);
        }
        toolbarTitle = findViewById(R.id.toolbar_title);
    }

    private void setupBottomNavigation() {
        bottomNav = findViewById(R.id.bottom_navigation);
        bottomNav.setOnItemSelectedListener(navListener);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.main_options_menu, menu);
        return true;
    }



    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int itemId = item.getItemId();

        if (itemId == R.id.menu_contacts) {
            startActivity(new Intent(this, EmergencyContactsActivity.class));
            return true;
        } else if (itemId == R.id.menu_profile) {
            // THE FIX: Open the real ProfileActivity
            startActivity(new Intent(this, ProfileActivity.class));
            return true;
        } else if (itemId == R.id.menu_logout) {
            // ... (logout logic)
        }
        return super.onOptionsItemSelected(item);
    }

    private final BottomNavigationView.OnItemSelectedListener navListener =
            item -> {
                Fragment selectedFragment = null;
                String title = getString(R.string.app_name);
                int itemId = item.getItemId();

                if (itemId == R.id.nav_home) {
                    selectedFragment = new HomeFragment();
                    title = "Home";
                } else if (itemId == R.id.nav_map) {
                    selectedFragment = new MapViewFragment();
                    title = "Map";
                }   else if (itemId == R.id.nav_weather) {
                    selectedFragment = new WeatherFragment();
                    title = "Weather Forecast";
                }
                else if (itemId == R.id.nav_alerts) {
                    selectedFragment = new AlertsFragment();
                    title = "Alerts";
                    // When user clicks the Alerts tab, remove the badge
                    removeNotificationBadge();
                    // TODO: Call an API here to mark notifications as read in the database
                }

                if (selectedFragment != null) {
                    getSupportFragmentManager().beginTransaction().replace(R.id.fragment_container, selectedFragment).commit();
                    toolbarTitle.setText(title);
                }
                return true;
            };

    private void fetchNotifications() {
        int userId = sessionManager.getUserId();
        if (userId == 0) return; // Don't fetch if user is not logged in

        apiService.getNotifications(userId).enqueue(new Callback<NotificationsResponse>() {
            @Override
            public void onResponse(@NonNull Call<NotificationsResponse> call, @NonNull Response<NotificationsResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    List<Notification> notifications = response.body().getData();
                    if (notifications != null && !notifications.isEmpty()) {
                        showNotificationBadge(notifications.size());
                    } else {
                        removeNotificationBadge();
                    }
                }
            }

            @Override
            public void onFailure(@NonNull Call<NotificationsResponse> call, @NonNull Throwable t) {
                Log.e("MainActivity", "Failed to fetch notifications: " + t.getMessage());
            }
        });
    }

    private void showNotificationBadge(int count) {
        BadgeDrawable badge = bottomNav.getOrCreateBadge(R.id.nav_alerts);
        badge.setVisible(true);
        badge.setNumber(count);
    }

    private void removeNotificationBadge() {
        bottomNav.removeBadge(R.id.nav_alerts);
    }

    private void checkAndRequestLocationPermission() {
        String permission = Manifest.permission.ACCESS_FINE_LOCATION;
        if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
            if (shouldShowRequestPermissionRationale(permission)) {
                new MaterialAlertDialogBuilder(this)
                        .setTitle("Permission Needed")
                        .setMessage("This app needs location access for map and safety features.")
                        .setPositiveButton("OK", (dialog, which) -> requestPermissionLauncher.launch(permission))
                        .setNegativeButton("Cancel", (dialog, which) -> dialog.dismiss())
                        .create()
                        .show();
            } else {
                requestPermissionLauncher.launch(permission);
            }
        }
    }


}