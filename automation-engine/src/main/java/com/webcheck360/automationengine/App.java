package com.webcheck360.automationengine;

import java.io.File;
import java.io.FileWriter;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.*;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;

import io.github.bonigarcia.wdm.WebDriverManager;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.google.gson.JsonArray;
import com.google.gson.JsonObject;

public class App {

    // ---------- GLOBAL DATA ----------
    static Map<String, Integer> linkStatusMap = new LinkedHashMap<>();
    static Set<String> visitedPages = new LinkedHashSet<>();
    static Queue<String> pagesToVisit = new LinkedList<>();

    static int totalLinks = 0;
    static int brokenLinks = 0;
    static int suspectLinks = 0;
    static int checkedLinks = 0;

    static final int MAX_PAGES = 5;

    // ---------- FILE PATHS ----------
    static final String BASE = "D:/XAMPP/htdocs/webcheck360/";
    static final String PROGRESS = BASE + "progress.json";
    static final String REPORT = BASE + "report.json";
    static final String LINKS = BASE + "links.json";

    static final String STOP = BASE + "stop.flag";
    static final String PAUSE = BASE + "pause.flag";
    static final String LOCK = BASE + "scan.lock";

    // ---------- DOMAIN EXTRACTOR ----------
    static String getDomain(String url) {
        try {
            URL u = new URL(url);
            return u.getHost().replace("www.", "");
        } catch (Exception e) {
            return "";
        }
    }

    public static void main(String[] args) {

        if (args.length == 0) {
            System.out.println("Provide URL");
            return;
        }

        String startUrl = args[0];
        String baseDomain = getDomain(startUrl);
        boolean demoMode = args.length > 1 && args[1].equalsIgnoreCase("demo");

        System.out.println("Scanning: " + startUrl);

        // ---------- CREATE LOCK ----------
        try {
            new File(BASE).mkdirs();
            new File(LOCK).createNewFile();
        } catch (Exception e) {}

        // ---------- CLEAN OLD FILES ----------
        try {
            new File(PROGRESS).delete();
            new File(REPORT).delete();
            new File(LINKS).delete();
            new File(STOP).delete();
        } catch (Exception e) {}

        WebDriverManager.chromedriver().setup();
        WebDriver driver = new ChromeDriver();

        try {
            pagesToVisit.add(startUrl);

            while (!pagesToVisit.isEmpty() && visitedPages.size() < MAX_PAGES) {

                if (isStopped()) break;
                waitIfPaused();

                String currentPage = pagesToVisit.poll();
                if (visitedPages.contains(currentPage)) continue;

                visitedPages.add(currentPage);
                driver.get(currentPage);

                List<WebElement> links = driver.findElements(By.tagName("a"));

                for (WebElement link : links) {

                    if (isStopped()) break;
                    waitIfPaused();

                    String href = link.getAttribute("href");
                    if (href == null || href.isEmpty()) continue;

                    // ✅ DOMAIN BASED FILTER (FIXED)
                    String linkDomain = getDomain(href);
                    if (!linkDomain.equalsIgnoreCase(baseDomain)) continue;

                    if (!linkStatusMap.containsKey(href)) {
                        checkLinkStatus(href);
                        totalLinks++;
                    }

                    if (!visitedPages.contains(href)) {
                        pagesToVisit.add(href);
                    }
                }
            }

            // ---------- DEMO MODE ----------
            if (demoMode) {
                injectDemo("https://httpstat.us/404");
                injectDemo("https://httpstat.us/403");
            }

        } catch (Exception e) {
            e.printStackTrace();
        } finally {

            // ALWAYS SAVE RESULTS
            try {
                if (!linkStatusMap.isEmpty()) {
                    saveDetailedLinks();
                    generateJsonReport(startUrl);
                }
            } catch (Exception e) {}

            new File(LOCK).delete();
            new File(PAUSE).delete();

            driver.quit();
            System.out.println("Scan Finished.");
        }
    }

    // ---------- LINK CHECK ----------
    static void checkLinkStatus(String url) {

        updateProgress(url, "Checking");

        try {
            HttpURLConnection conn = (HttpURLConnection) new URL(url).openConnection();
            conn.setRequestMethod("GET");
            conn.setRequestProperty("User-Agent", "Mozilla/5.0");
            conn.setConnectTimeout(6000);
            conn.connect();

            int code = conn.getResponseCode();
            linkStatusMap.put(url, code);
            checkedLinks++;

            if (code >= 404) brokenLinks++;
            else if (code == 401 || code == 403) suspectLinks++;

            updateProgress(url, String.valueOf(code));

        } catch (Exception e) {
            linkStatusMap.put(url, 500);
            checkedLinks++;
            brokenLinks++;
            updateProgress(url, "Failed");
        }
    }

    // ---------- DEMO ----------
    static void injectDemo(String url) {
        linkStatusMap.put(url, 404);
        totalLinks++;
        brokenLinks++;
    }

    // ---------- SAVE LINKS ----------
    static void saveDetailedLinks() {
        try {
            JsonArray arr = new JsonArray();

            for (Map.Entry<String, Integer> entry : linkStatusMap.entrySet()) {
                JsonObject obj = new JsonObject();
                obj.addProperty("url", entry.getKey());
                obj.addProperty("status", entry.getValue());
                arr.add(obj);
            }

            Gson gson = new GsonBuilder().setPrettyPrinting().create();
            FileWriter writer = new FileWriter(LINKS);
            gson.toJson(arr, writer);
            writer.close();

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // ---------- FINAL REPORT ----------
    static void generateJsonReport(String website) {
        try {
            JsonObject json = new JsonObject();
            json.addProperty("website", website);
            json.addProperty("totalLinks", totalLinks);
            json.addProperty("brokenLinks", brokenLinks);
            json.addProperty("suspectLinks", suspectLinks);

            Gson gson = new GsonBuilder().setPrettyPrinting().create();
            FileWriter writer = new FileWriter(REPORT);
            gson.toJson(json, writer);
            writer.close();

            updateProgress("FINISHED", "COMPLETED");

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // ---------- REALISTIC PROGRESS ----------
    static void updateProgress(String url, String status) {
        try {

            int discovered = linkStatusMap.size();
            int queueSize = pagesToVisit.size();

            int estimatedRemaining = queueSize * 15;
            int totalEstimate = discovered + estimatedRemaining;

            if (totalEstimate == 0) totalEstimate = 1;

            int percent = (int)((checkedLinks * 100.0) / totalEstimate);

            if (percent >= 99 && !url.equals("FINISHED")) percent = 99;

            JsonObject p = new JsonObject();
            p.addProperty("checked", checkedLinks);
            p.addProperty("percent", percent);
            p.addProperty("currentUrl", url);
            p.addProperty("status", status);

            Gson gson = new GsonBuilder().setPrettyPrinting().create();
            FileWriter writer = new FileWriter(PROGRESS);
            gson.toJson(p, writer);
            writer.close();

        } catch (Exception e) {}
    }

    // ---------- CONTROL ----------
    static boolean isStopped() {
        return new File(STOP).exists();
    }

    static void waitIfPaused() {
        try {
            while (new File(PAUSE).exists()) {
                Thread.sleep(1000);
            }
        } catch (Exception e) {}
    }
}
