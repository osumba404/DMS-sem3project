package com.example.mobile_app.util;

import android.content.Context;
import android.content.SharedPreferences;

import com.example.mobile_app.models.User;

public class SessionManager {

    private static final String PREF_NAME = "DisasterAppSession";
    private static final String KEY_USER_ID = "user_id";
    private static final String KEY_FULL_NAME = "full_name";
    private static final String KEY_IS_LOGGED_IN = "is_logged_in";

    private SharedPreferences pref;
    private SharedPreferences.Editor editor;
    private Context _context;

    // Constructor
    public SessionManager(Context context) {
        this._context = context;
        pref = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = pref.edit();
    }

    /**
     * Create login session
     */
    public void createLoginSession(User user) {
        // Storing login value as TRUE
        editor.putBoolean(KEY_IS_LOGGED_IN, true);

        // Storing user details in pref
        editor.putInt(KEY_USER_ID, user.getId());
        editor.putString(KEY_FULL_NAME, user.getFullName());

        // commit changes
        editor.commit();
    }

    /**
     * Get stored session data
     */
    public int getUserId() {
        return pref.getInt(KEY_USER_ID, 0); // Return 0 if user ID not found
    }

    public String getFullName() {
        return pref.getString(KEY_FULL_NAME, null);
    }

    /**
     * Check login method will check user login status
     */
    public boolean isLoggedIn() {
        return pref.getBoolean(KEY_IS_LOGGED_IN, false);
    }

    /**
     * Clear session details
     */
    public void logoutUser() {
        // Clearing all data from Shared Preferences
        editor.clear();
        editor.commit();
    }
}