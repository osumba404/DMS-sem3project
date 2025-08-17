package com.example.mobile_app.network;

import com.example.mobile_app.models.AuthResponse;
import com.example.mobile_app.models.User;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.POST;

public interface ApiService {

    @POST("users/register.php")
    Call<AuthResponse> registerUser(@Body User user);

    @POST("users/login.php")
    Call<AuthResponse> loginUser(@Body User user);

}