<?php
require_once("config/ai_config.php");

function generateAIResponse($systemPrompt, $context, $userMessage) {

    $data = [
        "model" => OPENAI_MODEL,
        "messages" => [
            ["role"=>"system","content"=>$systemPrompt],
            ["role"=>"system","content"=>$context],
            ["role"=>"user","content"=>$userMessage]
        ]
    ];

    $ch = curl_init(OPENAI_API_URL);

    curl_setopt_array($ch,[
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_POST=>true,
        CURLOPT_HTTPHEADER=>[
            "Authorization: Bearer " . OPENAI_API_KEY,
            "Content-Type: application/json",
            "HTTP-Referer: http://localhost/webcheck360",
            "X-Title: WebCheck360"
        ],
        CURLOPT_POSTFIELDS=>json_encode($data),
        CURLOPT_TIMEOUT=>25
    ]);

    $response = curl_exec($ch);

    if(curl_errno($ch)){
        return "Curl Error: " . curl_error($ch);
    }

    curl_close($ch);

    $json = json_decode($response,true);

    if(isset($json['error'])){
        return "API Error: " . $json['error']['message'];
    }

    return $json['choices'][0]['message']['content'] ?? "AI unavailable.";
}
?>