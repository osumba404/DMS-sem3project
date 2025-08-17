package com.example.mobile_app.fragments;

import android.os.Bundle;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
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

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_home, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        recyclerView = view.findViewById(R.id.recycler_view_alerts);
        progressBar = view.findViewById(R.id.progress_bar);
        tvNoAlerts = view.findViewById(R.id.tv_no_alerts);
        btnImSafe = view.findViewById(R.id.btn_im_safe);
        apiService = ApiClient.getApiService();

        setupRecyclerView();
        fetchActiveAlerts();
        btnImSafe.setOnClickListener(v -> markUserAsSafe());
    }

    private void setupRecyclerView() {
        adapter = new DisasterAlertAdapter(alertList);
        recyclerView.setLayoutManager(new LinearLayoutManager(getContext()));
        recyclerView.setAdapter(adapter);
    }

    private void fetchActiveAlerts() {
        progressBar.setVisibility(View.VISIBLE);
        tvNoAlerts.setVisibility(View.GONE);
        recyclerView.setVisibility(View.GONE);

        apiService.getActiveAlerts().enqueue(new Callback<AlertsResponse>() {
            @Override
            public void onResponse(@NonNull Call<AlertsResponse> call, @NonNull Response<AlertsResponse> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null) {
                    List<DisasterAlert> alerts = response.body().getData();
                    if (alerts != null && !alerts.isEmpty()) {
                        recyclerView.setVisibility(View.VISIBLE);
                        alertList.clear();
                        alertList.addAll(alerts);
                        adapter.notifyDataSetChanged();
                    } else {
                        tvNoAlerts.setVisibility(View.VISIBLE);
                    }
                } else {
                    tvNoAlerts.setVisibility(View.VISIBLE);
                    Toast.makeText(getContext(), "Failed to fetch alerts", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<AlertsResponse> call, @NonNull Throwable t) {
                progressBar.setVisibility(View.GONE);
                tvNoAlerts.setVisibility(View.VISIBLE);
                Toast.makeText(getContext(), "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void markUserAsSafe() {
        // TODO: Replace with real user ID from SharedPreferences after login
        int userId = 1;
        // TODO: Replace with real GPS coordinates from location service
        double latitude = 34.0522;
        double longitude = -118.2437;

        MarkSafeRequest request = new MarkSafeRequest(userId, latitude, longitude);

        apiService.markUserSafe(request).enqueue(new Callback<SimpleResponse>() {
            @Override
            public void onResponse(@NonNull Call<SimpleResponse> call, @NonNull Response<SimpleResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Toast.makeText(getContext(), response.body().getMessage(), Toast.LENGTH_LONG).show();
                } else {
                    Toast.makeText(getContext(), "Failed to update status", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<SimpleResponse> call, @NonNull Throwable t) {
                Toast.makeText(getContext(), "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}