Of course, here is the requested project structure formatted in Markdown.

1. High-Level Project Directory Overview

This initial structure separates the main components of the system for organized development and deployment.

code
Code
download
content_copy
expand_less

disaster_management_and_evacuation_system/
├── backend/
├── admin_portal/
├── responder_portal/
└── mobile_app/
2. Admin Portal (PHP)
Folder & File Structure:
code
Code
download
content_copy
expand_less
IGNORE_WHEN_COPYING_START
IGNORE_WHEN_COPYING_END
admin_portal/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── includes/
│   ├── db_connect.php
│   ├── header.php
│   ├── footer.php
│   └── auth_check.php
├── login.php
├── logout.php
├── dashboard.php
├── disasters.php
├── shelters.php
├── users.php
├── responders.php
├── broadcast.php
├── reports.php
└── index.php
File Descriptions:

assets/: This folder holds all static files.

css/style.css: Main stylesheet for all pages.

js/script.js: For basic JavaScript functionalities like form validation or simple dynamic actions (e.g., confirmation dialogs).

includes/: Contains reusable PHP code snippets included in other files.

db_connect.php: Handles the connection to the MySQL database.

header.php: Contains the opening HTML, head section, and navigation menu. Included at the top of every page.

footer.php: Contains the closing HTML tags and any footer content. Included at the bottom of every page.

auth_check.php: Checks if an admin is logged in. Included in every page except login.php to secure the portal.

login.php: The login page for administrators. It handles user authentication and session creation.

logout.php: Destroys the user session and redirects to the login page.

dashboard.php: The main landing page after login, showing key statistics and an overview.

disasters.php: A single file to handle Creating, Reading, Updating, and Deleting (CRUD) disaster events. It will display a list of disasters and have forms for creating/editing them.

shelters.php: Manages CRUD operations for shelters and help camps.

users.php: Displays a list of all registered public users.

responders.php: Manages CRUD operations for emergency responder accounts.

broadcast.php: A page with a form to send broadcast messages (push/SMS) to users.

reports.php: Generates and displays post-disaster analytics and reports.

index.php: The entry point. It typically checks if the user is logged in and redirects to dashboard.php or login.php accordingly.

3. Emergency Responder Portal (Raw PHP)

This portal is even more streamlined, focusing only on the essential tasks for on-the-ground personnel.

