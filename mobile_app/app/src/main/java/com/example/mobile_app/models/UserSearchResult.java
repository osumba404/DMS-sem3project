package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class UserSearchResult {
    @SerializedName("id")
    private int id;
    @SerializedName("full_name")
    private String fullName;
    @SerializedName("email")
    private String email;

    // Getters
    public int getId() { return id; }
    public String getFullName() { return fullName; }
    public String getEmail() { return email; }
}