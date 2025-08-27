package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

// A unified model to represent either a Broadcast or a personal Notification
public class Alert {

    // Common fields for both types
    @SerializedName("id")
    private int id;
    @SerializedName("title")
    private String title;
    @SerializedName("body")
    private String body;
    @SerializedName("timestamp")
    private String timestamp;

    // This field tells us what kind of alert it is ("broadcast" or "notification")
    @SerializedName("type")
    private String type;

    // This field is ONLY for personal notifications
    @SerializedName("sender_name")
    private String senderName;

    // Getters
    public int getId() { return id; }
    public String getTitle() { return title; }
    public String getBody() { return body; }
    public String getTimestamp() { return timestamp; }
    public String getType() { return type; }
    public String getSenderName() { return senderName; }
}