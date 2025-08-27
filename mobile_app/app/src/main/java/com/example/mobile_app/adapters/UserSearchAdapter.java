package com.example.mobile_app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.example.mobile_app.R;
import com.example.mobile_app.models.UserSearchResult;
import java.util.List;

public class UserSearchAdapter extends RecyclerView.Adapter<UserSearchAdapter.UserViewHolder> {

    private List<UserSearchResult> userList;
    private OnUserClickListener listener;

    public interface OnUserClickListener {
        void onUserClick(UserSearchResult user);
    }

    public UserSearchAdapter(List<UserSearchResult> userList, OnUserClickListener listener) {
        this.userList = userList;
        this.listener = listener;
    }

    @NonNull
    @Override
    public UserViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_user_search_result, parent, false);
        return new UserViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull UserViewHolder holder, int position) {
        UserSearchResult user = userList.get(position);
        holder.bind(user, listener);
    }

    @Override
    public int getItemCount() {
        return userList.size();
    }

    // Method to update the data in the adapter
    public void updateData(List<UserSearchResult> newUserList) {
        this.userList.clear();
        this.userList.addAll(newUserList);
        notifyDataSetChanged();
    }

    static class UserViewHolder extends RecyclerView.ViewHolder {
        TextView name, email;

        public UserViewHolder(@NonNull View itemView) {
            super(itemView);
            name = itemView.findViewById(R.id.tv_user_name);
            email = itemView.findViewById(R.id.tv_user_email);
        }

        public void bind(final UserSearchResult user, final OnUserClickListener listener) {
            name.setText(user.getFullName());
            email.setText(user.getEmail());
            itemView.setOnClickListener(v -> listener.onUserClick(user));
        }
    }
}