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
import com.example.mobile_app.util.SessionManager;
import com.google.android.material.textfield.TextInputLayout;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class AuthActivity extends AppCompatActivity {

    // UI Elements
    private LinearLayout loginForm, registerForm;
    private TextInputLayout tilLoginEmail, tilLoginPassword, tilRegisterName, tilRegisterEmail, tilRegisterPhone, tilRegisterPassword;
    private EditText etLoginEmail, etLoginPassword, etRegisterName, etRegisterEmail, etRegisterPhone, etRegisterPassword;
    private Button btnLogin, btnRegister;
    private TextView tvGoToRegister, tvGoToLogin;
    private SessionManager sessionManager;
    private ApiService apiService;

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

        // Initialize SessionManager
        sessionManager = new SessionManager(getApplicationContext());
        
        // Check if user is already logged in
        if (sessionManager.isLoggedIn()) {
            navigateToMain();
        }
    }

    private void initializeViews() {
        // Form containers
        loginForm = findViewById(R.id.login_form);
        registerForm = findViewById(R.id.register_form);

        // Login form fields
        tilLoginEmail = findViewById(R.id.til_login_email);
        tilLoginPassword = findViewById(R.id.til_login_password);
        etLoginEmail = findViewById(R.id.et_login_email);
        etLoginPassword = findViewById(R.id.et_login_password);

        // Register form fields
        tilRegisterName = findViewById(R.id.til_register_name);
        tilRegisterEmail = findViewById(R.id.til_register_email);
        tilRegisterPhone = findViewById(R.id.til_register_phone);
        tilRegisterPassword = findViewById(R.id.til_register_password);
        etRegisterName = findViewById(R.id.et_register_name);
        etRegisterEmail = findViewById(R.id.et_register_email);
        etRegisterPhone = findViewById(R.id.et_register_phone);
        etRegisterPassword = findViewById(R.id.et_register_password);

        // Buttons
        btnLogin = findViewById(R.id.btn_login);
        btnRegister = findViewById(R.id.btn_register);

        // Navigation text views
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

        // Reset errors
        tilLoginEmail.setError(null);
        tilLoginPassword.setError(null);

        // Validate inputs
        if (TextUtils.isEmpty(email)) {
            tilLoginEmail.setError("Email is required");
            return;
        }

        if (TextUtils.isEmpty(password)) {
            tilLoginPassword.setError("Password is required");
            return;
        }

        // Show loading
        showLoading(true);

        // Make login API call using the correct method name
        Call<AuthResponse> call = apiService.loginUser(new User(email, password));
        call.enqueue(new Callback<AuthResponse>() {
            @Override
            public void onResponse(@NonNull Call<AuthResponse> call, @NonNull Response<AuthResponse> response) {
                showLoading(false);
                if (response.isSuccessful() && response.body() != null) {
                    // Save user session
                    sessionManager.createLoginSession(response.body().getData());
                    
                    // Navigate to main activity
                    navigateToMain();
                } else {
                    // Handle login error
                    String errorMessage = "Login failed. Please check your credentials.";
                    if (response.errorBody() != null) {
                        try {
                            errorMessage = response.errorBody().string();
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                    }
                    Toast.makeText(AuthActivity.this, errorMessage, Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<AuthResponse> call, @NonNull Throwable t) {
                showLoading(false);
                Toast.makeText(AuthActivity.this, "Network error. Please try again.", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void performRegistration() {
        String name = etRegisterName.getText().toString().trim();
        String email = etRegisterEmail.getText().toString().trim();
        String phone = etRegisterPhone.getText().toString().trim();
        String password = etRegisterPassword.getText().toString().trim();

        // Reset errors
        tilRegisterName.setError(null);
        tilRegisterEmail.setError(null);
        tilRegisterPhone.setError(null);
        tilRegisterPassword.setError(null);

        // Validate inputs
        if (TextUtils.isEmpty(name)) {
            tilRegisterName.setError("Name is required");
            return;
        }

        if (TextUtils.isEmpty(email)) {
            tilRegisterEmail.setError("Email is required");
            return;
        }

        if (TextUtils.isEmpty(phone)) {
            tilRegisterPhone.setError("Phone number is required");
            return;
        }

        if (TextUtils.isEmpty(password)) {
            tilRegisterPassword.setError("Password is required");
            return;
        }

        // Show loading
        showLoading(true);

        // Create user object
        User user = new User(name, email, password, phone);

        // Make registration API call using the correct method name
        Call<AuthResponse> call = apiService.registerUser(user);
        call.enqueue(new Callback<AuthResponse>() {
            @Override
            public void onResponse(@NonNull Call<AuthResponse> call, @NonNull Response<AuthResponse> response) {
                showLoading(false);
                if (response.isSuccessful() && response.body() != null) {
                    // Registration successful, switch to login form
                    toggleForms(true);
                    Toast.makeText(AuthActivity.this, "Registration successful. Please login.", Toast.LENGTH_SHORT).show();
                } else {
                    // Handle registration error
                    String errorMessage = "Registration failed. Please try again.";
                    if (response.errorBody() != null) {
                        try {
                            errorMessage = response.errorBody().string();
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                    }
                    Toast.makeText(AuthActivity.this, errorMessage, Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<AuthResponse> call, @NonNull Throwable t) {
                showLoading(false);
                Toast.makeText(AuthActivity.this, "Network error. Please try again.", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void showLoading(boolean show) {
        if (show) {
            // Show loading UI
        } else {
            // Hide loading UI
        }
    }

    private void navigateToMain() {
        startActivity(new Intent(this, MainActivity.class));
        finish();
    }
}