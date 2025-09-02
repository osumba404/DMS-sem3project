package com.example.mobile_app.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class UserReportResponse {
    @SerializedName("success")
    private boolean success;

    @SerializedName("message")
    private String message;

    @SerializedName("reports")
    private List<UserReport> reports;

    @SerializedName("report")
    private UserReport report;

    @SerializedName("report_id")
    private Integer reportId;

    // Constructors
    public UserReportResponse() {}

    public UserReportResponse(boolean success, String message) {
        this.success = success;
        this.message = message;
    }

    // Getters and Setters
    public boolean isSuccess() { return success; }
    public void setSuccess(boolean success) { this.success = success; }

    public String getMessage() { return message; }
    public void setMessage(String message) { this.message = message; }

    public List<UserReport> getReports() { return reports; }
    public void setReports(List<UserReport> reports) { this.reports = reports; }

    public UserReport getReport() { return report; }
    public void setReport(UserReport report) { this.report = report; }

    public Integer getReportId() { return reportId; }
    public void setReportId(Integer reportId) { this.reportId = reportId; }
}
