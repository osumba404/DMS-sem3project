package com.example.mobile_app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.example.mobile_app.R;
import com.example.mobile_app.models.BroadcastMessage;
import java.util.List;

public class BroadcastMessageAdapter extends RecyclerView.Adapter<BroadcastMessageAdapter.MessageViewHolder> {

    private List<BroadcastMessage> messageList;

    public BroadcastMessageAdapter(List<BroadcastMessage> messageList) {
        this.messageList = messageList;
    }

    @NonNull
    @Override
    public MessageViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_broadcast_message, parent, false);
        return new MessageViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull MessageViewHolder holder, int position) {
        BroadcastMessage message = messageList.get(position);
        holder.bind(message);
    }

    @Override
    public int getItemCount() {
        return messageList.size();
    }

    static class MessageViewHolder extends RecyclerView.ViewHolder {
        TextView title, body, timestamp;

        public MessageViewHolder(@NonNull View itemView) {
            super(itemView);
            title = itemView.findViewById(R.id.tv_alert_title);
            body = itemView.findViewById(R.id.tv_alert_body);
            timestamp = itemView.findViewById(R.id.tv_alert_timestamp);
        }

        public void bind(final BroadcastMessage message) {
            title.setText(message.getTitle());
            body.setText(message.getBody());
            timestamp.setText(message.getSentAt());
        }
    }
}