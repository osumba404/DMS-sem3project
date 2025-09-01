package com.example.mobile_app.network;

import com.google.gson.annotations.SerializedName;

public class UpdateProfileRequest {
    @SerializedName("user_id")
    private int userId;
    @SerializedName("full_name")
    private String fullName;
    @SerializedName("phone_number")
    private String phoneNumber;
    @SerializedName("password") // Optional, can be null
    private String password;

    // Constructor for updating details only
    public UpdateProfileRequest(int userId, String fullName, String phoneNumber) {
        this.userId = userId;
        this.fullName = fullName;
        this.phoneNumber = phoneNumber;
    }

    // Constructor for updating with a new password
    public UpdateProfileRequest(int userId, String fullName, String phoneNumber, String password) {
        this.userId = userId;
        this.fullName = fullName;
        this.phoneNumber = phoneNumber;
        this.password = password;
    }
}