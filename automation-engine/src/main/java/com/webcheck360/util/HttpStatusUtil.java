package com.webcheck360.util;

import java.net.HttpURLConnection;
import java.net.URL;

public class HttpStatusUtil {

    public static int getStatusCode(String link) {
        try {
            URL url = new URL(link);
            HttpURLConnection connection =
                    (HttpURLConnection) url.openConnection();

            // Use GET instead of HEAD
            connection.setRequestMethod("GET");

            // Set User-Agent to avoid bot blocking
            connection.setRequestProperty(
                    "User-Agent",
                    "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
            );

            connection.setConnectTimeout(7000);
            connection.setReadTimeout(7000);
            connection.connect();

            return connection.getResponseCode();

        } catch (Exception e) {
            return 500;
        }
    }
}
