# Disaster Management & Evacuation System
## eProject Report

---

### **Project Information**
- **Project Title:** Disaster Management & Evacuation System (DMS)
- **Academic Year:** 2024-2025
- **Semester:** 3rd Semester
- **Technology Stack:** PHP, MySQL, Android (Java), OSMDroid, Material Design 3
- **Development Period:** September 2024 - December 2024

---

## **Acknowledgements**

We would like to express our sincere gratitude to all those who contributed to the successful completion of this Disaster Management & Evacuation System project.

First and foremost, we thank our project supervisor and faculty members for their invaluable guidance, continuous support, and constructive feedback throughout the development process. Their expertise and mentorship were instrumental in shaping this project.

We extend our appreciation to the college administration for providing the necessary infrastructure, laboratory facilities, and resources that enabled us to develop and test this comprehensive disaster management solution.

Special thanks to the open-source community for providing excellent libraries and frameworks including OSMDroid for mapping functionality, Material Design 3 for modern UI components, and Retrofit for seamless API communication.

We also acknowledge the various online resources, documentation, and tutorials that helped us understand disaster management protocols and implement best practices in emergency response systems.

Finally, we thank our families and friends for their patience, encouragement, and moral support during the intensive development phases of this project.

---

## **eProject Synopsis**

### **Project Overview**
The Disaster Management & Evacuation System (DMS) is a comprehensive digital solution designed to enhance emergency preparedness, response coordination, and public safety during natural disasters and emergency situations. The system addresses critical challenges in disaster management through a multi-platform approach integrating web-based administration, mobile applications, and real-time communication systems.

### **Problem Statement**
Traditional disaster management systems often suffer from:
- Delayed communication between authorities and affected populations
- Lack of real-time location tracking and evacuation coordination
- Insufficient shelter management and resource allocation
- Poor integration between emergency responders and administrative systems
- Limited accessibility for tourists and non-local populations
- Inadequate post-disaster analysis and reporting capabilities

### **Proposed Solution**
Our DMS provides a unified platform featuring:
1. **Admin Portal**: Web-based dashboard for disaster management, shelter coordination, and emergency broadcasting
2. **Mobile Application**: Android app for citizens with real-time alerts, navigation, and safety features
3. **Responder Portal**: Specialized interface for emergency personnel field operations
4. **Real-time Communication**: Integrated messaging and notification systems
5. **Geospatial Integration**: Interactive mapping with evacuation routes and shelter locations

### **Key Features**
- **Real-time Disaster Alerts**: Push notifications and SMS broadcasting
- **Interactive Mapping**: OSMDroid integration with shelter locations and evacuation routes
- **In-app Navigation**: Direct routing to nearest shelters and safe zones
- **Multi-user Management**: Separate interfaces for admins, responders, and citizens
- **Offline Capabilities**: Essential information accessible without internet connectivity
- **Tourist Mode**: Multilingual support for non-local users
- **Report System**: User-generated incident reporting with location tracking
- **Emergency Contacts**: Integrated contact management and safety status sharing

### **Technology Stack**
- **Backend**: PHP 8.x with MySQL 8.0 database
- **Frontend Web**: HTML5, CSS3, JavaScript, Leaflet.js mapping
- **Mobile**: Android (Java), Material Design 3, OSMDroid
- **Database**: MySQL with spatial data types (POINT, GEOMETRY)
- **APIs**: RESTful services with JSON data exchange
- **Mapping**: OSMDroid for mobile, Leaflet.js for web interfaces

---

## **eProject Analysis**

### **Requirements Analysis**

#### **Functional Requirements**
1. **User Management**
   - User registration and authentication
   - Profile management and emergency contact setup
   - Role-based access control (Admin, Responder, Citizen)

2. **Disaster Management**
   - Create, update, and monitor disaster events
   - Define affected areas using polygon geometry
   - Set disaster status levels (Prepare, Evacuate, All Clear)
   - Broadcast emergency alerts to targeted populations

3. **Shelter Management**
   - Register and maintain shelter information
   - Real-time capacity and occupancy tracking
   - Supply inventory management
   - Location-based shelter discovery

4. **Communication System**
   - Push notification delivery
   - SMS broadcasting capabilities
   - Emergency contact notification
   - Safety status updates ("I'm Safe" feature)

5. **Mapping and Navigation**
   - Interactive map display with user location
   - Shelter and disaster zone visualization
   - In-app navigation to safe locations
   - Evacuation route planning

6. **Reporting and Analytics**
   - User incident reporting
   - Field reports from responders
   - Post-disaster analysis and statistics
   - Location history tracking

#### **Non-Functional Requirements**
1. **Performance**
   - Response time < 3 seconds for critical operations
   - Support for 1000+ concurrent users
   - Efficient battery usage on mobile devices

