package com.example.mobile_app.activities;

import android.Manifest;
import android.content.pm.PackageManager;
import android.location.Location;
import android.os.Bundle;
import android.view.MenuItem;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.app.ActivityCompat;

import com.example.mobile_app.R;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.example.mobile_app.models.UserReport;
import com.example.mobile_app.models.UserReportResponse;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;
import com.google.android.material.textfield.TextInputEditText;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class SubmitReportActivity extends AppCompatActivity {

    private TextInputEditText editTitle, editDescription, editAddress;
    private Spinner spinnerCategory, spinnerPriority;
    private Button btnSubmit, btnGetLocation;
    private ApiService apiService;
    private FusedLocationProviderClient fusedLocationClient;
    private double currentLatitude = 0.0;
    private double currentLongitude = 0.0;

    private static final int LOCATION_PERMISSION_REQUEST_CODE = 1001;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_submit_report);

        initViews();
        setupToolbar();
        setupSpinners();
        setupApiService();
        setupLocationClient();
        setupClickListeners();
    }

    private void initViews() {
        editTitle = findViewById(R.id.editReportTitle);
        editDescription = findViewById(R.id.editReportDescription);
        editAddress = findViewById(R.id.editReportAddress);
        spinnerCategory = findViewById(R.id.spinnerCategory);
        spinnerPriority = findViewById(R.id.spinnerPriority);
        btnSubmit = findViewById(R.id.btnSubmitReport);
        btnGetLocation = findViewById(R.id.btnGetLocation);
    }

    private void setupToolbar() {
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle("Submit Report");
        }
    }

    private void setupSpinners() {
        // Category spinner
        String[] categories = {"Incident", "Hazard", "Infrastructure", "Safety", "Other"};
        ArrayAdapter<String> categoryAdapter = new ArrayAdapter<>(this, 
            android.R.layout.simple_spinner_item, categories);
        categoryAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerCategory.setAdapter(categoryAdapter);

        // Priority spinner
        String[] priorities = {"Low", "Medium", "High", "Critical"};
        ArrayAdapter<String> priorityAdapter = new ArrayAdapter<>(this, 
            android.R.layout.simple_spinner_item, priorities);
        priorityAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerPriority.setAdapter(priorityAdapter);
        spinnerPriority.setSelection(1); // Default to Medium
    }

    private void setupApiService() {
        apiService = ApiClient.getApiService();
    }

    private void setupLocationClient() {
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this);
    }

    private void setupClickListeners() {
        btnGetLocation.setOnClickListener(v -> getCurrentLocation());
        btnSubmit.setOnClickListener(v -> submitReport());
    }

    private void getCurrentLocation() {
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) 
                != PackageManager.PERMISSION_GRANTED && 
            ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION) 
                != PackageManager.PERMISSION_GRANTED) {
            
            ActivityCompat.requestPermissions(this,
                new String[]{Manifest.permission.ACCESS_FINE_LOCATION, 
                           Manifest.permission.ACCESS_COARSE_LOCATION},
                LOCATION_PERMISSION_REQUEST_CODE);
            return;
        }

        fusedLocationClient.getLastLocation()
            .addOnSuccessListener(this, location -> {
                if (location != null) {
                    currentLatitude = location.getLatitude();
                    currentLongitude = location.getLongitude();
                    
                    String locationText = String.format("📍 Location: %.4f, %.4f", 
                        currentLatitude, currentLongitude);
                    btnGetLocation.setText(locationText);
                    btnGetLocation.setEnabled(false);
                    
                    Toast.makeText(this, "Location captured successfully", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(this, "Unable to get current location", Toast.LENGTH_SHORT).show();
                }
            })
            .addOnFailureListener(this, e -> {
                Toast.makeText(this, "Failed to get location: " + e.getMessage(), 
                    Toast.LENGTH_SHORT).show();
            });
    }

    private void submitReport() {
        String title = editTitle.getText().toString().trim();
        String description = editDescription.getText().toString().trim();
        String address = editAddress.getText().toString().trim();
        String category = spinnerCategory.getSelectedItem().toString();
        String priority = spinnerPriority.getSelectedItem().toString();

        // Validation
        if (title.isEmpty()) {
            editTitle.setError("Title is required");
            editTitle.requestFocus();
            return;
        }

        if (description.isEmpty()) {
            editDescription.setError("Description is required");
            editDescription.requestFocus();
            return;
        }

        // Create report object
        UserReport report = new UserReport(title, description, category, priority, 
            currentLatitude != 0.0 ? currentLatitude : null,
            currentLongitude != 0.0 ? currentLongitude : null,
            address.isEmpty() ? null : address);

        // TODO: Get actual user ID from SharedPreferences or session
        report.setUserId(1); // Placeholder

        // Disable submit button to prevent double submission
        btnSubmit.setEnabled(false);
        btnSubmit.setText("Submitting...");

        // Submit report
        Call<UserReportResponse> call = apiService.submitReport(report);
        call.enqueue(new Callback<UserReportResponse>() {
            @Override
            public void onResponse(Call<UserReportResponse> call, Response<UserReportResponse> response) {
                btnSubmit.setEnabled(true);
                btnSubmit.setText("Submit Report");

                if (response.isSuccessful() && response.body() != null) {
                    UserReportResponse reportResponse = response.body();
                    if (reportResponse.isSuccess()) {
                        Toast.makeText(SubmitReportActivity.this, 
                            "Report submitted successfully!", Toast.LENGTH_LONG).show();
                        finish(); // Close activity and return to reports list
                    } else {
                        Toast.makeText(SubmitReportActivity.this, 
                            "Error: " + reportResponse.getMessage(), Toast.LENGTH_LONG).show();
                    }
                } else {
                    Toast.makeText(SubmitReportActivity.this, 
                        "Failed to submit report", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<UserReportResponse> call, Throwable t) {
                btnSubmit.setEnabled(true);
                btnSubmit.setText("Submit Report");
                Toast.makeText(SubmitReportActivity.this, 
                    "Network error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, 
                                         @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        
        if (requestCode == LOCATION_PERMISSION_REQUEST_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                getCurrentLocation();
            } else {
                Toast.makeText(this, "Location permission denied", Toast.LENGTH_SHORT).show();
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
}
