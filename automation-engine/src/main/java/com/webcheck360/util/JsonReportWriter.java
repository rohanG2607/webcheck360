package com.webcheck360.util;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.webcheck360.model.ScanReport;

import java.io.FileWriter;

public class JsonReportWriter {

    public static void writeReport(ScanReport report, String filePath) {

        try {
            Gson gson = new GsonBuilder()
                    .setPrettyPrinting()
                    .create();
            
            FileWriter writer = new FileWriter(filePath);
            gson.toJson(report, writer);
            writer.flush();
            writer.close();

            System.out.println("\nJSON report generated at:");
            System.out.println(filePath);

        } catch (Exception e) {
            System.out.println("Failed to write JSON report");
        }
    }
}
