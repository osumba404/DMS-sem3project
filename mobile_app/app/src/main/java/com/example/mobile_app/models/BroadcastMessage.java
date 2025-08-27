package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class BroadcastMessage {
    @SerializedName("id")
    private int id;
    @SerializedName("title")
    private String title;
    @SerializedName("body")
    private String body;
    @SerializedName("sent_at")
    private String sentAt;

    // Getters
    public int getId() { return id; }
    public String getTitle() { return title; }
    public String getBody() { return body; }
    public String getSentAt() { return sentAt; }
}