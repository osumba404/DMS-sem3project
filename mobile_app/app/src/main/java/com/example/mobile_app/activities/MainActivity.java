package com.example.mobile_app.activities;

import androidx.appcompat.app.AppCompatActivity;
import androidx.fragment.app.Fragment;

import android.os.Bundle;

import com.example.mobile_app.R;
import com.example.mobile_app.fragments.AlertsFragment;
import com.example.mobile_app.fragments.HomeFragment;
import com.example.mobile_app.fragments.MapViewFragment;
import com.google.android.material.bottomnavigation.BottomNavigationView;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        BottomNavigationView bottomNav = findViewById(R.id.bottom_navigation);
        // Set up the listener for item clicks
        bottomNav.setOnItemSelectedListener(navListener);

        // Load the default fragment when the activity is first created
        if (savedInstanceState == null) {
            getSupportFragmentManager().beginTransaction().replace(R.id.fragment_container,
                    new HomeFragment()).commit();
        }
    }

    // Listener for the bottom navigation view
    private final BottomNavigationView.OnItemSelectedListener navListener =
            item -> {
                Fragment selectedFragment = null;

                int itemId = item.getItemId();
                if (itemId == R.id.nav_home) {
                    selectedFragment = new HomeFragment();
                } else if (itemId == R.id.nav_map) {
                    selectedFragment = new MapViewFragment();
                } else if (itemId == R.id.nav_alerts) {
                    selectedFragment = new AlertsFragment();
                }

                if (selectedFragment != null) {
                    // Replace the current fragment with the selected one
                    getSupportFragmentManager().beginTransaction().replace(R.id.fragment_container,
                            selectedFragment).commit();
                }
                return true;
            };
}