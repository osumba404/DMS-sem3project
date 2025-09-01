package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class ProfileData {
    @SerializedName("full_name")
    private String fullName;
    @SerializedName("email")
    private String email;
    @SerializedName("phone_number")
    private String phoneNumber;

    // Getters
    public String getFullName() { return fullName; }
    public String getEmail() { return email; }
    public String getPhoneNumber() { return phoneNumber; }
}