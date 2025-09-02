package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;

public class UserReport {
    @SerializedName("id")
    private int id;

    @SerializedName("user_id")
    private int userId;

    @SerializedName("title")
    private String title;

    @SerializedName("description")
    private String description;

    @SerializedName("category")
    private String category;

    @SerializedName("priority")
    private String priority;

    @SerializedName("status")
    private String status;

    @SerializedName("latitude")
    private Double latitude;

    @SerializedName("longitude")
    private Double longitude;

    @SerializedName("address")
    private String address;

    @SerializedName("image_url")
    private String imageUrl;

    @SerializedName("admin_notes")
    private String adminNotes;

    @SerializedName("created_at")
    private String createdAt;

    @SerializedName("updated_at")
    private String updatedAt;

    @SerializedName("relative_time")
    private String relativeTime;

    @SerializedName("reporter_name")
    private String reporterName;

    // Constructors
    public UserReport() {}

    public UserReport(String title, String description, String category, String priority, 
                     Double latitude, Double longitude, String address) {
        this.title = title;
        this.description = description;
        this.category = category;
        this.priority = priority;
        this.latitude = latitude;
        this.longitude = longitude;
        this.address = address;
    }

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public int getUserId() { return userId; }
    public void setUserId(int userId) { this.userId = userId; }

    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }

    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }

    public String getCategory() { return category; }
    public void setCategory(String category) { this.category = category; }

    public String getPriority() { return priority; }
    public void setPriority(String priority) { this.priority = priority; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }

    public Double getLatitude() { return latitude; }
    public void setLatitude(Double latitude) { this.latitude = latitude; }

    public Double getLongitude() { return longitude; }
    public void setLongitude(Double longitude) { this.longitude = longitude; }

    public String getAddress() { return address; }
    public void setAddress(String address) { this.address = address; }

    public String getImageUrl() { return imageUrl; }
    public void setImageUrl(String imageUrl) { this.imageUrl = imageUrl; }

    public String getAdminNotes() { return adminNotes; }
    public void setAdminNotes(String adminNotes) { this.adminNotes = adminNotes; }

    public String getCreatedAt() { return createdAt; }
    public void setCreatedAt(String createdAt) { this.createdAt = createdAt; }

    public String getUpdatedAt() { return updatedAt; }
    public void setUpdatedAt(String updatedAt) { this.updatedAt = updatedAt; }

    public String getRelativeTime() { return relativeTime; }
    public void setRelativeTime(String relativeTime) { this.relativeTime = relativeTime; }

    public String getReporterName() { return reporterName; }
    public void setReporterName(String reporterName) { this.reporterName = reporterName; }

    // Helper methods
    public String getStatusColor() {
        switch (status != null ? status.toLowerCase() : "") {
            case "submitted": return "#6c757d";
            case "under review": return "#17a2b8";
            case "investigating": return "#ffc107";
            case "resolved": return "#28a745";
            case "closed": return "#343a40";
            default: return "#6c757d";
        }
    }

    public String getPriorityColor() {
        switch (priority != null ? priority.toLowerCase() : "") {
            case "critical": return "#dc3545";
            case "high": return "#fd7e14";
            case "medium": return "#ffc107";
            case "low": return "#28a745";
            default: return "#6c757d";
        }
    }

    public String getCategoryIcon() {
        switch (category != null ? category.toLowerCase() : "") {
            case "incident": return "🚨";
            case "hazard": return "⚠️";
            case "infrastructure": return "🏗️";
            case "safety": return "🛡️";
            default: return "📝";
        }
    }
}