Folder & File Structure:
code
Code
download
content_copy
expand_less
IGNORE_WHEN_COPYING_START
IGNORE_WHEN_COPYING_END
responder_portal/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── includes/
│   ├── db_connect.php
│   ├── header.php
│   ├── footer.php
│   └── auth_check.php
├── login.php
├── logout.php
├── dashboard.php
├── update_shelter.php
├── submit_report.php
└── index.php```

### File Descriptions:
*   **`assets/`** & **`includes/`**: These folders serve the same purpose as in the Admin Portal but are specific to the responder's interface. `auth_check.php` will verify a responder's session.
*   **`login.php`**: The login page for emergency responders.
*   **`logout.php`**: Ends the responder's session.
*   **`dashboard.php`**: The responder's main page, showing assigned shelters or zones and a summary of tasks.
*   **`update_shelter.php`**: Allows a responder to view a list of shelters and update their status, capacity, and available supplies. The page can take a shelter ID as a URL parameter (e.g., `update_shelter.php?id=123`) to show a form for a specific shelter.
*   **`submit_report.php`**: A simple form for responders to submit live field reports to the central system.
*   **`index.php`**: Redirects logged-in responders to their `dashboard.php` or new responders to `login.php`.

# 4. Mobile App (Android - Java + XML)
The primary interface for the public, including citizens and tourists.

### Folder & File Structure (within `app/src/main/`):

java/com/example/disasterapp/
├── activities/
│ ├── SplashActivity.java
│ ├── AuthActivity.java
│ ├── MainActivity.java
│ ├── ProfileActivity.java
│ ├── EmergencyContactsActivity.java
│ ├── TouristModeActivity.java
│ └── OfflineResourcesActivity.java
├── adapters/
│ ├── ShelterListAdapter.java
│ └── EmergencyContactAdapter.java
├── fragments/
│ ├── HomeFragment.java
│ ├── MapViewFragment.java
│ ├── AlertsFragment.java
│ └── SettingsFragment.java
├── models/
│ ├── User.java
│ ├── Shelter.java
│ └── DisasterAlert.java
├── network/
│ ├── ApiClient.java
│ └── ApiService.java
├── services/
│ ├── AppFirebaseMessagingService.java
│ └── LocationUpdatesService.java
├── util/
│ ├── PermissionManager.java
│ └── SharedPreferencesHelper.java
└── db/
├── AppDatabase.java
├── OfflineContentDao.java
└── OfflineContent.java
res/
├── layout/
│ ├── activity_main.xml
│ ├── fragment_home.xml
│ ├── item_shelter_list.xml
│ └── ... (layouts for each activity, fragment, and list item)
├── drawable/
│ ├── ic_shelter.xml
│ └── ... (all icons and image assets)
├── values/
│ ├── strings.xml
│ ├── colors.xml
│ └── styles.xml
├── xml/
│ └── network_security_config.xml
└── raw/
├── first_aid_guide.html
└── safety_protocols.html

code
Code
download
content_copy
expand_less
IGNORE_WHEN_COPYING_START
IGNORE_WHEN_COPYING_END
# 5. Database Schema (MySQL)
The foundational data structure for the entire system.

### Database Tables:
| Table Name | Description |
| :--- | :--- |
| **`users`** | Stores information about the public users of the mobile app. |
| **`emergency_contacts`** | Stores emergency contact information linked to each user. |
| **`admins`** | Contains credentials and details for admin portal users. |
| **`responders`** | Holds information about the emergency responders. |
| **`disasters`** | A central table to define and track active or past disaster events. |
| **`shelters`** | Contains details of all registered safe shelters and help camps. |
| **`broadcast_messages`** | A log of all push and SMS alerts sent by admins. |
| **`field_reports`** | Stores reports submitted by emergency responders from the ground. |
| **`awareness_content`** | Holds educational materials for the pre-disaster phase. |
| **`user_locations`** | Tracks the location history of users for post-disaster analysis. |
| **`shelter_updates`** | Logs changes in shelter status and occupancy over time. |

### Table Details:

#### `users`
*   `id` (Primary Key, INT)
*   `full_name` (VARCHAR)
*   `email` (VARCHAR, Unique)
*   `password` (VARCHAR, Hashed)
*   `phone_number` (VARCHAR, Unique)
*   `last_known_latitude` (DECIMAL)
*   `last_known_longitude` (DECIMAL)
*   `is_safe` (BOOLEAN)
*   `is_tourist` (BOOLEAN)
*   `device_token` (VARCHAR)
*   `created_at` (TIMESTAMP)

#### `emergency_contacts`
*   `id` (Primary Key, INT)
*   `user_id` (Foreign Key to `users`)
*   `name` (VARCHAR)
*   `phone_number` (VARCHAR)
*   `relationship` (VARCHAR)

#### `admins`
*   `id` (Primary Key, INT)
*   `username` (VARCHAR, Unique)
*   `password` (VARCHAR, Hashed)
*   `full_name` (VARCHAR)
*   `role` (VARCHAR)

#### `responders`
*   `id` (Primary Key, INT)
*   `username` (VARCHAR, Unique)
*   `password` (VARCHAR, Hashed)
*   `full_name` (VARCHAR)
*   `team` (VARCHAR)
*   `assigned_zone` (GEOMETRY)

#### `disasters`
*   `id` (Primary Key, INT)
*   `name` (VARCHAR)
*   `type` (ENUM: 'Flood', 'Earthquake', 'Hurricane', 'Wildfire')
*   `status` (ENUM: 'Prepare', 'Evacuate', 'All Clear')
*   `affected_area` (GEOMETRY)
*   `created_by_admin_id` (Foreign Key to `admins`)
*   `created_at` (TIMESTAMP)

#### `shelters`
*   `id` (Primary Key, INT)
*   `name` (VARCHAR)
*   `location` (POINT)
*   `capacity` (INT)
*   `current_occupancy` (INT)
*   `available_supplies` (JSON)
*   `status` (ENUM: 'Open', 'Full', 'Closed')
*   `last_updated_by_responder_id` (Foreign Key to `responders`)
*   `updated_at` (TIMESTAMP)

#### `broadcast_messages`
*   `id` (Primary Key, INT)
*   `admin_id` (Foreign Key to `admins`)
*   `disaster_id` (Foreign Key to `disasters`, nullable)
*   `title` (VARCHAR)
*   `body` (TEXT)
*   `target_audience` (ENUM: 'All', 'Affected Zone', 'Tourists')
*   `sent_at` (TIMESTAMP)

#### `field_reports`
*   `id` (Primary Key, INT)
*   `responder_id` (Foreign Key to `responders`)
*   `disaster_id` (Foreign Key to `disasters`)
*   `report_content` (TEXT)
*   `location` (POINT)
*   `created_at` (TIMESTAMP)

#### `awareness_content`
*   `id` (Primary Key, INT)
*   `title` (VARCHAR)
*   `content` (TEXT)
*   `type` (ENUM: 'First Aid', 'Safety Tip', 'Evacuation Guide')
*   `language` (VARCHAR)

#### `user_locations`
*   `id` (Primary Key, BIGINT)
*   `user_id` (Foreign Key to `users`)
*   `location` (POINT)
*   `timestamp` (TIMESTAMP)

#### `shelter_updates`
*   `id` (Primary Key, INT)
*   `shelter_id` (Foreign Key to `shelters`)
*   `responder_id` (Foreign Key to `responders`)
*   `occupancy` (INT)
*   `status` (ENUM: 'Open', 'Full', 'Closed')
*   `notes` (TEXT)
*   `updated_at` (TIMESTAMP)