2. **Security**
   - Encrypted password storage (bcrypt hashing)
   - Secure API endpoints with authentication
   - Data privacy compliance
   - Input validation and SQL injection prevention

3. **Usability**
   - Intuitive user interface design
   - Accessibility features for diverse users
   - Multilingual support for tourist mode
   - Offline functionality for essential features

4. **Reliability**
   - 99.5% system uptime
   - Automatic failover mechanisms
   - Data backup and recovery procedures
   - Error handling and graceful degradation

5. **Scalability**
   - Horizontal scaling capability
   - Database optimization for large datasets
   - Efficient spatial data indexing
   - Load balancing support



{{ ... }}

---

## **eProject Design**

### **System Architecture**

#### **Overall System Architecture**
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Admin Portal  │    │ Responder Portal│    │  Mobile App     │
│   (Web - PHP)   │    │   (Web - PHP)   │    │  (Android)      │
└─────────┬───────┘    └─────────┬───────┘    └─────────┬───────┘
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 │
                    ┌─────────────┴─────────────┐
                    │     Backend API Server    │
                    │        (PHP/MySQL)        │
                    └─────────────┬─────────────┘
                                  │
                    ┌─────────────┴─────────────┐
                    │      MySQL Database       │
                    │   (Spatial Data Types)    │
                    └───────────────────────────┘
```

### **Data Flow Diagrams (DFDs)**

#### **Level 0 DFD - Context Diagram**
```
                    ┌─────────────────────────────────┐
                    │                                 │
     Citizens ──────┤                                 ├────── Emergency Alerts
                    │                                 │
   Emergency ───────┤    Disaster Management System  ├────── Shelter Information
   Responders       │                                 │
                    │                                 ├────── Navigation Routes
  Government ───────┤                                 │
  Administrators    │                                 ├────── Status Reports
                    └─────────────────────────────────┘
```

#### **Level 1 DFD - Main Processes**
```
Citizens ──┐
           ├──→ [1.0 User Management] ──→ User Database
Emergency ─┘
Responders

Government ────→ [2.0 Disaster Management] ──→ Disaster Database
Administrators                    │
                                 ├──→ [3.0 Alert Broadcasting]
                                 │
Citizens ────────────────────────┼──→ [4.0 Shelter Management] ──→ Shelter Database
                                 │
Emergency ───────────────────────┼──→ [5.0 Location Services] ──→ Location Database
Responders                       │
                                 └──→ [6.0 Reporting System] ──→ Reports Database
```

#### **Level 2 DFD - Disaster Management Process**
```
Government ──→ [2.1 Create Disaster Event] ──→ Disaster Database
Administrators
               [2.2 Update Disaster Status] ──→ Status Updates
                         │
                         ├──→ [2.3 Affected Area Mapping] ──→ Spatial Database
                         │
                         └──→ [2.4 Resource Allocation] ──→ Resource Database
```

### **Process Flow Charts**

#### **User Registration Process**
```
START
  │
  ▼
[User Opens App]
  │
  ▼
[Select Registration]
  │
  ▼
[Enter Personal Details]
  │
  ▼
[Validate Input] ──No──→ [Show Error Message] ──┐
  │Yes                                          │
  ▼                                             │
[Check Email/Phone Exists] ──Yes──→ [Show Duplicate Error] ─┘
  │No
  ▼
[Hash Password]
  │
  ▼
[Store in Database]
  │
  ▼
[Send Verification]
  │
  ▼
[Registration Complete]
  │
  ▼
END
```

#### **Emergency Alert Broadcasting Process**
```
START
  │
  ▼
[Admin Creates Disaster Event]
  │
  ▼
[Define Affected Area]
  │
  ▼
[Set Alert Priority Level]
  │
  ▼
[Select Target Audience] ──→ [All Users/Affected Zone/Tourists]
  │
  ▼
[Compose Alert Message]
  │
  ▼
[Review and Confirm]
  │
  ▼
[Query Target Users from Database]
  │
  ▼
[Send Push Notifications] ──→ [Log Delivery Status]
  │
  ▼
[Send SMS Backup] ──→ [Update Broadcast Log]
  │
  ▼
[Alert Sent Successfully]
  │
  ▼
END
```

#### **Shelter Navigation Process**
```
START
  │
  ▼
[User Requests Shelter Navigation]
  │
  ▼
[Get Current GPS Location] ──Failed──→ [Request Location Permission] ──┐
  │Success                                                              │
  ▼                                                                     │
[Query Nearby Shelters] ←─────────────────────────────────────────────┘
  │
  ▼
[Calculate Distances]
  │
  ▼
[Sort by Distance/Availability]
  │
  ▼
[Display Shelter List]
  │
  ▼
[User Selects Shelter] ──→ [Show Shelter Details]
  │
  ▼
[Start In-App Navigation]
  │
  ▼
