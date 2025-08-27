package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class ContactsResponse {
    @SerializedName("status")
    private String status;

    @SerializedName("message")
    private String message;

    // THE FIX: The 'data' field is now an object of type ContactData,
    // which perfectly matches the JSON structure from the server.
    @SerializedName("data")
    private ContactData data;

    // Getters
    public String getStatus() { return status; }
    public String getMessage() { return message; }
    public ContactData getData() { return data; }
}