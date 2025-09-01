package com.example.mobile_app.network;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class ApiClient {

    // IMPORTANT: This is the base URL for your PHP backend.
    // Use http://10.0.2.2/ for the Android emulator to connect to your localhost.
//    private static final String BASE_URL = "http://192.168.0.101:8000/backend/api/";
    private static final String BASE_URL = "http://192.168.0.101:8000/backend/api/";


    private static Retrofit retrofit = null;

    public static Retrofit getClient() {
        if (retrofit == null) {
            retrofit = new Retrofit.Builder()
                    .baseUrl(BASE_URL)
                    .addConverterFactory(GsonConverterFactory.create())
                    .build();
        }
        return retrofit;
    }

    public static ApiService getApiService() {
        return getClient().create(ApiService.class);
    }
}