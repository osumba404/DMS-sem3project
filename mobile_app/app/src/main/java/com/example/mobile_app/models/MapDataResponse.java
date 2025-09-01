package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class MapDataResponse {
    @SerializedName("status")
    private String status;
    @SerializedName("message")
    private String message;
    @SerializedName("data")
    private MapData data;

    // Getters
    public String getStatus() { return status; }
    public String getMessage() { return message; }
    public MapData getData() { return data; }
}