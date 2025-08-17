package com.example.mobile_app.network;

import com.google.gson.annotations.SerializedName;

// This class is used to create the JSON body for the mark_safe API call
public class MarkSafeRequest {

    @SerializedName("user_id")
    private int userId;

    @SerializedName("latitude")
    private double latitude;

    @SerializedName("longitude")
    private double longitude;

    public MarkSafeRequest(int userId, double latitude, double longitude) {
        this.userId = userId;
        this.latitude = latitude;
        this.longitude = longitude;
    }
}