package com.example.mobile_app.activities;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.example.mobile_app.R;
import com.example.mobile_app.models.AuthResponse;
import com.example.mobile_app.models.User;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class AuthActivity extends AppCompatActivity {

    // Declare UI elements
    LinearLayout loginForm, registerForm;
    EditText etLoginEmail, etLoginPassword, etRegisterName, etRegisterEmail, etRegisterPhone, etRegisterPassword;
    Button btnLogin, btnRegister;
    TextView tvGoToRegister, tvGoToLogin;

    ApiService apiService;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_auth);

        // Initialize UI elements
        initializeViews();

        // Initialize ApiService
        apiService = ApiClient.getApiService();

        // Setup button click listeners
        setupListeners();
    }

    private void initializeViews() {
        loginForm = findViewById(R.id.login_form);
        registerForm = findViewById(R.id.register_form);

        etLoginEmail = findViewById(R.id.et_login_email);
        etLoginPassword = findViewById(R.id.et_login_password);
        etRegisterName = findViewById(R.id.et_register_name);
        etRegisterEmail = findViewById(R.id.et_register_email);
        etRegisterPhone = findViewById(R.id.et_register_phone);
        etRegisterPassword = findViewById(R.id.et_register_password);

        btnLogin = findViewById(R.id.btn_login);
        btnRegister = findViewById(R.id.btn_register);

        tvGoToRegister = findViewById(R.id.tv_go_to_register);
        tvGoToLogin = findViewById(R.id.tv_go_to_login);
    }

    private void setupListeners() {
        // Switch to Register form
        tvGoToRegister.setOnClickListener(v -> toggleForms(false));

        // Switch to Login form
        tvGoToLogin.setOnClickListener(v -> toggleForms(true));

        // Login button click
        btnLogin.setOnClickListener(v -> performLogin());

        // Register button click
        btnRegister.setOnClickListener(v -> performRegistration());
    }

    private void toggleForms(boolean showLogin) {
        if (showLogin) {
            loginForm.setVisibility(View.VISIBLE);
            registerForm.setVisibility(View.GONE);
        } else {
            loginForm.setVisibility(View.GONE);
            registerForm.setVisibility(View.VISIBLE);
        }
    }

    private void performLogin() {
        String email = etLoginEmail.getText().toString().trim();
        String password = etLoginPassword.getText().toString().trim();

        if (TextUtils.isEmpty(email) || TextUtils.isEmpty(password)) {
            Toast.makeText(this, "All fields are required", Toast.LENGTH_SHORT).show();
            return;
        }

        User user = new User(email, password);
        apiService.loginUser(user).enqueue(new Callback<AuthResponse>() {
            @Override
            public void onResponse(@NonNull Call<AuthResponse> call, @NonNull Response<AuthResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    AuthResponse authResponse = response.body();
                    Toast.makeText(AuthActivity.this, authResponse.getMessage(), Toast.LENGTH_LONG).show();
                    if ("success".equals(authResponse.getStatus())) {
                        // TODO: Save user session and navigate to MainActivity
                        // Intent intent = new Intent(AuthActivity.this, MainActivity.class);
                        // startActivity(intent);
                        // finish();
                    }
                } else {
                    Toast.makeText(AuthActivity.this, "Login failed. Please try again.", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<AuthResponse> call, @NonNull Throwable t) {
                Toast.makeText(AuthActivity.this, "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void performRegistration() {
        String name = etRegisterName.getText().toString().trim();
        String email = etRegisterEmail.getText().toString().trim();
        String phone = etRegisterPhone.getText().toString().trim();
        String password = etRegisterPassword.getText().toString().trim();

        if (TextUtils.isEmpty(name) || TextUtils.isEmpty(email) || TextUtils.isEmpty(phone) || TextUtils.isEmpty(password)) {
            Toast.makeText(this, "All fields are required", Toast.LENGTH_SHORT).show();
            return;
        }

        User user = new User(name, email, password, phone);
        apiService.registerUser(user).enqueue(new Callback<AuthResponse>() {
            @Override
            public void onResponse(@NonNull Call<AuthResponse> call, @NonNull Response<AuthResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    AuthResponse authResponse = response.body();
                    Toast.makeText(AuthActivity.this, authResponse.getMessage(), Toast.LENGTH_LONG).show();
                    if ("success".equals(authResponse.getStatus())) {
                        // Switch to login form after successful registration
                        toggleForms(true);
                    }
                } else {
                    Toast.makeText(AuthActivity.this, "Registration failed. Please try again.", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<AuthResponse> call, @NonNull Throwable t) {
                Toast.makeText(AuthActivity.this, "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}