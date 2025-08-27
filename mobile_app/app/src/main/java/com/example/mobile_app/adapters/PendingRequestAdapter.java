package com.example.mobile_app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.example.mobile_app.R;
import com.example.mobile_app.models.EmergencyContact;
import java.util.List;

public class PendingRequestAdapter extends RecyclerView.Adapter<PendingRequestAdapter.RequestViewHolder> {

    private List<EmergencyContact> requestList;
    private OnRequestActionListener listener;

    // The interface for handling clicks on the Accept/Decline buttons
    public interface OnRequestActionListener {
        void onAccept(EmergencyContact request);
        void onDecline(EmergencyContact request);
    }

    // THE FIX: This is the constructor that was missing.
    // It takes a list of data and a listener for the actions.
    public PendingRequestAdapter(List<EmergencyContact> requestList, OnRequestActionListener listener) {
        this.requestList = requestList;
        this.listener = listener;
    }

    @NonNull
    @Override
    public RequestViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_pending_request, parent, false);
        return new RequestViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull RequestViewHolder holder, int position) {
        EmergencyContact request = requestList.get(position);
        holder.bind(request, listener);
    }

    @Override
    public int getItemCount() {
        return requestList.size();
    }

    static class RequestViewHolder extends RecyclerView.ViewHolder {
        TextView name, relationship;
        Button acceptButton, declineButton;

        public RequestViewHolder(@NonNull View itemView) {
            super(itemView);
            name = itemView.findViewById(R.id.tv_requester_name);
            relationship = itemView.findViewById(R.id.tv_requester_relationship);
            acceptButton = itemView.findViewById(R.id.btn_accept);
            declineButton = itemView.findViewById(R.id.btn_decline);
        }

        public void bind(final EmergencyContact request, final OnRequestActionListener listener) {
            // We assume the 'name' and 'relationship' fields are populated from the complex JOIN in the PHP script
            name.setText(request.getName());
            String relationshipText = "Wants to add you as their '" + request.getRelationship() + "'";
            relationship.setText(relationshipText);

            acceptButton.setOnClickListener(v -> listener.onAccept(request));
            declineButton.setOnClickListener(v -> listener.onDecline(request));
        }
    }
}