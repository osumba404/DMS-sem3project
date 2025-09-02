package com.example.mobile_app.activities;

import android.graphics.Color;
import android.os.Bundle;
import android.view.MenuItem;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;

import com.example.mobile_app.R;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.example.mobile_app.models.UserReport;
import com.example.mobile_app.models.UserReportResponse;
import com.google.android.material.card.MaterialCardView;
import com.google.android.material.chip.Chip;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ReportDetailActivity extends AppCompatActivity {

    private TextView categoryIcon, titleText, descriptionText, timeText, locationText;
    private TextView statusLabel, priorityLabel, categoryLabel;
    private Chip statusChip, priorityChip;
    private MaterialCardView adminNotesCard;
    private TextView adminNotesText;
    private ApiService apiService;
    private int reportId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_report_detail);

        // Get report ID from intent
        reportId = getIntent().getIntExtra("report_id", -1);
        if (reportId == -1) {
            Toast.makeText(this, "Invalid report ID", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        initViews();
        setupToolbar();
        setupApiService();
        loadReportDetails();
    }

    private void initViews() {
        categoryIcon = findViewById(R.id.textCategoryIcon);
        titleText = findViewById(R.id.textReportTitle);
        descriptionText = findViewById(R.id.textReportDescription);
        timeText = findViewById(R.id.textReportTime);
        locationText = findViewById(R.id.textReportLocation);
        statusLabel = findViewById(R.id.textStatusLabel);
        priorityLabel = findViewById(R.id.textPriorityLabel);
        categoryLabel = findViewById(R.id.textCategoryLabel);
        statusChip = findViewById(R.id.chipStatus);
        priorityChip = findViewById(R.id.chipPriority);
        adminNotesCard = findViewById(R.id.cardAdminNotes);
        adminNotesText = findViewById(R.id.textAdminNotes);
    }

    private void setupToolbar() {
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle("Report Details");
        }
    }

    private void setupApiService() {
        apiService = ApiClient.getApiService();
    }

    private void loadReportDetails() {
        Call<UserReportResponse> call = apiService.getReportById(reportId);
        call.enqueue(new Callback<UserReportResponse>() {
            @Override
            public void onResponse(Call<UserReportResponse> call, Response<UserReportResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    UserReportResponse reportResponse = response.body();
                    if (reportResponse.isSuccess()) {
                        displayReportDetails(reportResponse.getReport());
                    } else {
                        Toast.makeText(ReportDetailActivity.this, 
                            "Error: " + reportResponse.getMessage(), Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(ReportDetailActivity.this, 
                        "Failed to load report details", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<UserReportResponse> call, Throwable t) {
                Toast.makeText(ReportDetailActivity.this, 
                    "Network error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void displayReportDetails(UserReport report) {
        // Set category icon and labels
        categoryIcon.setText(report.getCategoryIcon());
        categoryLabel.setText(report.getCategory());

        // Set title and description
        titleText.setText(report.getTitle());
        descriptionText.setText(report.getDescription());

        // Set time
        timeText.setText(report.getRelativeTime() != null ? report.getRelativeTime() : report.getCreatedAt());

        // Set location
        if (report.getAddress() != null && !report.getAddress().isEmpty()) {
            locationText.setText("📍 " + report.getAddress());
        } else if (report.getLatitude() != null && report.getLongitude() != null) {
            locationText.setText(String.format("📍 %.6f, %.6f", report.getLatitude(), report.getLongitude()));
        } else {
            locationText.setText("📍 Location not provided");
        }

        // Set status
        statusLabel.setText("Status: " + report.getStatus());
        statusChip.setText(report.getStatus());
        try {
            statusChip.setChipBackgroundColor(android.content.res.ColorStateList.valueOf(
                    Color.parseColor(report.getStatusColor())));
            statusChip.setTextColor(getContrastColor(report.getStatusColor()));
        } catch (Exception e) {
            statusChip.setChipBackgroundColor(android.content.res.ColorStateList.valueOf(
                    Color.parseColor("#6c757d")));
            statusChip.setTextColor(Color.WHITE);
        }

        // Set priority
        priorityLabel.setText("Priority: " + report.getPriority());
        priorityChip.setText(report.getPriority());
        try {
            priorityChip.setChipBackgroundColor(android.content.res.ColorStateList.valueOf(
                    Color.parseColor(report.getPriorityColor())));
            priorityChip.setTextColor(getContrastColor(report.getPriorityColor()));
        } catch (Exception e) {
            priorityChip.setChipBackgroundColor(android.content.res.ColorStateList.valueOf(
                    Color.parseColor("#6c757d")));
            priorityChip.setTextColor(Color.WHITE);
        }

        // Set admin notes if available
        if (report.getAdminNotes() != null && !report.getAdminNotes().trim().isEmpty()) {
            adminNotesCard.setVisibility(android.view.View.VISIBLE);
            adminNotesText.setText(report.getAdminNotes());
        } else {
            adminNotesCard.setVisibility(android.view.View.GONE);
        }
    }

    private int getContrastColor(String hexColor) {
        try {
            int color = Color.parseColor(hexColor);
            double luminance = (0.299 * Color.red(color) + 0.587 * Color.green(color) + 0.114 * Color.blue(color)) / 255;
            return luminance > 0.5 ? Color.BLACK : Color.WHITE;
        } catch (Exception e) {
            return Color.WHITE;
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
