package com.example.mobile_app.adapters;

import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.example.mobile_app.R;
import com.example.mobile_app.models.UserReport;
import com.google.android.material.card.MaterialCardView;
import com.google.android.material.chip.Chip;

import java.util.List;

public class UserReportAdapter extends RecyclerView.Adapter<UserReportAdapter.ReportViewHolder> {

    private List<UserReport> reports;
    private OnReportClickListener listener;

    public interface OnReportClickListener {
        void onReportClick(UserReport report);
    }

    public UserReportAdapter(List<UserReport> reports, OnReportClickListener listener) {
        this.reports = reports;
        this.listener = listener;
    }

    @NonNull
    @Override
    public ReportViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_user_report, parent, false);
        return new ReportViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ReportViewHolder holder, int position) {
        UserReport report = reports.get(position);
        holder.bind(report);
    }

    @Override
    public int getItemCount() {
        return reports.size();
    }

    class ReportViewHolder extends RecyclerView.ViewHolder {
        private MaterialCardView cardView;
        private TextView categoryIcon;
        private TextView titleText;
        private TextView descriptionText;
        private TextView timeText;
        private TextView locationText;
        private Chip statusChip;
        private Chip priorityChip;

        public ReportViewHolder(@NonNull View itemView) {
            super(itemView);
            cardView = itemView.findViewById(R.id.cardReport);
            categoryIcon = itemView.findViewById(R.id.textCategoryIcon);
            titleText = itemView.findViewById(R.id.textReportTitle);
            descriptionText = itemView.findViewById(R.id.textReportDescription);
            timeText = itemView.findViewById(R.id.textReportTime);
            locationText = itemView.findViewById(R.id.textReportLocation);
            statusChip = itemView.findViewById(R.id.chipStatus);
            priorityChip = itemView.findViewById(R.id.chipPriority);

            cardView.setOnClickListener(v -> {
                if (listener != null) {
                    listener.onReportClick(reports.get(getAdapterPosition()));
                }
            });
        }

        public void bind(UserReport report) {
            // Set category icon
            categoryIcon.setText(report.getCategoryIcon());

            // Set title and description
            titleText.setText(report.getTitle());
            descriptionText.setText(report.getDescription());

            // Set time
            timeText.setText(report.getRelativeTime() != null ? report.getRelativeTime() : report.getCreatedAt());

            // Set location
            if (report.getAddress() != null && !report.getAddress().isEmpty()) {
                locationText.setText("📍 " + report.getAddress());
                locationText.setVisibility(View.VISIBLE);
            } else if (report.getLatitude() != null && report.getLongitude() != null) {
                locationText.setText(String.format("📍 %.4f, %.4f", report.getLatitude(), report.getLongitude()));
                locationText.setVisibility(View.VISIBLE);
            } else {
                locationText.setVisibility(View.GONE);
            }

            // Set status chip
            statusChip.setText(report.getStatus());
            try {
                statusChip.setChipBackgroundColor(android.content.res.ColorStateList.valueOf(
                        Color.parseColor(report.getStatusColor())));
            } catch (Exception e) {
                // Fallback color if parsing fails
                statusChip.setChipBackgroundColor(android.content.res.ColorStateList.valueOf(
                        Color.parseColor("#6c757d")));
            }

            // Set priority chip
            priorityChip.setText(report.getPriority());
            try {
                priorityChip.setChipBackgroundColor(android.content.res.ColorStateList.valueOf(
                        Color.parseColor(report.getPriorityColor())));
            } catch (Exception e) {
                // Fallback color if parsing fails
                priorityChip.setChipBackgroundColor(android.content.res.ColorStateList.valueOf(
                        Color.parseColor("#6c757d")));
            }

            // Set text color based on chip background
            statusChip.setTextColor(getContrastColor(report.getStatusColor()));
            priorityChip.setTextColor(getContrastColor(report.getPriorityColor()));
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
    }
}
