package com.example.mobile_app.activities;

import android.Manifest;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.Fragment;

import com.example.mobile_app.R;
import com.example.mobile_app.fragments.AlertsFragment;
import com.example.mobile_app.fragments.HomeFragment;
import com.example.mobile_app.fragments.MapViewFragment;
import com.google.android.material.bottomnavigation.BottomNavigationView;
import com.google.android.material.dialog.MaterialAlertDialogBuilder;

public class MainActivity extends AppCompatActivity {

    // A variable for our custom title TextView in the toolbar
    private TextView toolbarTitle;

    // Launcher for handling the permission request result
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

        // --- Toolbar Setup ---
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        // Disable the default title provided by the Toolbar
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayShowTitleEnabled(false);
        }
        // Get a reference to our custom title TextView from the layout
        toolbarTitle = findViewById(R.id.toolbar_title);


        // --- Bottom Navigation Setup ---
        BottomNavigationView bottomNav = findViewById(R.id.bottom_navigation);
        bottomNav.setOnItemSelectedListener(navListener);


        // --- Initial Fragment and Permission Check ---
        if (savedInstanceState == null) {
            // Load the default fragment (HomeFragment)
            getSupportFragmentManager().beginTransaction().replace(R.id.fragment_container,
                    new HomeFragment()).commit();
            // Set the initial title on our custom TextView
            toolbarTitle.setText("Home");
        }

        // Check for location permission as soon as the activity is created
        checkAndRequestLocationPermission();
    }


    /**
     * This method is called by the system to create the options menu (three dots).
     */
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.main_options_menu, menu);
        return true;
    }

    /**
     * This method is called when a user clicks on an item in the options menu.
     */
    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int itemId = item.getItemId();

        if (itemId == R.id.menu_contacts) {
            Intent intent = new Intent(this, EmergencyContactsActivity.class);
            startActivity(intent);
            return true;
        } else if (itemId == R.id.menu_profile) {
            Toast.makeText(this, "Profile screen coming soon!", Toast.LENGTH_SHORT).show();
            return true;
        } else if (itemId == R.id.menu_logout) {
            // Logout and go back to the login screen
            Intent intent = new Intent(this, AuthActivity.class);
            // Flags to clear the activity stack so the user can't press "back" to get into the app
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
            startActivity(intent);
            finish(); // Close MainActivity
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    /**
     * Listener for handling clicks on the bottom navigation bar items.
     */
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
                } else if (itemId == R.id.nav_alerts) {
                    selectedFragment = new AlertsFragment();
                    title = "Alerts";
                }

                if (selectedFragment != null) {
                    // Replace the fragment in the container
                    getSupportFragmentManager().beginTransaction().replace(R.id.fragment_container,
                            selectedFragment).commit();
                    // Set the title on our custom TextView in the toolbar
                    toolbarTitle.setText(title);
                }
                return true;
            };

    /**
     * Checks if location permission is granted. If not, it requests it.
     */
    private void checkAndRequestLocationPermission() {
        String permission = Manifest.permission.ACCESS_FINE_LOCATION;

        if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
            // Check if we should show a rationale (explanation) to the user
            if (shouldShowRequestPermissionRationale(permission)) {
                new MaterialAlertDialogBuilder(this)
                        .setTitle("Permission Needed")
                        .setMessage("This app needs location access for map and safety features.")
                        .setPositiveButton("OK", (dialog, which) -> requestPermissionLauncher.launch(permission))
                        .setNegativeButton("Cancel", (dialog, which) -> dialog.dismiss())
                        .create()
                        .show();
            } else {
                // No explanation needed, just request the permission
                requestPermissionLauncher.launch(permission);
            }
        }
        // If permission is already granted, do nothing.
    }
}