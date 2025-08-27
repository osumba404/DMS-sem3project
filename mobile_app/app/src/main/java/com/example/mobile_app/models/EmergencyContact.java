package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class EmergencyContact {
    // This 'id' is the request_id from the emergency_contacts table
    @SerializedName("request_id")
    private int id;

    // This is the ID of the other user in the relationship
    @SerializedName("user_id")
    private int userId;

    // These fields come from the JOIN with the 'users' table
    @SerializedName("full_name")
    private String name;
    @SerializedName("phone_number")
    private String phoneNumber;

    // This is the relationship text
    @SerializedName("relationship")
    private String relationship;

    // Getters
    public int getId() { return id; }
    public int getUserId() { return userId; }
    public String getName() { return name; }
    public String getPhoneNumber() { return phoneNumber; }
    public String getRelationship() { return relationship; }
}