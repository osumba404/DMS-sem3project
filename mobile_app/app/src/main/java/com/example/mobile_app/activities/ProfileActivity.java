package com.example.mobile_app.activities;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.example.mobile_app.R;
import com.example.mobile_app.models.ProfileData;
import com.example.mobile_app.models.ProfileResponse;
import com.example.mobile_app.models.SimpleResponse;
import com.example.mobile_app.models.User;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.example.mobile_app.network.UpdateProfileRequest;
import com.example.mobile_app.util.SessionManager;
import com.google.android.material.textfield.TextInputEditText;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ProfileActivity extends AppCompatActivity {

    private TextView tvEmail;
    private TextInputEditText etName, etPhone;
    private Button btnSaveChanges, btnManageContacts, btnChangePassword;
    private ProgressBar progressBar;

    private ApiService apiService;
    private SessionManager sessionManager;
    private int userId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_profile);

        sessionManager = new SessionManager(getApplicationContext());
        apiService = ApiClient.getApiService();
        userId = sessionManager.getUserId();

        initializeViews();
        setupToolbar();
        setupClickListeners();

        fetchUserProfile();
    }

    private void initializeViews() {
        tvEmail = findViewById(R.id.tv_profile_email);
        etName = findViewById(R.id.et_profile_name);
        etPhone = findViewById(R.id.et_profile_phone);
        btnSaveChanges = findViewById(R.id.btn_save_changes);
        btnManageContacts = findViewById(R.id.btn_manage_contacts);
        btnChangePassword = findViewById(R.id.btn_change_password);
        progressBar = findViewById(R.id.progress_bar_profile);
    }

    private void setupToolbar() {
        Toolbar toolbar = findViewById(R.id.toolbar_profile);
        setSupportActionBar(toolbar);
        toolbar.setNavigationOnClickListener(v -> finish());
    }

    private void setupClickListeners() {
        btnSaveChanges.setOnClickListener(v -> saveProfileChanges());
        btnManageContacts.setOnClickListener(v ->
                startActivity(new Intent(this, EmergencyContactsActivity.class))
        );
        btnChangePassword.setOnClickListener(v ->
                Toast.makeText(this, "Change password feature coming soon.", Toast.LENGTH_SHORT).show()
        );
    }

    private void fetchUserProfile() {
        progressBar.setVisibility(View.VISIBLE);
        apiService.getUserProfile(userId).enqueue(new Callback<ProfileResponse>() {
            @Override
            public void onResponse(@NonNull Call<ProfileResponse> call, @NonNull Response<ProfileResponse> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null) {
                    ProfileData profile = response.body().getData();
                    if (profile != null) {
                        populateUI(profile);
                    }
                } else {
                    Toast.makeText(ProfileActivity.this, "Failed to load profile.", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<ProfileResponse> call, @NonNull Throwable t) {
                progressBar.setVisibility(View.GONE);
                Toast.makeText(ProfileActivity.this, "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void populateUI(ProfileData profile) {
        tvEmail.setText(profile.getEmail());
        etName.setText(profile.getFullName());
        etPhone.setText(profile.getPhoneNumber());
    }

    private void saveProfileChanges() {
        String name = etName.getText().toString().trim();
        String phone = etPhone.getText().toString().trim();

        if (name.isEmpty() || phone.isEmpty()) {
            Toast.makeText(this, "Name and Phone cannot be empty.", Toast.LENGTH_SHORT).show();
            return;
        }

        progressBar.setVisibility(View.VISIBLE);
        btnSaveChanges.setEnabled(false);

        UpdateProfileRequest request = new UpdateProfileRequest(userId, name, phone);

        apiService.updateUserProfile(request).enqueue(new Callback<SimpleResponse>() {
            @Override
            public void onResponse(@NonNull Call<SimpleResponse> call, @NonNull Response<SimpleResponse> response) {
                progressBar.setVisibility(View.GONE);
                btnSaveChanges.setEnabled(true);
                if (response.isSuccessful() && response.body() != null) {
                    Toast.makeText(ProfileActivity.this, response.body().getMessage(), Toast.LENGTH_LONG).show();
                    // Optionally, update the name in the session manager if it changed
                    if ("success".equals(response.body().getStatus())) {
                        sessionManager.updateFullName(name);
                    }
                } else {
                    Toast.makeText(ProfileActivity.this, "Failed to update profile.", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<SimpleResponse> call, @NonNull Throwable t) {
                progressBar.setVisibility(View.GONE);
                btnSaveChanges.setEnabled(true);
                Toast.makeText(ProfileActivity.this, "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}