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
    alias(libs.plugins.android.application)
}

android {
    namespace = "com.example.mobile_app"
    compileSdk = 35

    defaultConfig {
        applicationId = "com.example.mobile_app"
        minSdk = 28
        targetSdk = 35
        versionCode = 1
        versionName = "1.0"

        testInstrumentationRunner = "androidx.test.runner.AndroidJUnitRunner"

        // This is the Kotlin Script syntax for setting a manifest placeholder.
        // It's a map-like assignment.
        manifestPlaceholders["MAPS_API_KEY"] = secrets.getProperty("MAPS_API_KEY", "")
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
}

dependencies {
    implementation(libs.appcompat)
    implementation(libs.material)
    implementation(libs.activity)
    implementation(libs.constraintlayout)
    implementation(libs.play.services.location)
    testImplementation(libs.junit)
    androidTestImplementation(libs.ext.junit)
    androidTestImplementation(libs.espresso.core)
    implementation(libs.retrofit)
    implementation(libs.retrofit.converter.gson)
    implementation(libs.play.services.maps)
}