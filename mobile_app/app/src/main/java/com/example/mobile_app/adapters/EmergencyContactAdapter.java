package com.example.mobile_app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.example.mobile_app.R;
import com.example.mobile_app.models.EmergencyContact;
import java.util.List;

public class EmergencyContactAdapter extends RecyclerView.Adapter<EmergencyContactAdapter.ContactViewHolder> {

    private List<EmergencyContact> contactList;
    private OnDeleteClickListener listener;

    public interface OnDeleteClickListener {
        void onDeleteClick(EmergencyContact contact);
    }

    public EmergencyContactAdapter(List<EmergencyContact> contactList, OnDeleteClickListener listener) {
        this.contactList = contactList;
        this.listener = listener;
    }

    @NonNull
    @Override
    public ContactViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_emergency_contact, parent, false);
        return new ContactViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ContactViewHolder holder, int position) {
        EmergencyContact contact = contactList.get(position);
        holder.bind(contact, listener);
    }

    @Override
    public int getItemCount() {
        return contactList.size();
    }

    static class ContactViewHolder extends RecyclerView.ViewHolder {
        TextView name, phone, relationship;
        ImageButton deleteButton;

        public ContactViewHolder(@NonNull View itemView) {
            super(itemView);
            name = itemView.findViewById(R.id.tv_contact_name);
            phone = itemView.findViewById(R.id.tv_contact_phone);
            relationship = itemView.findViewById(R.id.tv_contact_relationship);
            deleteButton = itemView.findViewById(R.id.btn_delete_contact);
        }

        public void bind(final EmergencyContact contact, final OnDeleteClickListener listener) {
            name.setText(contact.getName());
            phone.setText(contact.getPhoneNumber());
            relationship.setText(contact.getRelationship());
            deleteButton.setOnClickListener(v -> listener.onDeleteClick(contact));
        }
    }
}