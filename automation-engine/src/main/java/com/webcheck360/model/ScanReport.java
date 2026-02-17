package com.webcheck360.model;

import java.util.Map;

public class ScanReport {

    private String website;
    private int totalLinks;
    private int brokenLinks;
    private int totalImages;
    private int brokenImages;

    private Map<String, Integer> links;
    private Map<String, Integer> images;

    public ScanReport(String website,
                      Map<String, Integer> links,
                      Map<String, Integer> images) {

        this.website = website;
        this.links = links;
        this.images = images;

        this.totalLinks = links.size();
        this.totalImages = images.size();

        this.brokenLinks =
                (int) links.values().stream()
                        .filter(code -> code != 200)
                        .count();

        this.brokenImages =
                (int) images.values().stream()
                        .filter(code -> code != 200)
                        .count();
    }
}
