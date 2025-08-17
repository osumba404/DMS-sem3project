package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class AuthResponse {
    @SerializedName("status")
    private String status;

    @SerializedName("message")
    private String message;

    @SerializedName("data")
    private User data; // The 'data' field in the JSON will contain a User object

    // Getters
    public String getStatus() {
        return status;
    }

    public String getMessage() {
        return message;
    }

    public User getData() {
        return data;
    }
}