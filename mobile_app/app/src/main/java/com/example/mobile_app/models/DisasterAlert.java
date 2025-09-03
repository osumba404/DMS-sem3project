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
    
    @SerializedName("severity")
    private String severity;
    
    @SerializedName("description")
    private String description;
    
    @SerializedName("affected_area")
    private String affectedArea; // WKT String
    
    @SerializedName("created_at")
    private String createdAt;
    
    @SerializedName("location")
    private String location;
    
    @SerializedName("relative_time")
    private String relativeTime;

    // Getters
    public int getId() {
        return id;
    }

    public String getName() {
        return name != null ? name : "";
    }

    public String getType() {
        return type != null ? type : "";
    }

    public String getStatus() {
        return status != null ? status : "";
    }
    
    public String getSeverity() {
        return severity != null ? severity : "medium"; // Default to medium if not specified
    }
    
    public String getDescription() {
        return description != null ? description : "";
    }

    public String getAffectedArea() {
        return affectedArea != null ? affectedArea : "";
    }
    
    public String getCreatedAt() {
        return createdAt != null ? createdAt : "";
    }
    
    public String getLocation() {
        return location != null ? location : "";
    }
    
    public String getRelativeTime() {
        return relativeTime != null ? relativeTime : "";
    }

    // Setters (if needed for your use case)
    public void setSeverity(String severity) {
        this.severity = severity;
    }
    
    public void setDescription(String description) {
        this.description = description;
    }
}