[Draw Route on Map]
  │
  ▼
[Provide Turn-by-Turn Directions]
  │
  ▼
[Arrival at Destination]
  │
  ▼
END
```

### **System Process Diagrams**

#### **Real-time Alert Distribution System**
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│  Admin Portal   │    │   Alert Engine  │    │ Notification    │
│                 │    │                 │    │ Service         │
│ 1. Create Alert │───→│ 2. Process      │───→│ 3. Distribute   │
│ 2. Define Area  │    │ 3. Target Users │    │ 4. Track Status │
│ 3. Set Priority │    │ 4. Queue Jobs   │    │ 5. Log Results  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   User Database │    │ Spatial Query   │    │ Push/SMS Gateway│
│                 │    │ Engine          │    │                 │
│ • User Profiles │    │ • Geofencing    │    │ • FCM Service   │
│ • Device Tokens │    │ • Location Math │    │ • SMS Provider  │
│ • Preferences   │    │ • Area Matching │    │ • Delivery Log  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

#### **Disaster Event Management Workflow**
```
┌─────────────────┐
│ Disaster Event  │
│ Detection       │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│ Admin Portal    │
│ Event Creation  │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Affected Area   │    │ Shelter         │    │ Responder       │
│ Definition      │───→│ Activation      │───→│ Assignment      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
          │                       │                       │
          ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Alert           │    │ Resource        │    │ Field           │
│ Broadcasting    │    │ Allocation      │    │ Operations      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

---

## **Database Design / Structure**

### **Entity Relationship Diagram (ERD)**
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     USERS       │    │ EMERGENCY_      │    │   DISASTERS     │
│                 │    │ CONTACTS        │    │                 │
│ • id (PK)       │    │                 │    │ • id (PK)       │
│ • full_name     │───┐│ • id (PK)       │    │ • name          │
│ • email         │   ││ • user_id (FK)  │    │ • type          │
│ • password      │   ││ • contact_id(FK)│    │ • status        │
│ • phone_number  │   ││ • relationship  │    │ • affected_area │
│ • location      │   │└─────────────────┘    │ • created_by    │
│ • is_safe       │   │                       │ • created_at    │
│ • device_token  │   │                       └─────────────────┘
└─────────┬───────┘   │
          │           │
          └───────────┘
          │
          ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   SHELTERS      │    │ BROADCAST_      │    │ USER_REPORTS    │
│                 │    │ MESSAGES        │    │                 │
│ • id (PK)       │    │                 │    │ • id (PK)       │
│ • name          │    │ • id (PK)       │    │ • user_id (FK)  │
│ • location      │    │ • admin_id (FK) │    │ • title         │
│ • capacity      │    │ • disaster_id   │    │ • description   │
│ • occupancy     │    │ • title         │    │ • category      │
│ • supplies      │    │ • body          │    │ • priority      │
│ • status        │    │ • target_aud    │    │ • location      │
└─────────────────┘    │ • sent_at       │    │ • status        │
                       └─────────────────┘    └─────────────────┘
