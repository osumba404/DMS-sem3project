package com.example.mobile_app.activities;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.appcompat.widget.Toolbar;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.example.mobile_app.R;
import com.example.mobile_app.adapters.EmergencyContactAdapter;
import com.example.mobile_app.adapters.PendingRequestAdapter;
import com.example.mobile_app.models.ContactData;
import com.example.mobile_app.models.ContactsResponse;
import com.example.mobile_app.models.EmergencyContact;
import com.example.mobile_app.models.SimpleResponse;
import com.example.mobile_app.network.ApiClient;
import com.example.mobile_app.network.ApiService;
import com.example.mobile_app.util.SessionManager;
import com.google.android.material.dialog.MaterialAlertDialogBuilder;
import com.google.android.material.floatingactionbutton.FloatingActionButton;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class EmergencyContactsActivity extends AppCompatActivity {

    // UI Elements
    private RecyclerView acceptedRecyclerView, pendingRecyclerView;
    private ProgressBar progressBar;
    private TextView tvNoContacts, tvPendingTitle;
    private FloatingActionButton fabAdd;

    // Adapters and Data Lists
    private EmergencyContactAdapter acceptedAdapter;
    private PendingRequestAdapter pendingAdapter;
    private List<EmergencyContact> acceptedContactList = new ArrayList<>();
    private List<EmergencyContact> pendingRequestList = new ArrayList<>();

    // Networking & Session
    private ApiService apiService;
    private SessionManager sessionManager;
    private int userId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_emergency_contacts);

        // Initialize Session and get the logged-in user's ID
        sessionManager = new SessionManager(getApplicationContext());
        userId = sessionManager.getUserId();

        apiService = ApiClient.getApiService();

        initializeViews();
        setupToolbar();
        setupRecyclerViews();

        fabAdd.setOnClickListener(v -> {
            Intent intent = new Intent(EmergencyContactsActivity.this, SearchUserActivity.class);
            startActivity(intent);
        });
    }

    @Override
    protected void onResume() {
        super.onResume();
        // Refresh the lists every time the user comes back to this screen
        fetchContacts();
    }

    private void setupToolbar() {
        Toolbar toolbar = findViewById(R.id.toolbar_contacts);
        setSupportActionBar(toolbar);
        toolbar.setNavigationOnClickListener(v -> finish());
    }

    private void initializeViews() {
        acceptedRecyclerView = findViewById(R.id.recycler_view_contacts);
        pendingRecyclerView = findViewById(R.id.recycler_view_pending);
        progressBar = findViewById(R.id.progress_bar_contacts);
        tvNoContacts = findViewById(R.id.tv_no_contacts);
        tvPendingTitle = findViewById(R.id.tv_pending_title);
        fabAdd = findViewById(R.id.fab_add_contact);
    }

    private void setupRecyclerViews() {
        // 1. Setup for ACCEPTED contacts list
        acceptedAdapter = new EmergencyContactAdapter(acceptedContactList, contact -> {
            // THE FIX: Re-enabled the delete/remove functionality
            new MaterialAlertDialogBuilder(this)
                    .setTitle("Remove Contact")
                    .setMessage("Are you sure you want to remove " + contact.getName() + " from your emergency contacts?")
                    .setPositiveButton("Remove", (dialog, which) -> rejectRequest(contact)) // Removing a contact is the same as rejecting a request
                    .setNegativeButton("Cancel", null)
                    .show();
        });
        acceptedRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        acceptedRecyclerView.setAdapter(acceptedAdapter);

        // 2. Setup for PENDING requests list
        pendingAdapter = new PendingRequestAdapter(pendingRequestList, new PendingRequestAdapter.OnRequestActionListener() {
            @Override
            public void onAccept(EmergencyContact request) {
                acceptRequest(request);
            }

            @Override
            public void onDecline(EmergencyContact request) {
                rejectRequest(request);
            }
        });
        pendingRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        pendingRecyclerView.setAdapter(pendingAdapter);
    }

    private void fetchContacts() {
        progressBar.setVisibility(View.VISIBLE);
        tvNoContacts.setVisibility(View.GONE);
        acceptedRecyclerView.setVisibility(View.GONE);
        pendingRecyclerView.setVisibility(View.GONE);
        tvPendingTitle.setVisibility(View.GONE);

        apiService.getEmergencyContacts(userId).enqueue(new Callback<ContactsResponse>() {
            @Override
            public void onResponse(@NonNull Call<ContactsResponse> call, @NonNull Response<ContactsResponse> response) {
                progressBar.setVisibility(View.GONE);
                if (response.isSuccessful() && response.body() != null) {
                    ContactData contactData = response.body().getData();
                    if (contactData != null) {
                        // THE FIX: More efficient data handling
                        List<EmergencyContact> accepted = contactData.getAcceptedContacts();
                        List<EmergencyContact> pending = contactData.getPendingReceivedRequests();

                        // Populate accepted contacts list
                        acceptedContactList.clear();
                        if (accepted != null) {
                            acceptedContactList.addAll(accepted);
                        }
                        acceptedAdapter.notifyDataSetChanged();

                        // Populate pending requests list
                        pendingRequestList.clear();
                        if (pending != null) {
                            pendingRequestList.addAll(pending);
                        }
                        pendingAdapter.notifyDataSetChanged();

                    }
                } else {
                    Toast.makeText(EmergencyContactsActivity.this, "Failed to fetch contacts", Toast.LENGTH_SHORT).show();
                    // Clear lists on failure
                    acceptedContactList.clear();
                    pendingRequestList.clear();
                    acceptedAdapter.notifyDataSetChanged();
                    pendingAdapter.notifyDataSetChanged();
                }
                // Update UI visibility after processing data, success or fail
                updateUIVisibility();
            }

            @Override
            public void onFailure(@NonNull Call<ContactsResponse> call, @NonNull Throwable t) {
                progressBar.setVisibility(View.GONE);
                Toast.makeText(EmergencyContactsActivity.this, "Network Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                updateUIVisibility();
            }
        });
    }

    private void updateUIVisibility() {
        // Show/hide the "Pending Requests" section
        if (pendingRequestList.isEmpty()) {
            pendingRecyclerView.setVisibility(View.GONE);
            tvPendingTitle.setVisibility(View.GONE);
        } else {
            pendingRecyclerView.setVisibility(View.VISIBLE);
            tvPendingTitle.setVisibility(View.VISIBLE);
        }

        // Show/hide the main contacts list
        if (acceptedContactList.isEmpty()) {
            acceptedRecyclerView.setVisibility(View.GONE);
        } else {
            acceptedRecyclerView.setVisibility(View.VISIBLE);
        }

        // Show/hide the "No contacts" message
        if (pendingRequestList.isEmpty() && acceptedContactList.isEmpty()) {
            tvNoContacts.setVisibility(View.VISIBLE);
        } else {
            tvNoContacts.setVisibility(View.GONE);
        }
    }

    private void acceptRequest(EmergencyContact request) {
        apiService.acceptContactRequest(request.getId()).enqueue(new Callback<SimpleResponse>() {
            @Override
            public void onResponse(@NonNull Call<SimpleResponse> call, @NonNull Response<SimpleResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Toast.makeText(EmergencyContactsActivity.this, response.body().getMessage(), Toast.LENGTH_SHORT).show();
                    fetchContacts(); // Refresh all lists
                } else {
                    Toast.makeText(EmergencyContactsActivity.this, "Failed to accept request", Toast.LENGTH_SHORT).show();
                }
            }
            @Override
            public void onFailure(@NonNull Call<SimpleResponse> call, @NonNull Throwable t) {
                Toast.makeText(EmergencyContactsActivity.this, "Network Error", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void rejectRequest(EmergencyContact request) {
        apiService.rejectContactRequest(request.getId()).enqueue(new Callback<SimpleResponse>() {
            @Override
            public void onResponse(@NonNull Call<SimpleResponse> call, @NonNull Response<SimpleResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Toast.makeText(EmergencyContactsActivity.this, response.body().getMessage(), Toast.LENGTH_SHORT).show();
                    fetchContacts(); // Refresh all lists
                } else {
                    Toast.makeText(EmergencyContactsActivity.this, "Failed to process request", Toast.LENGTH_SHORT).show();
                }
            }
            @Override
            public void onFailure(@NonNull Call<SimpleResponse> call, @NonNull Throwable t) {
                Toast.makeText(EmergencyContactsActivity.this, "Network Error", Toast.LENGTH_SHORT).show();
            }
        });
    }
}