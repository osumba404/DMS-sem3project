package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

// This class is for any API response that only returns a status and a message.
public class SimpleResponse {
    @SerializedName("status")
    private String status;

    @SerializedName("message")
    private String message;

    // Getters
    public String getStatus() { return status; }
    public String getMessage() { return message; }
}