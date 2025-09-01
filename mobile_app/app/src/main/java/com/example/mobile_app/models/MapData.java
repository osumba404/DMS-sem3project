package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class MapData {
    @SerializedName("shelters")
    private List<Shelter> shelters;
    @SerializedName("disasters")
    private List<DisasterAlert> disasters; // We can reuse the DisasterAlert model

    // Getters
    public List<Shelter> getShelters() { return shelters; }
    public List<DisasterAlert> getDisasters() { return disasters; }
}