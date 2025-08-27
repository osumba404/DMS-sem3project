package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class Shelter {
    @SerializedName("id")
    private int id;
    @SerializedName("name")
    private String name;
    @SerializedName("latitude")
    private double latitude;
    @SerializedName("longitude")
    private double longitude;
    @SerializedName("capacity")
    private int capacity;
    @SerializedName("current_occupancy")
    private int currentOccupancy;
    @SerializedName("status")
    private String status;

    // Getters
    public int getId() { return id; }
    public String getName() { return name; }
    public double getLatitude() { return latitude; }
    public double getLongitude() { return longitude; }
    public int getCapacity() { return capacity; }
    public int getCurrentOccupancy() { return currentOccupancy; }
    public String getStatus() { return status; }
}