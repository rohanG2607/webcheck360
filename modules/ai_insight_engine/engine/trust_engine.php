<?php
require_once(__DIR__ . "/../../../app/helpers/db.php");

/* =========================================================
   ENTERPRISE TRUST ENGINE
   Hybrid phishing detection with RDAP intelligence
========================================================= */

function calculateTrustScore($url)
{
    global $conn;

    $flags = [];
    $score = 100;
    $confidence = 40;

    /* ---------------------------
       NORMALIZE DOMAIN
    ---------------------------- */
    $parsed = parse_url($url);
    $host = strtolower($parsed['host'] ?? $url);
    $host = preg_replace('/^www\./','',$host);

    $parts = explode('.', $host);
    $tld = end($parts);


    /* ---------------------------
       1️⃣ HTTPS CHECK
    ---------------------------- */

    if (stripos($url,"https://") !== 0) {
        $score -= 20;
        $flags[] = "Website does not use HTTPS encryption.";
        $confidence += 8;
    }


    /* ---------------------------
       2️⃣ RAW IP DOMAIN
    ---------------------------- */

    if (filter_var($host, FILTER_VALIDATE_IP)) {
        $score -= 30;
        $flags[] = "Website uses raw IP address instead of a domain.";
        $confidence += 15;
    }


    /* ---------------------------
       3️⃣ SUSPICIOUS TLD
    ---------------------------- */

    $suspiciousTlds = [
        'xyz','top','tk','gq','cf','ml','click','work','zip'
    ];

    if (in_array($tld,$suspiciousTlds)) {

        $score -= 15;
        $flags[] = "Suspicious domain extension detected (.$tld).";
        $confidence += 10;

    }


    /* ---------------------------
       4️⃣ SUBDOMAIN DEPTH
    ---------------------------- */

    if (count($parts) > 3) {

        $score -= 10;
        $flags[] = "Excessive subdomain depth detected.";
        $confidence += 8;

    }


    /* ---------------------------
       5️⃣ SUSPICIOUS KEYWORDS
    ---------------------------- */

    $keywords = [
        'login','verify','secure','account',
        'banking','update','confirm','wallet'
    ];

    foreach ($keywords as $kw) {

        if (strpos($host,$kw) !== false) {

            $score -= 10;
            $flags[] = "Suspicious keyword detected in domain ($kw).";
            $confidence += 8;
            break;

        }

    }


    /* ---------------------------
       6️⃣ DOMAIN LENGTH CHECK
    ---------------------------- */

    if (strlen($host) > 30) {

        $score -= 10;
        $flags[] = "Unusually long domain name.";
        $confidence += 6;

    }


    /* ---------------------------
       7️⃣ EXCESSIVE HYPHENS
    ---------------------------- */

    if (substr_count($host,'-') >= 3) {

        $score -= 10;
        $flags[] = "Domain contains excessive hyphens.";
        $confidence += 6;

    }


    /* ---------------------------
       8️⃣ HOMOGRAPH ATTACK CHECK
    ---------------------------- */

    if (preg_match('/[0-9]/',$host)) {

        $score -= 5;
        $flags[] = "Domain contains numeric characters that may imitate letters.";
        $confidence += 5;

    }


    /* ---------------------------
       9️⃣ BRAND IMPERSONATION
    ---------------------------- */

    $officialDomains = [
        'paypal.com',
        'amazon.com',
        'google.com',
        'facebook.com',
        'instagram.com',
        'icicibank.com',
        'hdfcbank.com'
    ];

    foreach ($officialDomains as $brand) {

        $brandName = explode('.',$brand)[0];

        if (
            strpos($host,$brandName) !== false &&
            strpos($host,$brand) === false
        ){

            $score -= 35;
            $flags[] = "Possible brand impersonation attempt ($brandName).";
            $confidence += 20;
            break;

        }

    }


    /* ---------------------------
       🔟 DOMAIN AGE (RDAP)
    ---------------------------- */

    $ageData = getDomainAgeCached($host);

    if ($ageData !== null) {

        $ageDays = $ageData['age'];

        if ($ageDays < 30) {

            $score -= 30;
            $flags[] = "Very recently registered domain ($ageDays days).";
            $confidence += 20;

        }
        elseif ($ageDays < 90) {

            $score -= 20;
            $flags[] = "Recently registered domain ($ageDays days).";
            $confidence += 15;

        }
        elseif ($ageDays < 365) {

            $score -= 10;
            $flags[] = "Domain less than 1 year old.";
            $confidence += 10;

        }

    }


    /* ---------------------------
       NORMALIZE SCORE
    ---------------------------- */

    $score = max(0,min(100,$score));
    $confidence = min(100,$confidence);


    /* ---------------------------
       CLASSIFICATION
    ---------------------------- */

    if ($score >= 80) {

        $status = "LIKELY LEGIT";
        $riskLevel = "LOW";

    }
    elseif ($score >= 50) {

        $status = "SUSPICIOUS";
        $riskLevel = "MEDIUM";

    }
    else {

        $status = "HIGH PHISHING RISK";
        $riskLevel = "HIGH";

    }


    return [

        'trustScore'=>$score,
        'status'=>$status,
        'riskLevel'=>$riskLevel,
        'flags'=>$flags,
        'confidence'=>$confidence

    ];

}



/* =========================================================
   RDAP DOMAIN AGE WITH DATABASE CACHE
========================================================= */

function getDomainAgeCached($domain)
{
    global $conn;


    /* 1️⃣ CHECK CACHE */

    $stmt = $conn->prepare("
        SELECT domain_age_days,created_date
        FROM domain_intelligence_cache
        WHERE domain=?
        LIMIT 1
    ");

    $stmt->bind_param("s",$domain);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if ($row) {

        return [
            'age'=>$row['domain_age_days'],
            'created'=>$row['created_date']
        ];

    }


    /* 2️⃣ RDAP LOOKUP */

    $rdapUrl = "https://rdap.org/domain/" . urlencode($domain);

    $context = stream_context_create([
        'http'=>[
            'timeout'=>8
        ]
    ]);

    $response = @file_get_contents($rdapUrl,false,$context);

    if (!$response) {
        return null;
    }

    $data = json_decode($response,true);

    if (!isset($data['events'])) {
        return null;
    }


    $createdDate = null;

    foreach ($data['events'] as $event) {

        if (
            isset($event['eventAction']) &&
            strtolower($event['eventAction']) === 'registration'
        ){

            $createdDate = $event['eventDate'];
            break;

        }

    }

    if (!$createdDate) {
        return null;
    }


    $createdTimestamp = strtotime($createdDate);

    $ageDays = floor(
        (time() - $createdTimestamp) / 86400
    );


    /* 3️⃣ SAVE CACHE */

    $insert = $conn->prepare("
        INSERT INTO domain_intelligence_cache
        (domain,domain_age_days,created_date)
        VALUES (?,?,?)
    ");

    $insert->bind_param(
        "sis",
        $domain,
        $ageDays,
        date("Y-m-d H:i:s",$createdTimestamp)
    );

    $insert->execute();


    return [

        'age'=>$ageDays,
        'created'=>$createdDate

    ];

}