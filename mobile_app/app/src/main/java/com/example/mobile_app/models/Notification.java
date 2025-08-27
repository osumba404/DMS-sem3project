package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class Notification {
    @SerializedName("id")
    private int id;
    @SerializedName("title")
    private String title;
    @SerializedName("message")
    private String message;
    @SerializedName("created_at")
    private String createdAt;

    // Getters
    public int getId() { return id; }
    public String getTitle() { return title; }
    public String getMessage() { return message; }
    public String getCreatedAt() { return createdAt; }
}