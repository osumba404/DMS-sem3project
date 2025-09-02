package com.example.mobile_app.fragments;

import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.example.mobile_app.R;
import com.example.mobile_app.activities.ReportDetailActivity;
import com.example.mobile_app.activities.SubmitReportActivity;
import com.example.mobile_app.adapters.UserReportAdapter;
import com.example.mobile_app.models.UserReport;
import com.example.mobile_app.models.UserReportResponse;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.google.android.material.floatingactionbutton.FloatingActionButton;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ReportsFragment extends Fragment implements UserReportAdapter.OnReportClickListener {

    private RecyclerView recyclerView;
    private UserReportAdapter adapter;
    private List<UserReport> reportsList;
    private FloatingActionButton fabAddReport;
    private ApiService apiService;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_reports, container, false);
        
        initViews(view);
        setupRecyclerView();
        setupApiService();
        loadUserReports();
        
        return view;
    }

    private void initViews(View view) {
        recyclerView = view.findViewById(R.id.recyclerViewReports);
        fabAddReport = view.findViewById(R.id.fabAddReport);
        
        fabAddReport.setOnClickListener(v -> {
            Intent intent = new Intent(getActivity(), SubmitReportActivity.class);
            startActivity(intent);
        });
    }

    private void setupRecyclerView() {
        reportsList = new ArrayList<>();
        adapter = new UserReportAdapter(reportsList, this);
        recyclerView.setLayoutManager(new LinearLayoutManager(getContext()));
        recyclerView.setAdapter(adapter);
    }

    private void setupApiService() {
        apiService = ApiClient.getApiService();
    }

    private void loadUserReports() {
        // TODO: Get actual user ID from SharedPreferences or session
        int userId = 1; // Placeholder
        
        Call<UserReportResponse> call = apiService.getUserReports(userId);
        call.enqueue(new Callback<UserReportResponse>() {
            @Override
            public void onResponse(Call<UserReportResponse> call, Response<UserReportResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    UserReportResponse reportResponse = response.body();
                    if (reportResponse.isSuccess()) {
                        reportsList.clear();
                        reportsList.addAll(reportResponse.getReports());
                        adapter.notifyDataSetChanged();
                    } else {
                        Toast.makeText(getContext(), "Error: " + reportResponse.getMessage(), Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(getContext(), "Failed to load reports", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<UserReportResponse> call, Throwable t) {
                Toast.makeText(getContext(), "Network error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    @Override
    public void onReportClick(UserReport report) {
        Intent intent = new Intent(getActivity(), ReportDetailActivity.class);
        intent.putExtra("report_id", report.getId());
        startActivity(intent);
    }

    @Override
    public void onResume() {
        super.onResume();
        // Refresh reports when returning to this fragment
        loadUserReports();
    }
}
