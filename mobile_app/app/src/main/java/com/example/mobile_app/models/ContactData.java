package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class ContactData {

    @SerializedName("accepted_contacts")
    private List<EmergencyContact> acceptedContacts;

    @SerializedName("pending_sent_requests")
    private List<EmergencyContact> pendingSentRequests;

    @SerializedName("pending_received_requests")
    private List<EmergencyContact> pendingReceivedRequests;

    // Getters
    public List<EmergencyContact> getAcceptedContacts() {
        return acceptedContacts;
    }

    public List<EmergencyContact> getPendingSentRequests() {
        return pendingSentRequests;
    }

    public List<EmergencyContact> getPendingReceivedRequests() {
        return pendingReceivedRequests;
    }
}