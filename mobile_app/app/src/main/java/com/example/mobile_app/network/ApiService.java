package com.example.mobile_app.network;

import com.example.mobile_app.models.AlertsResponse;
import com.example.mobile_app.models.AuthResponse;
import com.example.mobile_app.models.ContactsResponse;
import com.example.mobile_app.models.NotificationsResponse;
import com.example.mobile_app.models.SheltersResponse;
import com.example.mobile_app.models.SimpleResponse;
import com.example.mobile_app.models.User;
import com.example.mobile_app.models.UserSearchResponse;
import com.example.mobile_app.models.BroadcastResponse;

import com.example.mobile_app.models.UnifiedAlertsResponse;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Query;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;

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

    @GET("shelters/get_nearby_shelters.php")
    Call<SheltersResponse> getNearbyShelters(
            @Query("lat") double latitude,
            @Query("lon") double longitude
    );
    @GET("users/get_contacts.php")
    Call<ContactsResponse> getEmergencyContacts(@Query("user_id") int userId);

    @FormUrlEncoded // Used when sending form data instead of a JSON body
    @POST("users/add_contact.php")
    Call<SimpleResponse> addEmergencyContact(
            @Field("user_id") int userId,
            @Field("name") String name,
            @Field("phone_number") String phoneNumber,
            @Field("relationship") String relationship
    );

    @GET("users/search_users.php")
    Call<UserSearchResponse> searchUsers(
            @Query("query") String searchTerm,
            @Query("user_id") int currentUserId
    );

    @FormUrlEncoded
    @POST("users/accept_contact_request.php")
    Call<SimpleResponse> acceptContactRequest(@Field("request_id") int requestId);

    @FormUrlEncoded
    @POST("users/reject_contact_request.php")
    Call<SimpleResponse> rejectContactRequest(@Field("request_id") int requestId);


    @FormUrlEncoded
    @POST("users/delete_contact.php")
    Call<SimpleResponse> deleteEmergencyContact(@Field("contact_id") int contactId);

    @FormUrlEncoded
    @POST("users/add_contact_request.php")
    Call<SimpleResponse> addContactRequest(
            @Field("user_id") int userId,
            @Field("contact_user_id") int contactUserId,
            @Field("relationship") String relationship
    );

//    @GET("users/get_broadcast_history.php")
//    Call<BroadcastResponse> getBroadcastHistory();

    @GET("users/get_notifications.php")
    Call<NotificationsResponse> getNotifications(@Query("user_id") int userId);

    @GET("users/get_unified_alerts.php")
    Call<UnifiedAlertsResponse> getUnifiedAlerts(@Query("user_id") int userId);
}