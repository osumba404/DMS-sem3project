package com.example.mobile_app.network;

import com.example.mobile_app.models.AlertsResponse;
import com.example.mobile_app.models.AuthResponse;
import com.example.mobile_app.models.SimpleResponse;
import com.example.mobile_app.models.User;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.POST;

public interface ApiService {

    // These two endpoints return a User object inside the 'data' field
    @POST("users/register.php")
    Call<AuthResponse> registerUser(@Body User user);

    @POST("users/login.php")
    Call<AuthResponse> loginUser(@Body User user);

    // This endpoint returns a List of alerts inside the 'data' field
    @GET("disasters/get_alerts.php")
    Call<AlertsResponse> getActiveAlerts();

    // This endpoint returns ONLY a status and message, no 'data' field
    @POST("users/mark_safe.php")
    Call<SimpleResponse> markUserSafe(@Body MarkSafeRequest request);
}