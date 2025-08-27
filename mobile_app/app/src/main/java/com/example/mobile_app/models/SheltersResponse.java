package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class SheltersResponse {
    @SerializedName("status")
    private String status;
    @SerializedName("message")
    private String message;
    @SerializedName("data")
    private List<Shelter> data;

    // Getters
    public String getStatus() { return status; }
    public String getMessage() { return message; }
    public List<Shelter> getData() { return data; }
}