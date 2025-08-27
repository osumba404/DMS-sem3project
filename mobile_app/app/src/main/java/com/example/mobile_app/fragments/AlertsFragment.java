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
import com.example.mobile_app.adapters.BroadcastMessageAdapter;
import com.example.mobile_app.models.BroadcastMessage;
import com.example.mobile_app.models.BroadcastResponse;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class AlertsFragment extends Fragment {

    private RecyclerView recyclerView;
    private BroadcastMessageAdapter adapter;
    private List<BroadcastMessage> messageList = new ArrayList<>();
    private ProgressBar progressBar;
    private TextView tvNoAlerts;
    private ApiService apiService;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_alerts, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        apiService = ApiClient.getApiService();
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
        adapter = new BroadcastMessageAdapter(messageList);
        recyclerView.setLayoutManager(new LinearLayoutManager(getContext()));
        recyclerView.setAdapter(adapter);
    }

    private void fetchAlertHistory() {
        progressBar.setVisibility(View.VISIBLE);
        tvNoAlerts.setVisibility(View.GONE);
        recyclerView.setVisibility(View.GONE);

        apiService.getBroadcastHistory().enqueue(new Callback<BroadcastResponse>() {
            @Override
            public void onResponse(@NonNull Call<BroadcastResponse> call, @NonNull Response<BroadcastResponse> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null) {
                    List<BroadcastMessage> messages = response.body().getData();
                    if (messages != null && !messages.isEmpty()) {
                        recyclerView.setVisibility(View.VISIBLE);
                        messageList.clear();
                        messageList.addAll(messages);
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
            public void onFailure(@NonNull Call<BroadcastResponse> call, @NonNull Throwable t) {
                progressBar.setVisibility(View.GONE);
                tvNoAlerts.setVisibility(View.VISIBLE);
                Toast.makeText(getContext(), "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}