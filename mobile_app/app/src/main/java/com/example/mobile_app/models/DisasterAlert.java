package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

// This class represents a single disaster alert object from our API
public class DisasterAlert {

    @SerializedName("id")
    private int id;

    @SerializedName("name")
    private String name;

    @SerializedName("type")
    private String type;

    @SerializedName("status")
    private String status;
    @SerializedName("affected_area")
    private String affectedArea; // WKT String



    // Getters
    public int getId() {
        return id;
    }

    public String getName() {
        return name;
    }

    public String getType() {
        return type;
    }

    public String getStatus() {
        return status;
    }

    public String getAffectedArea()
    {
        return affectedArea;
    }
}