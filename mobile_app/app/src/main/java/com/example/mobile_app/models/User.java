package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

// This class represents a User object, matching the API and database structure.
public class User {

    // @SerializedName links the JSON key from the API to our Java variable.
    @SerializedName("user_id") // Used in the login response
    private int id;

    @SerializedName("full_name")
    private String fullName;

    @SerializedName("email")
    private String email;

    @SerializedName("password")
    private String password;

    @SerializedName("phone_number")
    private String phoneNumber;

    // Constructor for registration
    public User(String fullName, String email, String password, String phoneNumber) {
        this.fullName = fullName;
        this.email = email;
        this.password = password;
        this.phoneNumber = phoneNumber;
    }
    
    // Constructor for login
    public User(String email, String password) {
        this.email = email;
        this.password = password;
    }

    // Getters
    public int getId() {
        return id;
    }

    public String getFullName() {
        return fullName;
    }

    public String getEmail() {
        return email;
    }
}