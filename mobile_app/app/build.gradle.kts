import java.util.Properties
import java.io.FileInputStream

// This is the Kotlin Script way to read the properties file.
val secretsFile = rootProject.file("secrets.properties")
val secrets = Properties()
if (secretsFile.exists()) {
    // A safe way to read the file that ensures it gets closed.
    FileInputStream(secretsFile).use { fis -> secrets.load(fis) }
}

plugins {
    id("com.android.application")
    id("org.jetbrains.kotlin.android")
}

android {
    namespace = "com.example.mobile_app"
    compileSdk = 35

    defaultConfig {
        applicationId = "com.example.mobile_app"
        minSdk = 23
        targetSdk = 35
        versionCode = 1
        versionName = "1.0"

        testInstrumentationRunner = "androidx.test.runner.AndroidJUnitRunner"
        vectorDrawables.useSupportLibrary = true

        // This is the Kotlin Script syntax for setting a manifest placeholder.
        // It's a map-like assignment.
        manifestPlaceholders["MAPS_API_KEY"] = secrets.getProperty("MAPS_API_KEY", "")
    }

    buildFeatures {
        viewBinding = true
        buildConfig = true
    }

    buildTypes {
        release {
            isMinifyEnabled = false
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
        }
    }

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_11
        targetCompatibility = JavaVersion.VERSION_11
    }

//    kotlinOptions {
//        jvmTarget = JavaVersion.VERSION_11.toString()
//    }

    dependencies {
        // Core AndroidX
        implementation(libs.appcompat)
        implementation(libs.activity)
        implementation(libs.constraintlayout)
        implementation(libs.play.services.location)
        
        // Material Components (version 1.11.0)
        implementation("com.google.android.material:material:1.11.0")
        
        // Testing
        testImplementation(libs.junit)
        androidTestImplementation(libs.ext.junit)
        androidTestImplementation(libs.espresso.core)
        
        // Network
        implementation(libs.retrofit)
        implementation(libs.retrofit.converter.gson)
        
        // Google Maps
        implementation(libs.play.services.maps)

        // Material Icons
        implementation("androidx.compose.material:material-icons-core:1.5.4")
        implementation("androidx.compose.material:material-icons-extended:1.5.4")

        // Leaflet for maps
        implementation("org.osmdroid:osmdroid-android:6.1.16")
        implementation("org.osmdroid:osmdroid-mapsforge:6.1.16") {
            exclude(group = "com.j256.ormlite")
        }
        implementation("org.osmdroid:osmdroid-geopackage:6.1.16") {
            exclude(group = "com.j256.ormlite")
        }
        implementation("org.osmdroid:osmdroid-wms:6.1.16")
        implementation("org.osmdroid:osmdroid-shape:6.1.16")

        // For marker clustering
        implementation("com.github.MKergall:osmbonuspack:6.9.0") {
            exclude(group = "com.j256.ormlite")
        }
        
        // Add ORMLite explicitly
        implementation("com.j256.ormlite:ormlite-android:6.1")

        // ViewModel and LiveData
        implementation("androidx.lifecycle:lifecycle-viewmodel-ktx:2.6.2")
        implementation("androidx.lifecycle:lifecycle-livedata-ktx:2.6.2")

        // Navigation
        implementation("androidx.navigation:navigation-fragment-ktx:2.7.5")
        implementation("androidx.navigation:navigation-ui-ktx:2.7.5")
    }
}