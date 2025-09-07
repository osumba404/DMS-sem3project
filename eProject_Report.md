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
