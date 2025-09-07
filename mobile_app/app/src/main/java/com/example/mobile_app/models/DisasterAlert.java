package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;
import org.json.JSONArray;
import org.json.JSONObject;

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
    private String affectedArea; // GeoJSON or WKT String
    
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

    // Parse latitude from GeoJSON or WKT string
    public double getLatitude() {
        if (affectedArea == null || affectedArea.isEmpty()) {
            return 0.0;
        }
        
        try {
            // First try to parse as GeoJSON
            if (affectedArea.startsWith("{")) {
                // Parse GeoJSON format
                JSONObject geoJson = new JSONObject(affectedArea);
                if (geoJson.has("coordinates")) {
                    JSONArray coords = geoJson.getJSONArray("coordinates");
                    if (coords.length() > 0) {
                        // For polygon: coordinates[0] is the outer ring
                        JSONArray outerRing = coords.getJSONArray(0);
                        if (outerRing.length() > 0) {
                            // Get first coordinate pair [longitude, latitude]
                            JSONArray firstCoord = outerRing.getJSONArray(0);
                            if (firstCoord.length() >= 2) {
                                return firstCoord.getDouble(1); // latitude
                            }
                        }
                    }
                }
            }
            // Fallback: try to parse as WKT string (POLYGON or POINT)
            else if (affectedArea.startsWith("POLYGON")) {
                // Extract first coordinate from polygon
                String coordsSection = affectedArea.substring(affectedArea.indexOf("((") + 2, affectedArea.indexOf("))"));
                String[] coordPairs = coordsSection.split(",");
                if (coordPairs.length > 0) {
                    String[] firstPair = coordPairs[0].trim().split("\\s+");
                    if (firstPair.length >= 2) {
                        return Double.parseDouble(firstPair[1]); // latitude
                    }
                }
            }
            else if (affectedArea.startsWith("POINT")) {
                String coords = affectedArea
                    .replace("POINT(", "")
                    .replace(")", "")
                    .trim();
                String[] parts = coords.split("\\s+");
                if (parts.length >= 2) {
                    // WKT format is (longitude latitude)
                    return Double.parseDouble(parts[1]);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return 0.0;
    }

    // Parse longitude from GeoJSON or WKT string
    public double getLongitude() {
        if (affectedArea == null || affectedArea.isEmpty()) {
            return 0.0;
        }
        
        try {
            // First try to parse as GeoJSON
            if (affectedArea.startsWith("{")) {
                // Parse GeoJSON format
                JSONObject geoJson = new JSONObject(affectedArea);
                if (geoJson.has("coordinates")) {
                    JSONArray coords = geoJson.getJSONArray("coordinates");
                    if (coords.length() > 0) {
                        // For polygon: coordinates[0] is the outer ring
                        JSONArray outerRing = coords.getJSONArray(0);
                        if (outerRing.length() > 0) {
                            // Get first coordinate pair [longitude, latitude]
                            JSONArray firstCoord = outerRing.getJSONArray(0);
                            if (firstCoord.length() >= 1) {
                                return firstCoord.getDouble(0); // longitude
                            }
                        }
                    }
                }
            }
            // Fallback: try to parse as WKT string (POLYGON or POINT)
            else if (affectedArea.startsWith("POLYGON")) {
                // Extract first coordinate from polygon
                String coordsSection = affectedArea.substring(affectedArea.indexOf("((") + 2, affectedArea.indexOf("))"));
                String[] coordPairs = coordsSection.split(",");
                if (coordPairs.length > 0) {
                    String[] firstPair = coordPairs[0].trim().split("\\s+");
                    if (firstPair.length >= 1) {
                        return Double.parseDouble(firstPair[0]); // longitude
                    }
                }
            }
            else if (affectedArea.startsWith("POINT")) {
                String coords = affectedArea
                    .replace("POINT(", "")
                    .replace(")", "")
                    .trim();
                String[] parts = coords.split("\\s+");
                if (parts.length >= 1) {
                    // WKT format is (longitude latitude)
                    return Double.parseDouble(parts[0]);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return 0.0;
    }

    // Setters (if needed for your use case)
    public void setSeverity(String severity) {
        this.severity = severity;
    }
    
    public void setDescription(String description) {
        this.description = description;
    }
}