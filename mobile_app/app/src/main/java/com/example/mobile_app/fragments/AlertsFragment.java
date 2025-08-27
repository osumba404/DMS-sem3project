package com.example.mobile_app.fragments;

import android.os.Bundle;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.example.mobile_app.R;
import com.example.mobile_app.adapters.UnifiedAlertsAdapter; // <-- Import the new adapter
import com.example.mobile_app.models.Alert; // <-- Import the new model
import com.example.mobile_app.models.UnifiedAlertsResponse; // <-- Import the new response
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.example.mobile_app.util.SessionManager; // <-- Import SessionManager

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class AlertsFragment extends Fragment {

    private RecyclerView recyclerView;
    private UnifiedAlertsAdapter adapter; // <-- Use the new adapter
    private List<Alert> alertList = new ArrayList<>(); // <-- Use the new model
    private ProgressBar progressBar;
    private TextView tvNoAlerts;
    private ApiService apiService;
    private SessionManager sessionManager;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_alerts, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        apiService = ApiClient.getApiService();
        sessionManager = new SessionManager(requireContext()); // Initialize SessionManager
        initializeViews(view);
        setupRecyclerView();
        fetchAlertHistory();
    }

    private void initializeViews(View view) {
        recyclerView = view.findViewById(R.id.recycler_view_alerts_history);
        progressBar = view.findViewById(R.id.progress_bar_alerts);
        tvNoAlerts = view.findViewById(R.id.tv_no_alerts_history);
    }

    private void setupRecyclerView() {
        adapter = new UnifiedAlertsAdapter(alertList); // Use the new adapter
        recyclerView.setLayoutManager(new LinearLayoutManager(getContext()));
        recyclerView.setAdapter(adapter);
    }

    private void fetchAlertHistory() {
        progressBar.setVisibility(View.VISIBLE);
        tvNoAlerts.setVisibility(View.GONE);
        recyclerView.setVisibility(View.GONE);

        int userId = sessionManager.getUserId();
        if (userId == 0) {
            Toast.makeText(getContext(), "Error: Not logged in.", Toast.LENGTH_SHORT).show();
            return;
        }

        // Call the new unified alerts endpoint
        apiService.getUnifiedAlerts(userId).enqueue(new Callback<UnifiedAlertsResponse>() {
            @Override
            public void onResponse(@NonNull Call<UnifiedAlertsResponse> call, @NonNull Response<UnifiedAlertsResponse> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null) {
                    List<Alert> alerts = response.body().getData();
                    if (alerts != null && !alerts.isEmpty()) {
                        recyclerView.setVisibility(View.VISIBLE);

                        // Replace the entire list with the new sorted list from the server
                        alertList.clear();
                        alertList.addAll(alerts);
                        adapter.notifyDataSetChanged();
                    } else {
                        tvNoAlerts.setVisibility(View.VISIBLE);
                    }
                } else {
                    tvNoAlerts.setVisibility(View.VISIBLE);
                    Toast.makeText(getContext(), "Failed to fetch alert history", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<UnifiedAlertsResponse> call, @NonNull Throwable t) {
                progressBar.setVisibility(View.GONE);
                tvNoAlerts.setVisibility(View.VISIBLE);
                Toast.makeText(getContext(), "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}