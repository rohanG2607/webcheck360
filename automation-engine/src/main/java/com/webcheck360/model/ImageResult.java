package com.webcheck360.model;

public class ImageResult {

    private String imageUrl;
    private int statusCode;

    public ImageResult(String imageUrl, int statusCode) {
        this.imageUrl = imageUrl;
        this.statusCode = statusCode;
    }

    public String getImageUrl() {
        return imageUrl;
    }

    public int getStatusCode() {
        return statusCode;
    }

    @Override
    public String toString() {
        return imageUrl + " --> " + statusCode;
    }
}
 