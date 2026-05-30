package com.webcheck360.checker;

import com.webcheck360.model.ImageResult;
import com.webcheck360.util.HttpStatusUtil;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import java.util.*;

public class ImageHealthChecker {

    private WebDriver driver;
    private String baseUrl;

    public ImageHealthChecker(WebDriver driver, String baseUrl) {
        this.driver = driver;
        this.baseUrl = baseUrl;
    }

    public Map<String, Integer> checkImages() {

        Map<String, Integer> imageResults = new LinkedHashMap<>();

        List<WebElement> images =
                driver.findElements(By.tagName("img"));

        for (WebElement img : images) {

            String src = img.getAttribute("src");

            // Missing src attribute
            if (src == null || src.isEmpty()) {
                imageResults.put("MISSING_SRC", 0);
                continue;
            }

            // Handle relative image URLs
            if (src.startsWith("/")) {
                src = baseUrl + src;
            }

            int status = HttpStatusUtil.getStatusCode(src);
            imageResults.put(src, status);
        }

        return imageResults;
    }
}
