Got it ✅
Here’s your `.md` version with a **clickable Table of Contents** at the top so you can quickly jump between sections when viewing in GitHub, VS Code, or any Markdown viewer.

````markdown
# Disaster Management & Evacuation System

## Table of Contents
- [1. High-Level Project Directory Overview](#1-high-level-project-directory-overview)
- [2. Admin Portal (PHP)](#2-admin-portal-php)
- [3. Emergency Responder Portal (Raw PHP)](#3-emergency-responder-portal-raw-php)
- [4. Mobile App (Android - Java + XML)](#4-mobile-app-android---java--xml)
- [5. Database Schema (MySQL)](#5-database-schema-mysql)
  - [Database Tables Overview](#database-tables-overview)
  - [Table Details](#table-details)

---

## 1. High-Level Project Directory Overview
This initial structure separates the main components of the system for organized development and deployment.

```plaintext
disaster_management_and_evacuation_system/
├── backend/
├── admin_portal/
├── responder_portal/
└── mobile_app/
````

---

## 2. Admin Portal (PHP)

### Folder & File Structure:

```plaintext
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
```

### File Descriptions:

* **assets/**: Holds all static files.

  * **css/style.css**: Main stylesheet for all pages.
  * **js/script.js**: Basic JavaScript functionalities like form validation or confirmation dialogs.
* **includes/**: Contains reusable PHP code snippets included in other files.

  * **db\_connect.php**: Handles MySQL database connection.
  * **header.php**: Opening HTML, head section, and navigation menu.
  * **footer.php**: Closing HTML tags and footer content.
  * **auth\_check.php**: Ensures admin is logged in.
* **login.php**: Admin login page for authentication.
* **logout.php**: Ends the session and redirects to login.
* **dashboard.php**: Landing page with statistics and overview.
* **disasters.php**: CRUD operations for disaster events.
* **shelters.php**: CRUD operations for shelters/help camps.
* **users.php**: List of registered public users.
* **responders.php**: CRUD operations for emergency responders.
* **broadcast.php**: Send broadcast messages (push/SMS).
* **reports.php**: Post-disaster analytics and reports.
* **index.php**: Entry point; redirects to `dashboard.php` or `login.php`.

---

## 3. Emergency Responder Portal (Raw PHP)

### Folder & File Structure:

```plaintext
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
└── index.php
```

### File Descriptions:

* **assets/** & **includes/**: Same function as Admin Portal.
* **login.php**: Responder login.
* **logout.php**: Ends session.
* **dashboard.php**: Main page showing assigned shelters/zones.
* **update\_shelter.php**: Update shelter status, capacity, and supplies.
* **submit\_report.php**: Submit field reports to central system.
* **index.php**: Redirect to dashboard or login.

---

## 4. Mobile App (Android - Java + XML)

### Folder & File Structure (`app/src/main/`):

```plaintext
java/com/example/disasterapp/
├── activities/
│   ├── SplashActivity.java
│   ├── AuthActivity.java
│   ├── MainActivity.java
│   ├── ProfileActivity.java
│   ├── EmergencyContactsActivity.java
│   ├── TouristModeActivity.java
│   └── OfflineResourcesActivity.java
├── adapters/
│   ├── ShelterListAdapter.java
│   └── EmergencyContactAdapter.java
├── fragments/
│   ├── HomeFragment.java
│   ├── MapViewFragment.java
│   ├── AlertsFragment.java
│   └── SettingsFragment.java
├── models/
│   ├── User.java
│   ├── Shelter.java
│   └── DisasterAlert.java
├── network/
│   ├── ApiClient.java
│   └── ApiService.java
├── services/
│   ├── AppFirebaseMessagingService.java
│   └── LocationUpdatesService.java
├── util/
│   ├── PermissionManager.java
│   └── SharedPreferencesHelper.java
└── db/
    ├── AppDatabase.java
    ├── OfflineContentDao.java
    └── OfflineContent.java

res/
├── layout/
│   ├── activity_main.xml
│   ├── fragment_home.xml
│   ├── item_shelter_list.xml
│   └── ...
├── drawable/
│   ├── ic_shelter.xml
│   └── ...
├── values/
│   ├── strings.xml
│   ├── colors.xml
│   └── styles.xml
├── xml/
│   └── network_security_config.xml
└── raw/
    ├── first_aid_guide.html
    └── safety_protocols.html
```

---

## 5. Database Schema (MySQL)

### Database Tables Overview

| Table Name              | Description                                   |
| ----------------------- | --------------------------------------------- |
| **users**               | Public mobile app users.                      |
| **emergency\_contacts** | Emergency contacts linked to each user.       |
| **admins**              | Admin portal user credentials and details.    |
| **responders**          | Emergency responders' details.                |
| **disasters**           | Active or past disaster events.               |
| **shelters**            | Registered safe shelters/help camps.          |
| **broadcast\_messages** | Push/SMS alerts sent by admins.               |
| **field\_reports**      | Reports from emergency responders.            |
| **awareness\_content**  | Educational materials for pre-disaster phase. |
| **user\_locations**     | Location history for post-disaster analysis.  |
| **shelter\_updates**    | Shelter status and occupancy changes.         |

---

### Table Details

#### `users`

* id (PK, INT)
* full\_name (VARCHAR)
* email (VARCHAR, Unique)
* password (VARCHAR, Hashed)
* phone\_number (VARCHAR, Unique)
* last\_known\_latitude (DECIMAL)
* last\_known\_longitude (DECIMAL)
* is\_safe (BOOLEAN)
* is\_tourist (BOOLEAN)
* device\_token (VARCHAR)
* created\_at (TIMESTAMP)

#### `emergency_contacts`

* id (PK, INT)
* user\_id (FK → users)
* name (VARCHAR)
* phone\_number (VARCHAR)
* relationship (VARCHAR)

#### `admins`

* id (PK, INT)
* username (VARCHAR, Unique)
* password (VARCHAR, Hashed)
* full\_name (VARCHAR)
* role (VARCHAR)

#### `responders`

* id (PK, INT)
* username (VARCHAR, Unique)
* password (VARCHAR, Hashed)
* full\_name (VARCHAR)
* team (VARCHAR)
* assigned\_zone (GEOMETRY)

#### `disasters`

* id (PK, INT)
* name (VARCHAR)
* type (ENUM: 'Flood', 'Earthquake', 'Hurricane', 'Wildfire')
* status (ENUM: 'Prepare', 'Evacuate', 'All Clear')
* affected\_area (GEOMETRY)
* created\_by\_admin\_id (FK → admins)
* created\_at (TIMESTAMP)

#### `shelters`

* id (PK, INT)
* name (VARCHAR)
* location (POINT)
* capacity (INT)
* current\_occupancy (INT)
* available\_supplies (JSON)
* status (ENUM: 'Open', 'Full', 'Closed')
* last\_updated\_by\_responder\_id (FK → responders)
* updated\_at (TIMESTAMP)

#### `broadcast_messages`

* id (PK, INT)
* admin\_id (FK → admins)
* disaster\_id (FK → disasters, nullable)
* title (VARCHAR)
* body (TEXT)
* target\_audience (ENUM: 'All', 'Affected Zone', 'Tourists')
* sent\_at (TIMESTAMP)

#### `field_reports`

* id (PK, INT)
* responder\_id (FK → responders)
* disaster\_id (FK → disasters)
* report\_content (TEXT)
* location (POINT)
* created\_at (TIMESTAMP)

#### `awareness_content`

* id (PK, INT)
* title (VARCHAR)
* content (TEXT)
* type (ENUM: 'First Aid', 'Safety Tip', 'Evacuation Guide')
* language (VARCHAR)

#### `user_locations`

* id (PK, BIGINT)
* user\_id (FK → users)
* location (POINT)
* timestamp (TIMESTAMP)

#### `shelter_updates`

* id (PK, INT)
* shelter\_id (FK → shelters)
* responder\_id (FK → responders)
* occupancy (INT)
* status (ENUM: 'Open', 'Full', 'Closed')
* notes (TEXT)
* updated\_at (TIMESTAMP)

```

---


