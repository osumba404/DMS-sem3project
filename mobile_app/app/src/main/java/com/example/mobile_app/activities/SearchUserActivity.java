package com.example.mobile_app.activities;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.SearchView;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.view.View;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.example.mobile_app.R;
import com.example.mobile_app.adapters.UserSearchAdapter;
import com.example.mobile_app.models.SimpleResponse;
import com.example.mobile_app.models.UserSearchResult;
import com.example.mobile_app.models.UserSearchResponse;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.example.mobile_app.util.SessionManager;
import com.google.android.material.dialog.MaterialAlertDialogBuilder;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class SearchUserActivity extends AppCompatActivity {

    private SearchView searchView;
    private RecyclerView recyclerView;
    private ProgressBar progressBar;
    private TextView tvNoResults;
    private UserSearchAdapter adapter;
    private List<UserSearchResult> userList = new ArrayList<>();
    private ApiService apiService;
    private SessionManager sessionManager; // <-- Add variable
    private int currentUserId; // TODO: Replace with real user ID from SharedPreferences
    private Handler searchHandler = new Handler(Looper.getMainLooper());
    private Runnable searchRunnable;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_search_user);

        sessionManager = new SessionManager(getApplicationContext());
        currentUserId = sessionManager.getUserId();

        apiService = ApiClient.getApiService();
        initializeViews();
        setupToolbar();
        setupRecyclerView();
        setupSearchView();

        // --- NEW: Fetch all users initially ---
        performSearch(""); // An empty query will fetch all users
    }

    private void initializeViews() {
        searchView = findViewById(R.id.search_view);
        recyclerView = findViewById(R.id.recycler_view_search_results);
        progressBar = findViewById(R.id.progress_bar_search);
        tvNoResults = findViewById(R.id.tv_no_results);
    }

    private void setupToolbar() {
        Toolbar toolbar = findViewById(R.id.toolbar_search);
        setSupportActionBar(toolbar);
        // Handle back arrow click
        toolbar.setNavigationOnClickListener(v -> finish());
    }

    private void setupRecyclerView() {
        adapter = new UserSearchAdapter(userList, user -> {
            showAddContactDialog(user);
        });
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        recyclerView.setAdapter(adapter);
    }

    private void setupSearchView() {
        searchView.setOnQueryTextListener(new SearchView.OnQueryTextListener() {
            @Override
            public boolean onQueryTextSubmit(String query) {
                searchHandler.removeCallbacks(searchRunnable);
                performSearch(query);
                return true;
            }

            @Override
            public boolean onQueryTextChange(String newText) {
                searchHandler.removeCallbacks(searchRunnable);
                searchRunnable = () -> performSearch(newText);
                searchHandler.postDelayed(searchRunnable, 500); // 500ms delay to avoid API calls on every keystroke
                return true;
            }
        });
    }

    private void performSearch(String query) {
        progressBar.setVisibility(View.VISIBLE);
        tvNoResults.setVisibility(View.GONE);
        recyclerView.setVisibility(View.GONE);

        apiService.searchUsers(query.trim(), currentUserId).enqueue(new Callback<UserSearchResponse>() {
            @Override
            public void onResponse(@NonNull Call<UserSearchResponse> call, @NonNull Response<UserSearchResponse> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null) {
                    List<UserSearchResult> results = response.body().getData();
                    if (results != null && !results.isEmpty()) {
                        recyclerView.setVisibility(View.VISIBLE);
                        adapter.updateData(results);
                    } else {
                        tvNoResults.setVisibility(View.VISIBLE);
                        adapter.updateData(new ArrayList<>()); // Clear previous results
                    }
                }
            }

            @Override
            public void onFailure(@NonNull Call<UserSearchResponse> call, @NonNull Throwable t) {
                progressBar.setVisibility(View.GONE);
                tvNoResults.setVisibility(View.VISIBLE);
                Toast.makeText(SearchUserActivity.this, "Network Error", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void showAddContactDialog(UserSearchResult user) {
        final EditText input = new EditText(this);
        input.setHint("e.g., Mother, Brother, Friend");
        input.setPadding(60, 40, 60, 40);

        new MaterialAlertDialogBuilder(this)
                .setTitle("Add " + user.getFullName())
                .setMessage("What is your relationship to this person?")
                .setView(input)
                .setPositiveButton("Send Request", (dialog, which) -> {
                    String relationship = input.getText().toString().trim();
                    if (!relationship.isEmpty()) {
                        sendContactRequest(user.getId(), relationship);
                    } else {
                        Toast.makeText(this, "Relationship cannot be empty", Toast.LENGTH_SHORT).show();
                    }
                })
                .setNegativeButton("Cancel", null)
                .show();
    }

    private void sendContactRequest(int contactUserId, String relationship) {
        apiService.addContactRequest(currentUserId, contactUserId, relationship).enqueue(new Callback<SimpleResponse>() {
            @Override
            public void onResponse(@NonNull Call<SimpleResponse> call, @NonNull Response<SimpleResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Toast.makeText(SearchUserActivity.this, response.body().getMessage(), Toast.LENGTH_LONG).show();
                    if ("success".equals(response.body().getStatus())) {
                        finish(); // Close the search activity on success
                    }
                } else {
                    Toast.makeText(SearchUserActivity.this, "Failed to send request", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(@NonNull Call<SimpleResponse> call, @NonNull Throwable t) {
                Toast.makeText(SearchUserActivity.this, "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}