<?php

function calculateRisk($data) {

    $brokenRatio = $data['broken_ratio'];      // 0–1
    $performance = $data['performance_score']; // 0–100
    $ttfb        = $data['ttfb'];              // seconds
    $pageSize    = $data['page_size'];         // KB
    $https       = $data['https_flag'];        // 1 or 0

    /* ============================
       1️⃣ STRUCTURAL RISK
    ============================ */

    $structuralRisk = min(100, $brokenRatio * 100 * 1.2);

    /* ============================
       2️⃣ PERFORMANCE RISK
    ============================ */

    $performancePenalty = 100 - $performance;

    $ttfbPenalty = min(100, $ttfb * 40); // scaling
    $sizePenalty = min(100, $pageSize / 20);

    $performanceRisk = min(100,
        ($performancePenalty * 0.5) +
        ($ttfbPenalty * 0.3) +
        ($sizePenalty * 0.2)
    );

    /* ============================
       3️⃣ SECURITY RISK
    ============================ */

    $securityRisk = $https ? 0 : 40;

    /* ============================
       4️⃣ WEIGHTED OVERALL SCORE
    ============================ */

    $riskScore = round(
        ($structuralRisk * 0.4) +
        ($performanceRisk * 0.4) +
        ($securityRisk * 0.2)
    );

    $riskScore = min(100, $riskScore);

    /* ============================
       5️⃣ CLASSIFICATION
    ============================ */

    if ($riskScore <= 30) $level = "SAFE";
    elseif ($riskScore <= 60) $level = "MODERATE";
    else $level = "HIGH RISK";

    /* ============================
       6️⃣ CONTRIBUTION BREAKDOWN
    ============================ */

    $totalRaw =
        ($structuralRisk * 0.4) +
        ($performanceRisk * 0.4) +
        ($securityRisk * 0.2);

    $contributions = [
        "Structural"  => round(($structuralRisk * 0.4 / $totalRaw) * 100),
        "Performance" => round(($performanceRisk * 0.4 / $totalRaw) * 100),
        "Security"    => round(($securityRisk * 0.2 / $totalRaw) * 100)
    ];

    /* ============================
       7️⃣ CONFIDENCE SCORE
    ============================ */

    $confidence = 100;

    if ($brokenRatio == 0) $confidence -= 10;
    if ($performance == 50) $confidence -= 10;
    if ($ttfb == 1) $confidence -= 10;

    $confidence = max(60, $confidence);

    /* ============================
       8️⃣ DOMINANT FACTOR
    ============================ */

    arsort($contributions);
    $dominantFactor = array_key_first($contributions);

    /* ============================
       9️⃣ RECOMMENDATIONS
    ============================ */

    $recommendations = [];

    if ($structuralRisk > 30) {
        $recommendations[] = "Fix broken and invalid internal links to improve crawl integrity.";
    }

    if ($performanceRisk > 30) {
        $recommendations[] = "Optimize server response time and reduce page load bottlenecks.";
    }

    if ($securityRisk > 0) {
        $recommendations[] = "Enable HTTPS to secure communication and improve trust.";
    }

    if (empty($recommendations)) {
        $recommendations[] = "No major optimizations required. Website appears stable.";
    }

    /* ============================
       RETURN STRUCTURED DATA
    ============================ */

    return [
        "riskScore"       => $riskScore,
        "level"           => $level,
        "structuralRisk"  => round($structuralRisk),
        "performanceRisk" => round($performanceRisk),
        "securityRisk"    => round($securityRisk),
        "contributions"   => $contributions,
        "confidence"      => $confidence,
        "dominantFactor"  => $dominantFactor,
        "recommendations" => $recommendations
    ];
}
?>