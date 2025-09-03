package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class Shelter {
    @SerializedName("id")
    private int id;
    @SerializedName("name")
    private String name;
    @SerializedName("address")
    private String address;
    @SerializedName("phone")
    private String phone;
    @SerializedName("latitude")
    private double latitude;
    @SerializedName("longitude")
    private double longitude;
    @SerializedName("capacity")
    private int capacity;
    @SerializedName("current_occupancy")
    private int currentOccupancy;
    @SerializedName("food_supply")
    private String foodSupply;
    @SerializedName("water_supply")
    private String waterSupply;
    @SerializedName("medical_supply")
    private String medicalSupply;
    @SerializedName("blankets_available")
    private int blanketsAvailable;
    @SerializedName("status")
    private String status;
    @SerializedName("status_label")
    private String statusLabel;
    @SerializedName("distance_km")
    private double distanceKm;
    @SerializedName("availability_percentage")
    private double availabilityPercentage;

    // Getters
    public int getId() { return id; }
    public String getName() { return name; }
    public String getAddress() { return address; }
    public String getPhone() { return phone; }
    public double getLatitude() { return latitude; }
    public double getLongitude() { return longitude; }
    public int getCapacity() { return capacity; }
    public int getCurrentOccupancy() { return currentOccupancy; }
    public String getFoodSupply() { return foodSupply; }
    public String getWaterSupply() { return waterSupply; }
    public String getMedicalSupply() { return medicalSupply; }
    public int getBlanketsAvailable() { return blanketsAvailable; }
    public String getStatus() { return status; }
    public String getStatusLabel() { return statusLabel; }
    public double getDistanceKm() { return distanceKm; }
    public double getAvailabilityPercentage() { return availabilityPercentage; }
    
    // Helper method to get formatted info for map marker
    public String getInfoSnippet() {
        return String.format(
            "Address: %s\n" +
            "Phone: %s\n" +
            "Capacity: %d (%.0f%% available)\n" +
            "Supplies - Food: %s, Water: %s, Medical: %s\n" +
            "Blankets: %d\n" +
            "Status: %s\n" +
            "Distance: %.1f km",
            address != null ? address : "N/A",
            phone != null ? phone : "N/A",
            capacity,
            availabilityPercentage,
            foodSupply != null ? foodSupply : "N/A",
            waterSupply != null ? waterSupply : "N/A",
            medicalSupply != null ? medicalSupply : "N/A",
            blanketsAvailable,
            statusLabel != null ? statusLabel : status,
            distanceKm
        );
    }
}