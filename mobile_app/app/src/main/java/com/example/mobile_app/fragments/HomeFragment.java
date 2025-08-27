
package com.example.mobile_app.fragments;
import android.Manifest;
import android.annotation.SuppressLint;
import android.content.pm.PackageManager;
import android.os.Bundle;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;
import com.example.mobile_app.R;
import com.example.mobile_app.adapters.DisasterAlertAdapter;
import com.example.mobile_app.models.AlertsResponse;
import com.example.mobile_app.models.DisasterAlert;
import com.example.mobile_app.models.SimpleResponse;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.example.mobile_app.network.MarkSafeRequest;
import com.example.mobile_app.util.SessionManager;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;
import java.util.ArrayList;
import java.util.List;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
public class HomeFragment extends Fragment {

    private RecyclerView recyclerView;
    private DisasterAlertAdapter adapter;
    private List<DisasterAlert> alertList = new ArrayList<>();
    private ProgressBar progressBar;
    private TextView tvNoAlerts;
    private Button btnImSafe;
    private ApiService apiService;
    private SessionManager sessionManager;
    private FusedLocationProviderClient fusedLocationClient;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_home, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        // Initialize everything
        initializeViews(view);
        sessionManager = new SessionManager(requireContext());
        apiService = ApiClient.getApiService();
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(requireActivity());

        setupRecyclerView();
        fetchActiveAlerts();

        btnImSafe.setOnClickListener(v -> markUserAsSafe());
    }

    private void initializeViews(View view) {
        recyclerView = view.findViewById(R.id.recycler_view_alerts);
        progressBar = view.findViewById(R.id.progress_bar);
        tvNoAlerts = view.findViewById(R.id.tv_no_alerts);
        btnImSafe = view.findViewById(R.id.btn_im_safe);
    }

    private void setupRecyclerView() {
        adapter = new DisasterAlertAdapter(alertList);
        recyclerView.setLayoutManager(new LinearLayoutManager(getContext()));
        recyclerView.setAdapter(adapter);
    }

    private void fetchActiveAlerts() {
        // ... (This method remains the same as before)
    }

    // --- THIS IS THE NEW, FULLY FUNCTIONAL METHOD ---
    @SuppressLint("MissingPermission")
    private void markUserAsSafe() {
        // First, check for location permission. MainActivity should have already asked for it,
        // but this is a good safety check.
        if (ContextCompat.checkSelfPermission(requireContext(), Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            Toast.makeText(getContext(), "Location permission is required to mark yourself safe.", Toast.LENGTH_LONG).show();
            // Optionally, you could trigger the permission request from MainActivity again here.
            return;
        }

        // Disable button to prevent multiple clicks
        btnImSafe.setEnabled(false);
        btnImSafe.setText("Getting Location...");

        fusedLocationClient.getLastLocation().addOnSuccessListener(requireActivity(), location -> {
            if (location != null) {
                // We have the location, now make the API call
                double latitude = location.getLatitude();
                double longitude = location.getLongitude();
                int userId = sessionManager.getUserId();

                btnImSafe.setText("Notifying Contacts...");

                MarkSafeRequest request = new MarkSafeRequest(userId, latitude, longitude);

                apiService.markUserSafe(request).enqueue(new Callback<SimpleResponse>() {
                    @Override
                    public void onResponse(@NonNull Call<SimpleResponse> call, @NonNull Response<SimpleResponse> response) {
                        if (response.isSuccessful() && response.body() != null) {
                            Toast.makeText(getContext(), response.body().getMessage(), Toast.LENGTH_LONG).show();
                        } else {
                            Toast.makeText(getContext(), "Failed to update status", Toast.LENGTH_SHORT).show();
                        }
                        // Re-enable the button after the call is complete
                        btnImSafe.setEnabled(true);
                        btnImSafe.setText("I'm Safe");
                    }

                    @Override
                    public void onFailure(@NonNull Call<SimpleResponse> call, @NonNull Throwable t) {
                        Toast.makeText(getContext(), "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                        btnImSafe.setEnabled(true);
                        btnImSafe.setText("I'm Safe");
                    }
                });

            } else {
                Toast.makeText(getContext(), "Could not get current location. Please turn on GPS.", Toast.LENGTH_LONG).show();
                btnImSafe.setEnabled(true);
                btnImSafe.setText("I'm Safe");
            }
        });
    }
}