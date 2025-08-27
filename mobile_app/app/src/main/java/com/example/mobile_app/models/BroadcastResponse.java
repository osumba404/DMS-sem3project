package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class BroadcastResponse {
    @SerializedName("status")
    private String status;
    @SerializedName("message")
    private String message;
    @SerializedName("data")
    private List<BroadcastMessage> data;

    // Getters
    public String getStatus() { return status; }
    public String getMessage() { return message; }
    public List<BroadcastMessage> getData() { return data; }
}