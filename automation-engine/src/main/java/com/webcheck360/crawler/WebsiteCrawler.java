package com.webcheck360.crawler;

import com.webcheck360.util.HttpStatusUtil;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import com.webcheck360.checker.ImageHealthChecker;

import java.util.*;

public class WebsiteCrawler {

    private WebDriver driver;
    private String baseUrl;
    private int maxDepth = 2;
    private Set<String> visitedLinks = new HashSet<>();
    private Map<String, Integer> results = new LinkedHashMap<>();
    private Map<String, Integer> imageResults = new LinkedHashMap<>();

    public WebsiteCrawler(WebDriver driver, String baseUrl) {
        this.driver = driver;
        this.baseUrl = baseUrl;
    }

    public Map<String, Integer> startCrawling() {
        crawlPage(baseUrl, 0);
        return results;
    }


    private void crawlPage(String url, int depth) {

    	if (depth > maxDepth)
    	    return;

        if (visitedLinks.contains(url))
            return;

        visitedLinks.add(url);

        try {
            driver.get(url);
            ImageHealthChecker imageChecker =
                    new ImageHealthChecker(driver, baseUrl);

            imageResults.putAll(imageChecker.checkImages());


            List<WebElement> links =
                    driver.findElements(By.tagName("a"));

            for (WebElement element : links) {

                String href = element.getAttribute("href");

                if (href == null || href.isEmpty())
                    continue;

                // Handle relative URLs
                if (href.startsWith("/")) {
                    href = baseUrl + href;
                }

                // Only crawl internal links
                if (!href.startsWith(baseUrl))
                    continue;

                int status = HttpStatusUtil.getStatusCode(href);
                results.put(href, status);


                if (status == 200) {
                	crawlPage(href, depth + 1);

                }
            }

        } catch (Exception e) {
        	    // Ignore navigation errors to keep crawler stable      
        }
    }
    public Map<String, Integer> getImageResults() {
        return imageResults;
    }

}