```

### **Database Schema Details**

#### **Core Tables Structure**

1. **users** - Public mobile app users
   ```sql
   CREATE TABLE users (
     id INT AUTO_INCREMENT PRIMARY KEY,
     full_name VARCHAR(200) NOT NULL,
     email VARCHAR(150) NOT NULL UNIQUE,
     password VARCHAR(255) NOT NULL,
     phone_number VARCHAR(20) NOT NULL UNIQUE,
     last_known_latitude DECIMAL(10, 8),
     last_known_longitude DECIMAL(11, 8),
     is_safe BOOLEAN DEFAULT TRUE,
     is_tourist BOOLEAN DEFAULT FALSE,
     device_token VARCHAR(255) NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

2. **disasters** - Disaster event management
   ```sql
   CREATE TABLE disasters (
     id INT AUTO_INCREMENT PRIMARY KEY,
     name VARCHAR(255) NOT NULL,
     type ENUM('Flood', 'Earthquake', 'Hurricane', 'Wildfire', 
               'Tsunami', 'Tornado', 'Other') NOT NULL,
     status ENUM('Prepare', 'Evacuate', 'All Clear', 'Inactive') 
            DEFAULT 'Inactive',
     affected_area_geometry GEOMETRY NOT NULL,
     created_by_admin_id INT NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

3. **shelters** - Emergency shelter information
   ```sql
   CREATE TABLE shelters (
     id INT AUTO_INCREMENT PRIMARY KEY,
     name VARCHAR(255) NOT NULL,
     location POINT NOT NULL,
     capacity INT NOT NULL,
     current_occupancy INT DEFAULT 0,
     available_supplies JSON,
     status ENUM('Open', 'Full', 'Closed') DEFAULT 'Closed',
     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

### **Spatial Data Implementation**

#### **Geographic Data Types**
- **POINT**: Used for shelter locations, user positions
- **GEOMETRY**: Used for disaster affected areas (polygons)
- **Spatial Indexing**: Optimized queries for location-based searches

#### **Spatial Query Examples**
```sql
-- Find shelters within 10km radius
SELECT *, ST_Distance_Sphere(location, POINT(lng, lat)) as distance 
FROM shelters 
WHERE ST_Distance_Sphere(location, POINT(lng, lat)) <= 10000
ORDER BY distance;

-- Check if user is in disaster affected area
SELECT COUNT(*) as in_danger
FROM disasters 
WHERE ST_Contains(affected_area_geometry, POINT(user_lng, user_lat))
AND status IN ('Prepare', 'Evacuate');
```

### **Database Normalization**
- **First Normal Form (1NF)**: All tables have atomic values
- **Second Normal Form (2NF)**: No partial dependencies on composite keys
- **Third Normal Form (3NF)**: No transitive dependencies
- **Spatial Optimization**: Specialized indexing for geographic queries


### **System Analysis**

#### **Stakeholder Analysis**
1. **Primary Stakeholders**
   - **Citizens**: General public requiring emergency information and evacuation assistance
   - **Emergency Responders**: First responders, rescue teams, medical personnel
   - **Government Administrators**: Disaster management officials, emergency coordinators

2. **Secondary Stakeholders**
   - **Tourists**: Visitors unfamiliar with local emergency procedures
   - **Shelter Operators**: Personnel managing evacuation centers
   - **IT Support Teams**: System maintenance and technical support staff

#### **Use Case Analysis**
1. **Citizen Use Cases**
   - Register and create profile
   - Receive disaster alerts
   - Find nearest shelters
   - Navigate to safe locations
   - Report incidents or hazards
   - Update safety status
   - Access offline emergency guides

2. **Admin Use Cases**
   - Monitor disaster situations
   - Create and manage disaster events
   - Broadcast emergency messages
   - Manage shelter network
   - Assign responder duties
   - Generate reports and analytics

3. **Responder Use Cases**
   - Access assigned disaster zones
   - Update shelter status and capacity
   - Submit field reports
   - Coordinate with command center
   - Track resource allocation

### **Risk Analysis**

#### **Technical Risks**
1. **Network Connectivity**: Mobile network congestion during disasters
   - **Mitigation**: Offline mode capabilities, cached data storage

2. **Server Overload**: High traffic during emergency situations
   - **Mitigation**: Load balancing, cloud scaling, CDN implementation

3. **Data Accuracy**: Incorrect location or shelter information
   - **Mitigation**: Regular data validation, crowdsourced verification

#### **Operational Risks**
1. **User Adoption**: Low adoption rates among target population
   - **Mitigation**: Public awareness campaigns, training programs

2. **System Integration**: Compatibility with existing emergency systems
   - **Mitigation**: Standard API protocols, flexible data formats

3. **Maintenance**: Ongoing system updates and bug fixes
   - **Mitigation**: Automated testing, continuous integration practices





{{ ... }}

---

## **Developer's Guide**

### **Development Environment Setup**

#### **Prerequisites**
- **PHP 8.0+** with extensions: mysqli, json, curl
- **MySQL 8.0+** with spatial extensions enabled
- **Apache/Nginx** web server
- **Android Studio** (for mobile development)
- **Git** for version control
- **Composer** for PHP dependency management

#### **Project Structure**
```
DMS-sem3project/
├── backend/
│   ├── api/                 # RESTful API endpoints
│   ├── config/              # Database configuration
│   └── schema.sql           # Database structure
├── admin_portal/            # Web-based admin interface
├── responder_portal/        # Emergency responder interface
├── mobile_app/              # Android application
└── documentation/           # Project documentation
```

#### **Installation Steps**
1. Clone repository: `git clone [repository-url]`
2. Configure database connection in `backend/config/db_connect.php`
3. Import database schema: `mysql < backend/schema.sql`
4. Configure web server to serve admin/responder portals
5. Open mobile project in Android Studio
6. Update API base URLs in mobile app configuration

### **Module Descriptions**

#### **Backend API Modules**

##### **1. User Management Module**
**Location:** `backend/api/users/`

**Purpose:** Handles user authentication, registration, and profile management

**Key Files:**
- `register.php` - User registration endpoint
- `login.php` - Authentication endpoint  
- `profile_update.php` - Profile modification
- `get_profile.php` - User data retrieval

**Core Functions:**
```php
// User registration with validation
function registerUser($userData) {
    // Validate input data
    // Check for existing email/phone
    // Hash password using bcrypt
    // Insert into database
    // Return success/error response
}

// User authentication
function authenticateUser($email, $password) {
    // Retrieve user from database
    // Verify password hash
    // Generate session/token
    // Return authentication result
}
```

**Database Integration:**
- Primary table: `users`
- Relationships: `emergency_contacts`, `user_locations`
- Spatial queries for location-based features

##### **2. Disaster Management Module**
**Location:** `backend/api/disasters/`

**Purpose:** Manages disaster events, affected areas, and status updates

**Key Files:**
- `create.php` - Create new disaster events
- `get_all.php` - Retrieve disaster list
- `update.php` - Modify disaster information
- `get_map_data.php` - Spatial data for mobile app

**Core Functions:**
```php
// Create disaster with spatial data
function createDisaster($disasterData) {
    // Validate disaster information
    // Process affected area geometry (WKT/GeoJSON)
    // Store using MySQL spatial functions
    // Trigger alert broadcasting
    // Return disaster ID and status
}

// Get disasters within area
function getDisastersInArea($lat, $lng, $radius) {
    // Use ST_Distance_Sphere for proximity queries
    // Filter by status and date
    // Return formatted disaster data
    // Include GeoJSON for mobile consumption
}
```

**Spatial Data Handling:**
```sql
-- Store disaster affected area
INSERT INTO disasters (name, type, affected_area_geometry) 
VALUES (?, ?, ST_GeomFromText(?));

-- Query disasters affecting location
SELECT * FROM disasters 
WHERE ST_Contains(affected_area_geometry, POINT(?, ?))
AND status IN ('Prepare', 'Evacuate');
```

##### **3. Shelter Management Module**
**Location:** `backend/api/shelters/`

**Purpose:** Manages shelter information, capacity, and location services

**Key Files:**
- `create.php` - Add new shelters
- `get_all.php` - Retrieve shelter list with spatial data
- `update.php` - Modify shelter information
- `get_nearby.php` - Find shelters by proximity

**Core Functions:**
```php
// Find nearest shelters
function getNearestShelters($userLat, $userLng, $limit = 10) {
    // Calculate distances using ST_Distance_Sphere
    // Filter by availability and status
    // Sort by distance and capacity
    // Return shelter data with navigation info
}

// Update shelter status
function updateShelterStatus($shelterId, $occupancy, $supplies) {
    // Validate shelter exists
    // Update occupancy and supply data
    // Log status change
    // Notify relevant responders
}
```

##### **4. Alert Broadcasting Module**
**Location:** `backend/api/broadcast/`

**Purpose:** Handles emergency alert distribution and notification management

**Key Files:**
- `send_alert.php` - Broadcast emergency messages
- `get_delivery_status.php` - Track message delivery
- `target_users.php` - Identify alert recipients

**Core Functions:**
```php
// Broadcast alert to targeted users
function broadcastAlert($alertData) {
    // Determine target audience (spatial/demographic)
    // Queue push notifications
    // Send SMS backup messages
    // Log delivery attempts
    // Return broadcast statistics
}

// Target users in affected area
function getAffectedUsers($disasterId) {
    // Get disaster geometry
    // Query users within affected area
    // Filter by notification preferences
    // Return user device tokens
}
```

#### **Mobile Application Modules**

##### **1. Network Module**
**Location:** `mobile_app/app/src/main/java/com/example/mobile_app/network/`

**Purpose:** Handles API communication and data synchronization

**Key Classes:**
- `ApiService.java` - Retrofit interface definitions
- `ApiClient.java` - HTTP client configuration
- `NetworkUtils.java` - Connection utilities

**Implementation Details:**
```java
// API service interface
public interface ApiService {
    @POST("users/login")
    Call<LoginResponse> loginUser(@Body LoginRequest request);
    
    @GET("disasters/get_map_data")
    Call<MapDataResponse> getMapData(
        @Query("lat") double latitude,
        @Query("lng") double longitude,
        @Query("radius") int radius
    );
    
    @POST("reports/create")
    Call<ReportResponse> submitReport(@Body UserReport report);
}

// HTTP client configuration
public class ApiClient {
    private static final String BASE_URL = "http://192.168.0.101:8000/backend/api/";
    
    public static ApiService getApiService() {
        Retrofit retrofit = new Retrofit.Builder()
            .baseUrl(BASE_URL)
            .addConverterFactory(GsonConverterFactory.create())
            .build();
        return retrofit.create(ApiService.class);
    }
}
```

##### **2. Mapping Module**
**Location:** `mobile_app/app/src/main/java/com/example/mobile_app/fragments/MapViewFragment.java`

**Purpose:** Interactive map display with shelter/disaster visualization

**Key Features:**
- OSMDroid integration for offline-capable mapping
- Real-time location tracking
- Shelter and disaster marker management
- In-app navigation with route drawing

**Implementation Details:**
```java
// Initialize map with user location
private void initializeMap() {
    mapView.setTileSource(TileSourceFactory.MAPNIK);
    mapView.setMultiTouchControls(true);
    
    // Add user location overlay
    MyLocationNewOverlay locationOverlay = new MyLocationNewOverlay(
        new GpsMyLocationProvider(requireContext()), mapView);
    locationOverlay.enableMyLocation();
    mapView.getOverlays().add(locationOverlay);
}

// Add shelter markers to map
private void addShelterMarkers(List<Shelter> shelters) {
    ItemizedIconOverlay<OverlayItem> shelterOverlay = 
        new ItemizedIconOverlay<>(shelterItems, shelterGestureListener, 
        requireContext());
    
    for (Shelter shelter : shelters) {
        OverlayItem item = new OverlayItem(
            shelter.getName(),
            shelter.getDescription(),
            new GeoPoint(shelter.getLatitude(), shelter.getLongitude())
        );
        shelterItems.add(item);
    }
    mapView.getOverlays().add(shelterOverlay);
}
```

##### **3. Data Models Module**
**Location:** `mobile_app/app/src/main/java/com/example/mobile_app/models/`

**Purpose:** Data structures for API responses and local storage

**Key Classes:**
- `User.java` - User profile data
- `Shelter.java` - Shelter information
- `DisasterAlert.java` - Disaster event data
- `UserReport.java` - Incident report structure

**Implementation Example:**
```java
// Disaster alert model with coordinate parsing
public class DisasterAlert {
    private String name;
    private String type;
    private String status;
    private String affectedArea; // GeoJSON or WKT format
    
    // Parse coordinates from polygon geometry
    public double getLatitude() {
        if (affectedArea == null) return 0.0;
        
        try {
            if (affectedArea.startsWith("{")) {
                // Parse GeoJSON format
                JSONObject geoJson = new JSONObject(affectedArea);
                JSONArray coordinates = geoJson.getJSONArray("coordinates");
                JSONArray firstPoint = coordinates.getJSONArray(0).getJSONArray(0);
                return firstPoint.getDouble(1); // Latitude
            } else if (affectedArea.startsWith("POLYGON")) {
                // Parse WKT format
                return parseWKTLatitude(affectedArea);
            }
        } catch (Exception e) {
            Log.e("DisasterAlert", "Error parsing coordinates", e);
        }
        return 0.0;
    }
}
```

#### **Admin Portal Modules**

##### **1. Dashboard Module**
**Location:** `admin_portal/dashboard.php`

**Purpose:** Administrative overview and system monitoring

**Key Features:**
- Real-time statistics display
- Recent activity monitoring
- Quick action shortcuts
- Performance metrics

**Implementation:**
```php
// Get system statistics
function getSystemStats() {
    $stats = [];
    
    // Count active users
    $stats['active_users'] = getActiveUserCount();
    
    // Count active disasters
    $stats['active_disasters'] = getActiveDisasterCount();
    
    // Count available shelters
    $stats['available_shelters'] = getAvailableShelterCount();
    
    // Recent activity
    $stats['recent_activity'] = getRecentActivity(10);
    
    return $stats;
}
```

##### **2. Spatial Data Management**
**Location:** `admin_portal/assets/js/map-utils.js`

**Purpose:** Interactive map functionality for admin operations

**Key Features:**
- Leaflet.js integration
- Polygon drawing for disaster areas
- Shelter location placement
- Coordinate validation

**Implementation:**
```javascript
// Initialize admin map with drawing tools
function initAdminMap(mapId) {
    const map = L.map(mapId).setView([0, 0], 2);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    // Add drawing controls
    const drawControl = new L.Control.Draw({
        draw: {
            polygon: true,
            marker: true,
            circle: false,
            rectangle: false
        }
    });
    map.addControl(drawControl);
    
    // Handle drawing events
    map.on('draw:created', function(event) {
        const layer = event.layer;
        const geometry = layer.toGeoJSON();
        handleGeometryCreated(geometry);
    });
}
```

### **API Documentation**

#### **Authentication Endpoints**
```
POST /api/users/register
POST /api/users/login
POST /api/users/logout
GET  /api/users/profile
PUT  /api/users/profile
```

#### **Disaster Management Endpoints**
```
GET    /api/disasters/get_all
POST   /api/admin/disasters_create
PUT    /api/admin/disasters_update
DELETE /api/admin/disasters_delete
GET    /api/users/get_map_data
```

#### **Shelter Management Endpoints**
```
GET    /api/shelters/get_all
POST   /api/admin/shelters_create
PUT    /api/admin/shelters_update
DELETE /api/admin/shelters_delete
GET    /api/shelters/get_nearby
```

### **Testing and Deployment**

#### **Testing Strategy**
- **Unit Tests**: Individual function validation
- **Integration Tests**: API endpoint testing
- **Spatial Tests**: Geographic query validation
- **UI Tests**: Mobile app interface testing
- **Load Tests**: System performance under stress

#### **Deployment Considerations**
- **Database Optimization**: Spatial indexing for performance
- **API Security**: Input validation and authentication
- **Mobile Distribution**: APK signing and distribution
- **Monitoring**: Error logging and performance tracking
- **Backup Strategy**: Regular database backups with spatial data

---

## **Conclusion**

The Disaster Management & Evacuation System represents a comprehensive solution for emergency preparedness and response coordination. Through the integration of modern web technologies, mobile applications, and spatial data management, the system provides real-time communication, location-based services, and efficient resource coordination during disaster situations.

### **Key Achievements**
- **Multi-platform Integration**: Seamless coordination between web portals and mobile applications
- **Real-time Communication**: Instant alert broadcasting and status updates
- **Spatial Data Management**: Advanced geographic information system capabilities
- **User-Centric Design**: Intuitive interfaces for all user types
- **Scalable Architecture**: Designed for growth and high-availability requirements

### **Future Enhancements**
- **AI-Powered Predictions**: Machine learning for disaster forecasting
- **IoT Integration**: Sensor data integration for real-time monitoring
- **Advanced Analytics**: Predictive modeling and risk assessment
- **Multi-language Support**: Expanded internationalization
- **Blockchain Integration**: Secure and transparent resource tracking

### **Impact and Benefits**
The DMS system significantly improves emergency response capabilities by providing:
- Faster alert distribution and response times
- Better coordination between emergency services
- Enhanced public safety through real-time information
- Improved resource allocation and management
- Comprehensive post-disaster analysis and reporting

This project demonstrates the potential of technology to save lives and reduce the impact of natural disasters through effective communication, coordination, and resource management.





{{ ... }}

---

## **Screenshots**

### **Admin Portal Screenshots**

#### **1. Admin Dashboard**
![Admin Dashboard](screenshots/admin_dashboard.png)
*Features: Statistics overview, recent activities, quick action buttons, disaster monitoring panel*

#### **2. Disaster Management**
![Disaster Management](screenshots/disaster_management.png)
*Features: Interactive map for affected area selection, disaster type selection, status management*

#### **3. Shelter Management**
![Shelter Management](screenshots/shelter_management.png)
*Features: Shelter location mapping, capacity management, supply tracking, status updates*

#### **4. Broadcast Messages**
![Broadcast System](screenshots/broadcast_messages.png)
*Features: Alert composition, target audience selection, message scheduling, delivery tracking*

### **Mobile Application Screenshots**

#### **5. Home Screen**
![Mobile Home](screenshots/mobile_home.png)
*Features: Weather widget, safety status button, recent alerts, quick access menu*

#### **6. Interactive Map**
![Mobile Map](screenshots/mobile_map.png)
*Features: User location, shelter markers, disaster zones, in-app navigation*

#### **7. Disaster Alerts**
![Disaster Alerts](screenshots/mobile_alerts.png)
*Features: Real-time notifications, alert history, severity indicators, action buttons*

#### **8. Shelter Navigation**
![Navigation](screenshots/mobile_navigation.png)
*Features: Route display, distance calculation, turn-by-turn directions, navigation controls*

#### **9. Emergency Contacts**
![Emergency Contacts](screenshots/emergency_contacts.png)
*Features: Contact management, relationship status, quick call/message options*

#### **10. User Reports**
![User Reports](screenshots/user_reports.png)
*Features: Incident reporting form, location capture, category selection, photo upload*

---

## **User Guide**

### **Getting Started**

#### **System Requirements**
- **Mobile App**: Android 7.0 (API level 24) or higher
- **Web Portal**: Modern web browser (Chrome 90+, Firefox 88+, Safari 14+)
- **Internet Connection**: Required for real-time features
- **GPS**: Required for location-based services

#### **Installation Process**

**Mobile Application:**
1. Download the DMS APK file from the official source
2. Enable "Install from Unknown Sources" in Android settings
3. Install the application
4. Grant required permissions (Location, Notifications, Storage)
5. Complete user registration

**Web Portal Access:**
1. Open web browser
2. Navigate to the admin/responder portal URL
3. Enter provided credentials
4. Complete initial setup if required

### **Mobile Application User Manual**

#### **1. User Registration and Login**

**Registration Process:**
1. Open the DMS mobile app
2. Tap "Create New Account"
3. Fill in required information:
   - Full Name
   - Email Address
   - Phone Number
   - Password (minimum 8 characters)
4. Verify email address through sent link
5. Complete profile setup
6. Add emergency contacts

**Login Process:**
1. Open the app
2. Enter email and password
3. Tap "Login"
4. Grant location permissions when prompted

#### **2. Home Screen Navigation**

**Main Features:**
- **Weather Widget**: Current weather conditions and alerts
- **Safety Status Button**: "I'm Safe" emergency indicator
- **Recent Alerts**: Latest disaster notifications
- **Quick Actions**: Emergency contacts, shelter finder, report incident

**Bottom Navigation:**
- **Home**: Dashboard and overview
- **Map**: Interactive mapping features
- **Alerts**: Notification history
- **Reports**: Submit and view reports
- **Profile**: Account settings and preferences

#### **3. Emergency Alert System**

**Receiving Alerts:**
- Alerts appear as push notifications
- Tap notification to view full details
- Alerts are categorized by severity (High, Medium, Low)
- Audio alerts for critical emergencies

**Alert Information Includes:**
- Disaster type and location
- Affected area boundaries
- Recommended actions
- Evacuation instructions
- Shelter information

#### **4. Interactive Map Features**

**Map Navigation:**
- Pinch to zoom in/out
- Drag to pan around
- Tap "My Location" to center on current position
- Use search to find specific locations

**Map Elements:**
- **Blue Dot**: Your current location
- **Red Markers**: Shelter locations
- **Orange Overlays**: Disaster affected areas
- **Green Routes**: Navigation paths

**Shelter Information:**
- Tap shelter marker to view details
- Information includes: name, capacity, available supplies, status
- Tap "Get Directions" for navigation

#### **5. In-App Navigation**

**Starting Navigation:**
1. Select a shelter from the map or list
2. Tap "Get Directions"
3. Review route information
4. Tap "Start Navigation"

**Navigation Controls:**
- **Recenter Map**: Return to route overview
- **Stop Navigation**: End navigation session
- **External Maps**: Open in Google Maps
- Real-time distance and ETA updates

#### **6. Emergency Contacts Management**

**Adding Contacts:**
1. Go to Profile → Emergency Contacts
2. Tap "Add Contact"
3. Enter contact information
4. Select relationship type
5. Send invitation (optional)

**Contact Features:**
- Quick call/SMS buttons
- Safety status sharing
- Automatic emergency notifications
- Contact verification system

#### **7. Incident Reporting**

**Submitting Reports:**
1. Navigate to Reports tab
2. Tap "Submit New Report"
3. Fill in report details:
   - Title and description
   - Category selection
   - Priority level
   - Location (auto-captured)
4. Attach photos (optional)
5. Submit report

**Report Tracking:**
- View submission status
- Receive updates from administrators
- Track resolution progress

#### **8. Profile and Settings**

**Profile Management:**
- Update personal information
- Change password
- Manage notification preferences
- Set language preferences
- Privacy settings

**Notification Settings:**
- Enable/disable push notifications
- Set quiet hours
- Choose alert types
- SMS backup options

### **Admin Portal User Manual**

#### **1. Admin Dashboard**

**Overview Features:**
- System statistics (users, shelters, active disasters)
- Recent activity feed
- Quick action buttons
- Performance metrics

**Navigation Menu:**
- Dashboard: Main overview
- Disasters: Event management
- Shelters: Facility management
- Users: User administration
- Responders: Personnel management
- Broadcast: Message system
- Reports: Analytics and reports

#### **2. Disaster Management**

**Creating Disaster Events:**
1. Navigate to Disasters page
2. Click "Create New Disaster"
3. Enter disaster details:
   - Name and type
   - Status level
   - Description
4. Define affected area on map:
   - Click to add polygon points
   - Double-click to complete area
5. Save disaster event

**Managing Disasters:**
- Edit existing disasters
- Update status levels
- Modify affected areas
- View impact statistics
- Archive completed events

#### **3. Shelter Management**

**Adding Shelters:**
1. Go to Shelters page
2. Click "Add New Shelter"
3. Enter shelter information:
   - Name and address
   - Capacity details
   - Available supplies
   - Contact information
4. Set location on map
5. Save shelter data

**Shelter Operations:**
- Update capacity and occupancy
- Manage supply inventory
- Change operational status
- View utilization reports
- Assign responder personnel

#### **4. Broadcasting System**

**Sending Alerts:**
1. Navigate to Broadcast page
2. Click "Create New Message"
3. Compose message:
   - Title and content
   - Select target audience
   - Set priority level
   - Choose delivery methods
4. Review and send

**Message Management:**
- View delivery statistics
- Track message status
- Resend failed messages
- Schedule future broadcasts

### **Responder Portal User Manual**

#### **1. Field Operations Dashboard**

**Assignment Overview:**
- View assigned disasters and shelters
- Check current status and priorities
- Access contact information
- Review operational guidelines

#### **2. Shelter Status Updates**

**Updating Shelter Information:**
1. Select assigned shelter
2. Update current status:
   - Occupancy numbers
   - Supply levels
   - Operational status
   - Special notes
3. Submit update

#### **3. Field Reporting**

**Submitting Field Reports:**
1. Navigate to Reports section
2. Create new field report
3. Include:
   - Situation assessment
   - Resource needs
   - Recommendations
   - Location data
4. Submit to command center