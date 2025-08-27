package com.example.mobile_app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.example.mobile_app.R;
import com.example.mobile_app.models.Alert;
import java.util.List;
import java.util.Objects;

public class UnifiedAlertsAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    private final List<Alert> alertList;

    // Define constants for the two view types
    private static final int VIEW_TYPE_BROADCAST = 1;
    private static final int VIEW_TYPE_NOTIFICATION = 2;

    public UnifiedAlertsAdapter(List<Alert> alertList) {
        this.alertList = alertList;
    }

    @Override
    public int getItemViewType(int position) {
        // Check the 'type' field of the Alert object
        if (Objects.equals(alertList.get(position).getType(), "notification")) {
            return VIEW_TYPE_NOTIFICATION;
        }
        // Default to broadcast
        return VIEW_TYPE_BROADCAST;
    }

    @NonNull
    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        // Inflate the correct layout based on the view type
        if (viewType == VIEW_TYPE_NOTIFICATION) {
            View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_personal_notification, parent, false);
            return new NotificationViewHolder(view);
        }
        // For VIEW_TYPE_BROADCAST
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_broadcast_message, parent, false);
        return new BroadcastViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull RecyclerView.ViewHolder holder, int position) {
        Alert alert = alertList.get(position);
        // Bind the data to the correct ViewHolder
        if (holder.getItemViewType() == VIEW_TYPE_NOTIFICATION) {
            ((NotificationViewHolder) holder).bind(alert);
        } else {
            ((BroadcastViewHolder) holder).bind(alert);
        }
    }

    @Override
    public int getItemCount() {
        return alertList.size();
    }

    // ViewHolder for Broadcasts (item_broadcast_message.xml)
    static class BroadcastViewHolder extends RecyclerView.ViewHolder {
        TextView title, body, timestamp;

        public BroadcastViewHolder(@NonNull View itemView) {
            super(itemView);
            title = itemView.findViewById(R.id.tv_alert_title);
            body = itemView.findViewById(R.id.tv_alert_body);
            timestamp = itemView.findViewById(R.id.tv_alert_timestamp);
        }

        public void bind(Alert alert) {
            title.setText(alert.getTitle());
            body.setText(alert.getBody());
            timestamp.setText(alert.getTimestamp());
        }
    }

    // ViewHolder for Personal Notifications (item_personal_notification.xml)
    static class NotificationViewHolder extends RecyclerView.ViewHolder {
        TextView title, body, timestamp;

        public NotificationViewHolder(@NonNull View itemView) {
            super(itemView);
            title = itemView.findViewById(R.id.tv_notification_title);
            body = itemView.findViewById(R.id.tv_notification_body);
            timestamp = itemView.findViewById(R.id.tv_notification_timestamp);
        }

        public void bind(Alert alert) {
            // Here you could customize the display even more, e.g., using the sender_name
            title.setText(alert.getTitle());
            body.setText(alert.getBody());
            timestamp.setText(alert.getTimestamp());
        }
    }
}