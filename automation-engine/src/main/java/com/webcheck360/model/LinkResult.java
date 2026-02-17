package com.webcheck360.model;

public class LinkResult {

    private String url;
    private int statusCode;

    public LinkResult(String url, int statusCode) {
        this.url = url;
        this.statusCode = statusCode;
    }

    public String getUrl() {
        return url;
    }

    public int getStatusCode() {
        return statusCode;
    }

    @Override
    public String toString() {
        return url + " --> " + statusCode;
    }
}
