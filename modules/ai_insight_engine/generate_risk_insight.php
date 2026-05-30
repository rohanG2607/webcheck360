<?php
require_once(__DIR__."/../../app/helpers/session_config.php");
require_once(__DIR__."/../../app/config/ai_config.php");

header("Content-Type: application/json");

/* Show errors temporarily for debugging */
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["insight" => "Unauthorized"]);
    exit;
}

/* ---------- READ INPUT ---------- */
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["insight" => "Invalid request"]);
    exit;
}

$url         = $input['url'] ?? '';
$riskScore   = $input['riskScore'] ?? 0;
$level       = $input['level'] ?? '';
$structural  = $input['structuralRisk'] ?? 0;
$performance = $input['performanceRisk'] ?? 0;
$security    = $input['securityRisk'] ?? 0;

/* ---------- BUILD CONTEXT ---------- */

$context = "
Website: $url

Overall Risk Score: $riskScore
Risk Level: $level

Category Breakdown:
- Structural Risk: $structural
- Performance Risk: $performance
- Security Risk: $security

Provide:
1. Executive Summary
2. Root Cause Analysis
3. Business Impact
4. Prioritized Action Plan
5. Strategic Recommendation

Keep tone concise, professional, and actionable.
";

/* ---------- PREPARE API CALL ---------- */

$data = [
    "model" => OPENAI_MODEL,
    "messages" => [
        [
            "role" => "system",
            "content" => "You are a senior cybersecurity and performance risk consultant."
        ],
        [
            "role" => "user",
            "content" => $context
        ]
    ],
    "temperature" => 0.4
];

$ch = curl_init(OPENAI_API_URL);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . OPENAI_API_KEY,
        "Content-Type: application/json"
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_TIMEOUT => 20
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    curl_close($ch);
    echo json_encode(["insight" => "AI service unavailable."]);
    exit;
}

curl_close($ch);

$json = json_decode($response, true);

$insight = $json['choices'][0]['message']['content'] ?? null;

if (!$insight) {
    echo json_encode(["insight" => "AI response unavailable."]);
    exit;
}

echo json_encode([
    "insight" => $insight
]);