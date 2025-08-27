package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class UnifiedAlertsResponse {
    @SerializedName("status")
    private String status;
    @SerializedName("message")
    private String message;

    // The 'data' field now contains a list of our new unified Alert objects
    @SerializedName("data")
    private List<Alert> data;

    // Getters
    public String getStatus() { return status; }
    public String getMessage() { return message; }
    public List<Alert> getData() { return data; }
}