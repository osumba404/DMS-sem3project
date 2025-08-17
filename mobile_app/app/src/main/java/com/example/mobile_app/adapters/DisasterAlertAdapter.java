package com.example.mobile_app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.example.mobile_app.R;
import com.example.mobile_app.models.DisasterAlert;

import java.util.List;

public class DisasterAlertAdapter extends RecyclerView.Adapter<DisasterAlertAdapter.AlertViewHolder> {

    private List<DisasterAlert> alertList;

    public DisasterAlertAdapter(List<DisasterAlert> alertList) {
        this.alertList = alertList;
    }

    @NonNull
    @Override
    public AlertViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_disaster_alert, parent, false);
        return new AlertViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull AlertViewHolder holder, int position) {
        DisasterAlert alert = alertList.get(position);
        holder.name.setText(alert.getName());
        holder.type.setText("Type: " + alert.getType());
        holder.status.setText("Status: " + alert.getStatus());
    }

    @Override
    public int getItemCount() {
        return alertList.size();
    }

    static class AlertViewHolder extends RecyclerView.ViewHolder {
        TextView name, type, status;

        public AlertViewHolder(@NonNull View itemView) {
            super(itemView);
            name = itemView.findViewById(R.id.tv_disaster_name);
            type = itemView.findViewById(R.id.tv_disaster_type);
            status = itemView.findViewById(R.id.tv_disaster_status);
        }
    }
}