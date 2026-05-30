<div align="center">

# 🔐 WebCheck360

### AI-Powered Website Intelligence, Security Analysis & Monitoring Platform

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Java](https://img.shields.io/badge/Java-17-ED8B00?style=for-the-badge&logo=openjdk&logoColor=white)](https://openjdk.org)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Selenium](https://img.shields.io/badge/Selenium-4.x-43B02A?style=for-the-badge&logo=selenium&logoColor=white)](https://selenium.dev)
[![OpenAI](https://img.shields.io/badge/GPT--3.5-OpenRouter-412991?style=for-the-badge&logo=openai&logoColor=white)](https://openrouter.ai)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production-brightgreen?style=for-the-badge)]()

<br>

**WebCheck360** is an enterprise-grade, full-stack web application that performs comprehensive website health auditing, AI-powered risk analysis, phishing detection, performance benchmarking, and intelligent monitoring — all from a single, unified dashboard.

<br>

[Features](#-key-features) · [Architecture](#-system-architecture) · [Installation](#-installation-guide) · [Modules](#-core-modules) · [Contributing](#-contributing)

</div>

---

## 📋 Project Overview

**WebCheck360** is a sophisticated website intelligence platform designed to provide complete 360-degree visibility into any website's health, security posture, and performance characteristics. Built as a multi-layered full-stack application, it combines automated web crawling, real-time performance benchmarking, machine-learning-inspired risk scoring, RDAP-based domain intelligence, and GPT-powered natural language analysis into a single cohesive platform.

### The Problem

Website administrators, security analysts, and digital marketers need to constantly monitor their web properties for broken links, performance degradation, security vulnerabilities, and trust signals. Traditionally, this requires juggling multiple disconnected tools — link checkers, speed testers, SSL validators, domain reputation services, and manual security audits. This fragmented approach is time-consuming, error-prone, and lacks the contextual intelligence needed for informed decision-making.

### The Solution

WebCheck360 unifies all of these capabilities into one platform. A single scan of any URL triggers a full-stack automated pipeline: a headless Selenium-based Java crawler discovers and validates every link across up to 5 pages, a cURL-powered speed analyzer benchmarks Time to First Byte (TTFB), load times, and page sizes across multiple runs, a hybrid trust engine evaluates 10+ phishing risk indicators including RDAP domain age lookups, and a weighted risk engine computes composite scores across structural, performance, and security dimensions. The results are then visualized on an interactive Chart.js dashboard, stored in MySQL for historical trending, and optionally analyzed by GPT-3.5 Turbo via OpenRouter for natural language executive summaries and actionable recommendations.

### Target Users

- **Web Developers** monitoring their projects for broken links and performance issues
- **Security Analysts** evaluating domain trustworthiness and phishing indicators
- **Digital Marketers** tracking website health metrics over time
- **IT Administrators** performing routine website audits
- **Students & Researchers** exploring full-stack development, AI integration, and automation

### Real-World Use Cases

- Run a full broken-link audit on a corporate website before launch
- Benchmark page load speed against previous deployments
- Detect phishing attempts by analyzing suspicious domain characteristics
- Generate PDF audit reports for stakeholder review
- Chat with an AI assistant about website security best practices

---

## ✨ Key Features

| # | Feature | Description | Technologies |
|---|---------|-------------|--------------|
| 1 | **Automated Website Scanner** | Crawls up to 5 pages per domain, discovers all `<a>` links, checks HTTP status codes, and classifies links as healthy, broken (4xx/5xx), or suspect (401/403) | Java, Selenium, ChromeDriver |
| 2 | **Performance Analyzer** | Multi-run speed test measuring TTFB, full load time, page size, stability score, and performance grade (Excellent/Good/Average/Slow) | PHP, cURL |
| 3 | **AI Risk Insight Engine** | Sends composite risk scores to GPT-3.5 Turbo for executive summaries, root cause analysis, business impact assessment, and prioritized action plans | OpenRouter API, GPT-3.5 Turbo |
| 4 | **Enterprise Trust Engine** | Hybrid phishing detection with 10 analysis vectors: HTTPS check, raw IP detection, suspicious TLDs, subdomain depth, keyword analysis, domain length, hyphen abuse, homograph detection, brand impersonation, and RDAP domain age | PHP, RDAP Protocol |
| 5 | **Weighted Risk Scoring** | Composite risk score computed from structural risk (40%), performance risk (40%), and security risk (20%) with confidence scoring and dominant factor identification | PHP (Custom Engine) |
| 6 | **Interactive Dashboard** | Real-time charts showing scan distribution, speed test trends, and risk monitoring data with Chart.js-powered doughnut, bar, and line charts | Chart.js, JavaScript |
| 7 | **AI Chat Assistant** | Conversational AI interface for website security Q&A with persistent conversation history, multi-session management, and contextual responses | OpenRouter API, GPT-3.5 Turbo |
| 8 | **PDF Report Generation** | One-click downloadable audit reports with scan summaries, health scores, and broken link tables generated server-side | FPDF Library |
| 9 | **Scan History & Comparison** | Historical database of all scans with per-user isolation, detailed view pages, and trend tracking across multiple scan sessions | MySQL, PHP |
| 10 | **Speed Monitoring Dashboard** | Time-series performance tracking with multi-URL comparison, Chart.js visualizations, and performance trend analysis | Chart.js, MySQL |
| 11 | **Risk Monitoring Dashboard** | Historical risk score tracking with radar charts, trend lines, and AI-generated insight history | Chart.js, MySQL |
| 12 | **Domain Intelligence** | RDAP-based domain registration lookups with database caching for domain age analysis and registration date verification | RDAP Protocol, MySQL Cache |
| 13 | **Real-Time Scan Progress** | Live progress bar with percentage tracking, current URL display, and scan control buttons (Stop/Pause/Resume/Reset) | JSON Polling, JavaScript |
| 14 | **Role-Based Access Control** | Admin and viewer roles with admin-exclusive features like login activity monitoring | PHP Sessions |
| 15 | **Login Activity Monitoring** | Admin-only audit trail recording user login timestamps and IP addresses | MySQL, PHP |

---

## 🏗 System Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                        CLIENT BROWSER                               │
│          HTML5 / CSS3 / JavaScript / Chart.js                       │
└──────────────────────────┬──────────────────────────────────────────┘
                           │ HTTP Requests
                           ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      APACHE / PHP 8.2                               │
│                                                                     │
│  ┌──────────────┐  ┌──────────────┐  ┌───────────────────────────┐ │
│  │  PUBLIC LAYER │  │  API LAYER   │  │      MODULE LAYER         │ │
│  │              │  │              │  │                           │ │
│  │  Dashboard   │  │  Login       │  │  Website Scanner          │ │
│  │  Scanner     │  │  Register    │  │  Speed Analyzer           │ │
│  │  Report      │  │  Logout      │  │  AI Insight Engine        │ │
│  │  Analysis    │  │  Password    │  │  Trust Engine (RDAP)      │ │
│  │  Chatbot     │  │              │  │  Chatbot Backend          │ │
│  │  Speed Test  │  │              │  │  PDF Generator            │ │
│  │  History     │  │              │  │  Scan DB Storage          │ │
│  └──────┬───────┘  └──────┬───────┘  └───────────┬───────────────┘ │
│         │                 │                      │                  │
│         ▼                 ▼                      ▼                  │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │                   APP CORE LAYER                            │    │
│  │  db.php │ session_config.php │ engine.php │ fetch_data.php  │    │
│  │  ai_engine.php │ ai_config.php │ sidebar │ topbar           │    │
│  └──────────────────────────┬──────────────────────────────────┘    │
└─────────────────────────────┼──────────────────────────────────────┘
                              │
              ┌───────────────┼───────────────────┐
              ▼               ▼                   ▼
┌──────────────────┐ ┌────────────────┐ ┌─────────────────────┐
│   MySQL/MariaDB  │ │  OpenRouter AI │ │  Java Crawler Engine │
│                  │ │  (GPT-3.5)     │ │                     │
│  users           │ │                │ │  Selenium WebDriver │
│  scans           │ │  Risk Insights │ │  ChromeDriver       │
│  scan_links      │ │  Chat AI       │ │  Link Checker       │
│  speed_tests     │ │                │ │  JSON Reporter      │
│  ai_analysis     │ └────────────────┘ │  Progress Tracker   │
│  chatbot_*       │                    └─────────────────────┘
│  login_logs      │                          │
│  domain_cache    │                          │
│  trust_history   │         ┌────────────────┘
└──────────────────┘         ▼
                    ┌─────────────────────┐
                    │   RDAP Protocol     │
                    │   (rdap.org)        │
                    │   Domain Age Lookup │
                    └─────────────────────┘
```

### Layer Descriptions

| Layer | Responsibility |
|-------|---------------|
| **Public Layer** | User-facing pages — dashboard, scanner, reports, analysis, chatbot, speed test, and history views |
| **API Handlers** | Authentication endpoints — login, register, password change, and logout processing |
| **Module Layer** | Backend processing — scan execution, speed benchmarking, AI insights, chatbot logic, PDF generation |
| **App Core** | Shared services — database connectivity, session management, risk engine, AI service wrapper, UI partials |
| **Crawler Engine** | Java-based headless browser automation for multi-page link discovery and HTTP status validation |
| **AI Layer** | GPT-3.5 Turbo integration via OpenRouter for natural language risk analysis and conversational assistance |
| **Database Layer** | MySQL/MariaDB for persistent storage of users, scans, speed tests, AI analysis, chatbot conversations, and cached domain intelligence |

---

## 🛠 Technology Stack

### Backend

| Technology | Version | Purpose |
|-----------|---------|---------|
| **PHP** | 8.2+ | Server-side logic, API handling, session management, risk computation |
| **Java** | 17+ | Headless browser automation and multi-page web crawling |
| **Apache** | 2.4+ | HTTP server (via XAMPP) |

### Frontend

| Technology | Purpose |
|-----------|---------|
| **HTML5** | Semantic page structure |
| **CSS3** | Custom dark-theme UI with glassmorphism, gradients, and responsive layouts |
| **JavaScript (ES6+)** | DOM manipulation, async fetch API, real-time polling, Chart.js integration |
| **Chart.js** | Interactive doughnut, bar, line, and radar charts for data visualization |

### Database

| Technology | Purpose |
|-----------|---------|
| **MySQL / MariaDB** | Relational data storage with prepared statements and user-isolated queries |

### AI & Machine Learning

| Technology | Purpose |
|-----------|---------|
| **OpenRouter API** | Gateway to GPT-3.5 Turbo for risk insight generation and chatbot responses |
| **GPT-3.5 Turbo** | Natural language risk analysis, executive summaries, and conversational AI |

### Automation & Crawling

| Technology | Purpose |
|-----------|---------|
| **Selenium WebDriver** | Headless Chrome browser automation for dynamic page rendering |
| **ChromeDriver** | Chrome browser interface for Selenium |
| **WebDriverManager** | Automatic driver binary management for Selenium |
| **Gson** | Java JSON serialization for progress, report, and link data files |

### External Services & Protocols

| Service | Purpose |
|---------|---------|
| **RDAP (rdap.org)** | Domain registration age lookup for trust scoring |
| **cURL** | Server-side HTTP requests for speed testing and API communication |
| **FPDF** | Server-side PDF generation for downloadable audit reports |

---

## 📁 Folder Structure

```
WebCheck360/
│
├── app/                              # Core Application Layer
│   ├── config/
│   │   └── ai_config.php             # AI API keys and model configuration
│   ├── helpers/
│   │   ├── db.php                    # MySQL database connection
│   │   └── session_config.php        # Session management + auto-logout
│   ├── services/
│   │   ├── ai_engine.php             # GPT API wrapper (reusable AI caller)
│   │   ├── engine.php                # Weighted risk scoring engine
│   │   └── fetch_data.php            # User-scoped data retrieval service
│   └── views/partials/
│       ├── sidebar.php               # Navigation sidebar component
│       └── topbar.php                # Top navigation bar with user dropdown
│
├── public/                           # User-Facing Pages
│   ├── index.php                     # Dashboard with scan/speed/risk overview
│   ├── login.php                     # Authentication page
│   ├── register.php                  # New user registration
│   ├── change_password.php           # Password update page
│   ├── scanner.php                   # Website scanner with live progress
│   ├── report.php                    # Scan report viewer
│   ├── analysis.php                  # AI risk analysis dashboard
│   ├── chatbot.php                   # AI chat assistant interface
│   ├── speed_test.php                # Performance benchmarking tool
│   ├── view_scan.php                 # Detailed individual scan viewer
│   ├── assets/css/                   # Stylesheets (12 CSS files)
│   └── history/
│       ├── scan_history.php          # All saved scan records
│       ├── speed_history.php         # Speed test history + trends
│       ├── risk_history.php          # Risk analysis history + radar charts
│       └── login_history.php         # Admin-only login activity log
│
├── modules/                          # Backend Processing Modules
│   ├── website_scanner/
│   │   ├── run_scan.php              # Launches Java crawler
│   │   ├── stop_scan.php             # Signal crawler to stop
│   │   ├── pause_scan.php            # Signal crawler to pause
│   │   ├── resume_scan.php           # Resume paused crawler
│   │   ├── reset_scan.php            # Clean all scan artifacts
│   │   └── save_to_db.php            # Persist scan results to MySQL
│   ├── speed_analyzer/
│   │   └── run_speed_test.php        # Multi-run cURL speed benchmarker
│   ├── ai_insight_engine/
│   │   ├── generate_risk_insight.php # GPT risk analysis endpoint
│   │   └── engine/
│   │       └── trust_engine.php      # 10-vector phishing trust engine + RDAP
│   ├── chatbot/
│   │   ├── chatbot_api.php           # AI chat message handler
│   │   ├── chatbot_new.php           # Create new conversation
│   │   ├── chatbot_list.php          # List all conversations
│   │   ├── chatbot_load.php          # Load specific conversation
│   │   ├── chatbot_history.php       # Get conversation messages
│   │   ├── chatbot_save.php          # Save message to database
│   │   ├── chatbot_delete.php        # Delete a conversation
│   │   └── chatbot_clear.php         # Clear all user messages
│   └── reports/
│       └── generate_pdf.php          # FPDF-based PDF report generator
│
├── api/handlers/                     # Authentication API Endpoints
│   ├── login_process.php             # Login validation + session creation
│   ├── register_process.php          # New user registration handler
│   ├── change_password_process.php   # Password update handler
│   ├── logout.php                    # Session destruction
│   └── authenticate.php             # Legacy authentication endpoint
│
├── crawler/java/automation-engine/   # Java Selenium Crawler
│   ├── src/main/java/com/webcheck360/
│   │   ├── automationengine/App.java # Main crawler — link discovery + validation
│   │   ├── checker/                  # HTTP status checking utilities
│   │   ├── crawler/                  # Page crawling logic
│   │   ├── model/                    # Data models (ScanResult, LinkInfo, etc.)
│   │   └── util/                     # JSON and HTTP utility classes
│   ├── pom.xml                       # Maven build configuration
│   └── target/                       # Compiled JAR files
│
├── vendor/fpdf/                      # FPDF PDF generation library
├── database/                         # Database migration directory (future)
├── storage/                          # Runtime storage (logs, cache, exports)
├── .gitignore                        # Git exclusion rules
└── render.yaml                       # Render.com deployment configuration
```

---

## 🔧 Core Modules

### 1. Website Scanner

**Objective:** Automatically discover and validate every hyperlink across a target website.

| Aspect | Details |
|--------|---------|
| **Input** | Target URL entered by user (with optional Demo Mode) |
| **Crawling** | Selenium opens Chrome, visits up to 5 pages within the same domain, extracts all `<a href>` elements |
| **Validation** | Each unique link is checked via HTTP GET request with 6-second timeout |
| **Classification** | `200-399` = Healthy, `401/403` = Suspect, `404+` = Broken |
| **Progress** | Real-time progress.json updates with checked count, percentage, and current URL |
| **Controls** | Stop, Pause, Resume, and Reset via flag files (stop.flag, pause.flag) |
| **Output** | report.json (summary), links.json (detailed per-link data), scan.lock (state) |
| **Storage** | Results persisted to `scans` and `scan_links` tables with user isolation |
| **Technologies** | Java 17, Selenium WebDriver, ChromeDriver, Gson, Maven |

### 2. Speed Analyzer

**Objective:** Benchmark website performance with multi-run statistical analysis.

| Aspect | Details |
|--------|---------|
| **Input** | Target URL entered by user |
| **Method** | 3 sequential cURL requests measuring TTFB, total load time, and response size |
| **Metrics** | Average TTFB (ms), Average Load Time (ms), Average Page Size (KB), Stability Score (%) |
| **Scoring** | Performance Score (0-100) based on load time, TTFB, and page size thresholds |
| **Grading** | Excellent (≥80), Good (≥60), Average (≥40), Slow (<40) |
| **Output** | Real-time JSON response with all metrics, saved to `speed_tests` table |
| **Technologies** | PHP, cURL, MySQL |

### 3. AI Risk Insight Engine

**Objective:** Generate natural language risk analysis using GPT-3.5 Turbo.

| Aspect | Details |
|--------|---------|
| **Input** | Composite risk score, risk level, and category breakdown (structural, performance, security) |
| **Processing** | Sends structured prompt to OpenRouter API with cybersecurity consultant persona |
| **Output** | Executive summary, root cause analysis, business impact, prioritized action plan, strategic recommendations |
| **Storage** | Analysis results saved to `ai_analysis_history` table |
| **Technologies** | OpenRouter API, GPT-3.5 Turbo, PHP cURL |

### 4. Enterprise Trust Engine

**Objective:** Hybrid phishing detection combining heuristic analysis with RDAP domain intelligence.

| # | Analysis Vector | Penalty | Description |
|---|----------------|---------|-------------|
| 1 | HTTPS Encryption | -20 | Checks for secure protocol |
| 2 | Raw IP Address | -30 | Detects IP-based URLs |
| 3 | Suspicious TLD | -15 | Flags .xyz, .tk, .zip, etc. |
| 4 | Subdomain Depth | -10 | Flags >3 subdomain levels |
| 5 | Suspicious Keywords | -10 | Detects "login", "verify", "banking", etc. in domain |
| 6 | Domain Length | -10 | Flags domains >30 characters |
| 7 | Excessive Hyphens | -10 | Flags ≥3 hyphens |
| 8 | Homograph Attack | -5 | Detects numeric character substitution |
| 9 | Brand Impersonation | -35 | Detects fake PayPal, Amazon, Google, ICICI, HDFC domains |
| 10 | Domain Age (RDAP) | -10 to -30 | Queries rdap.org for registration date, penalizes new domains |

**Classification:** LIKELY LEGIT (≥80), SUSPICIOUS (50-79), HIGH PHISHING RISK (<50)

**RDAP Caching:** Domain age lookups are cached in `domain_intelligence_cache` table to avoid repeated API calls.

### 5. Weighted Risk Scoring Engine

**Objective:** Compute a composite risk score from multiple data dimensions.

```
Risk Score = (Structural Risk × 0.4) + (Performance Risk × 0.4) + (Security Risk × 0.2)
```

| Factor | Weight | Source |
|--------|--------|--------|
| Structural Risk | 40% | Broken link ratio from scan results |
| Performance Risk | 40% | Performance score, TTFB, and page size from speed tests |
| Security Risk | 20% | HTTPS presence check |

**Output:** Risk Score (0-100), Level (SAFE/MODERATE/HIGH RISK), Confidence Score, Dominant Factor, Recommendations

### 6. AI Chat Assistant

**Objective:** Conversational AI interface for website security Q&A.

| Aspect | Details |
|--------|---------|
| **Model** | GPT-3.5 Turbo via OpenRouter |
| **Features** | Multi-conversation management, persistent chat history, conversation deletion |
| **Storage** | `chatbot_conversations` and `chatbot_messages` tables with user isolation |
| **Interface** | Real-time message rendering with typing indicators |

### 7. PDF Report Generator

**Objective:** Generate downloadable PDF audit reports.

| Aspect | Details |
|--------|---------|
| **Library** | FPDF (PHP) |
| **Content** | Website URL, total links, broken links, suspect links, health score, detailed issue table |
| **Output** | Browser download as `WebCheck360_Audit_Report.pdf` |

### 8. Dashboard & Monitoring

**Objective:** Unified overview of all platform metrics.

| Dashboard | Visualizations |
|-----------|---------------|
| **Main Dashboard** | Scan distribution (doughnut), speed test distribution (doughnut), speed trends (bar chart) |
| **Speed Monitoring** | Multi-URL comparison, time-series load times, TTFB trends, performance score history |
| **Risk Monitoring** | Risk score trends, radar charts, historical AI insights |

---

## 🗄 Database Design

*Database schema inferred from project implementation.*

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `users` | User accounts and authentication | id, name, email, password (bcrypt), role |
| `scans` | Saved scan summaries | id, user_id, website, total_links, broken_links, scanned_at |
| `scan_links` | Individual link results per scan | id, scan_id, url, status_code |
| `speed_tests` | Performance benchmark results | id, user_id, url, load_time, ttfb, page_size, performance_score, grade |
| `ai_analysis_history` | Saved AI risk insights | id, user_id, url, risk_score, insight, analyzed_at |
| `chatbot_conversations` | Chat session metadata | id, user_id, title, created_at |
| `chatbot_messages` | Individual chat messages | id, conversation_id, user_id, role, message |
| `login_logs` | Login activity audit trail | id, user_id, login_time, ip_address |
| `domain_intelligence_cache` | RDAP lookup cache | domain, domain_age_days, created_date |
| `website_trust_history` | Historical trust score records | (trust analysis tracking) |

All queries use **prepared statements** with parameterized binding to prevent SQL injection. Data is **user-isolated** — each user can only access their own scans, speed tests, and analysis history.

---

## 🔌 API Integrations

### OpenRouter (GPT-3.5 Turbo)

WebCheck360 integrates with the **OpenRouter AI gateway** for two distinct use cases:

**1. Risk Insight Generation**
```
POST https://openrouter.ai/api/v1/chat/completions

System Role: "Senior cybersecurity and performance risk consultant"
Input: Composite risk scores + category breakdown
Output: Executive summary, root cause analysis, business impact, action plan
```

**2. Chat Assistant**
```
POST https://openrouter.ai/api/v1/chat/completions

System Role: Configurable per conversation context
Input: User message + conversation history
Output: Contextual AI response
```

### RDAP Protocol

The Trust Engine queries **rdap.org** for domain registration data:
```
GET https://rdap.org/domain/{domain}

Response: Registration events including creation date
Used for: Domain age calculation → trust score adjustment
Caching: Results cached in MySQL to minimize API calls
```

> ⚠️ **Security Note:** API keys are stored in `app/config/ai_config.php`. Before deployment, migrate these to environment variables.

---

## 🔒 Security Features

| Feature | Implementation |
|---------|---------------|
| **Password Hashing** | `password_hash()` with `PASSWORD_DEFAULT` (bcrypt) |
| **Session Security** | HTTP-only cookies, SameSite=Lax, session regeneration on login |
| **Auto-Logout** | 7-day inactivity timeout with automatic session destruction |
| **Session Fixation Prevention** | `session_regenerate_id(true)` after successful authentication |
| **SQL Injection Prevention** | Prepared statements with `bind_param()` across all database queries |
| **Input Validation** | Server-side email and URL validation before processing |
| **Error Suppression** | `display_errors=0` in production with `error_log` for server-side logging |
| **Brute Force Mitigation** | 1-second `sleep()` delay on failed login attempts |
| **Role-Based Access** | Admin and viewer roles with feature-gated access controls |
| **Phishing Detection** | 10-vector trust analysis engine with RDAP intelligence |
| **CSRF Protection** | POST-method enforcement on all state-changing operations |

---

## 🔄 Workflow

### Complete Scan Workflow

```
1. USER ENTERS URL
   └── User types a website URL on the Scanner page
        └── Optional: Enable "Demo Mode" for injected test failures

2. SCAN INITIATED
   └── PHP creates scan.lock file
   └── Cleans previous scan artifacts (progress.json, report.json, links.json)
   └── Launches Java JAR as background process via `pclose(popen(...))`

3. CRAWLER EXECUTES
   └── Selenium opens headless Chrome browser
   └── Visits target URL → extracts all <a> links
   └── Filters links to same domain only
   └── Queues discovered pages (max 5 pages)
   └── Checks each unique link via HTTP GET
   └── Writes progress.json after every link check

4. REAL-TIME MONITORING
   └── Browser polls progress.json every second via fetch()
   └── Updates progress bar, percentage, and current URL display
   └── User can Stop / Pause / Resume at any time via flag files

5. SCAN COMPLETE
   └── Crawler writes final report.json and links.json
   └── Removes scan.lock → browser detects completion
   └── Automatically redirects to Report page

6. REPORT & ANALYSIS
   └── Report page displays health score, broken/suspect links
   └── User can: Download PDF | Save to Database | View History

7. RISK ANALYSIS
   └── Analysis page fetches scan + speed data from database
   └── Risk engine computes weighted composite score
   └── Trust engine runs 10-vector phishing analysis
   └── User clicks "Generate AI Insight" → GPT analyzes results

8. HISTORICAL TRACKING
   └── All results stored in MySQL with user isolation
   └── Dashboards show trends via Chart.js visualizations
```

---

## 🚀 Installation Guide

### Prerequisites

| Software | Version | Purpose |
|----------|---------|---------|
| **XAMPP** | 8.2+ | Apache + MySQL + PHP bundle |
| **Java JDK** | 17+ | Required for crawler execution |
| **Maven** | 3.9+ | Build tool for Java crawler |
| **Google Chrome** | Latest | Required for Selenium WebDriver |
| **Git** | 2.x | Version control |

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/WebCheck360.git
cd WebCheck360
```

### Step 2: Place in XAMPP htdocs

```bash
# Copy/move the project to your XAMPP htdocs directory
cp -r WebCheck360 /path/to/xampp/htdocs/WebCheck360
```

### Step 3: Create MySQL Database

```sql
CREATE DATABASE webcheck360
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;
```

Import the schema or let the application create tables on first use.

### Step 4: Configure Database

Edit `app/helpers/db.php`:

```php
$host = "localhost";
$user = "root";
$pass = "";          // Your MySQL password
$db   = "webcheck360";
```

### Step 5: Configure AI API Key

Edit `app/config/ai_config.php`:

```php
define("OPENAI_API_KEY", "your-openrouter-api-key-here");
define("OPENAI_MODEL", "openai/gpt-3.5-turbo");
define("OPENAI_API_URL", "https://openrouter.ai/api/v1/chat/completions");
```

Get your API key at [openrouter.ai](https://openrouter.ai).

### Step 6: Build Java Crawler

```bash
cd crawler/java/automation-engine
mvn clean package -DskipTests
```

### Step 7: Update Scan Paths

Edit `modules/website_scanner/run_scan.php` and update:

```php
$basePath = "/path/to/your/htdocs/WebCheck360/";
$jarPath  = "/path/to/your/htdocs/WebCheck360/crawler/java/automation-engine/target/automation-engine-1.0-SNAPSHOT.jar";
```

Similarly, update `$basePath` in `stop_scan.php`, `pause_scan.php`, `resume_scan.php`, and `reset_scan.php`.

### Step 8: Start XAMPP

1. Start **Apache** from the XAMPP Control Panel
2. Start **MySQL** from the XAMPP Control Panel

### Step 9: Launch the Application

Open your browser and navigate to:

```
http://localhost/WebCheck360/public/login.php
```

Register a new account and start scanning!

---

## ⚙ Configuration

### Database Configuration

| File | Settings |
|------|----------|
| `app/helpers/db.php` | `$host`, `$user`, `$pass`, `$db` |

### AI Configuration

| File | Settings |
|------|----------|
| `app/config/ai_config.php` | `OPENAI_API_KEY`, `OPENAI_MODEL`, `OPENAI_API_URL` |

### Session Configuration

| File | Settings |
|------|----------|
| `app/helpers/session_config.php` | Cookie lifetime (7 days), HTTP-only, SameSite policy |

> 🔐 **Important:** Never commit API keys to version control. Use environment variables in production.

---

## 📸 Screenshots

> Screenshots can be added to a `/docs/screenshots/` directory.

| Page | Description |
|------|-------------|
| 🏠 **Dashboard** | Main overview with scan distribution charts and speed trends |
| 🔍 **Scanner** | URL input with live progress bar and scan controls |
| 📄 **Report** | Health score display with broken link details |
| 🤖 **AI Analysis** | Risk gauges, trust scores, and AI-generated insights |
| 💬 **Chatbot** | Conversational AI interface with history sidebar |
| ⚡ **Speed Test** | Performance metrics with real-time results |
| 📊 **History** | Tabular history views with detailed scan records |

---

## 🔮 Future Enhancements

| # | Enhancement | Description |
|---|------------|-------------|
| 1 | **REST API Architecture** | Expose all features via a documented REST API for third-party integration |
| 2 | **Email Alert System** | Automatic notifications when scan results exceed risk thresholds |
| 3 | **Cloud Deployment** | Docker containerization with CI/CD pipeline for AWS/GCP/Azure |
| 4 | **ML Risk Prediction** | Train a machine learning model on historical scan data for predictive risk scoring |
| 5 | **Scheduled Monitoring** | Cron-based automatic recurring scans with alerting |
| 6 | **Multi-User Teams** | Organization-level accounts with shared dashboards and team permissions |
| 7 | **Accessibility Audit** | WCAG compliance checking integrated into the scan pipeline |
| 8 | **SEO Analysis Module** | Meta tag validation, heading hierarchy checks, and structured data analysis |
| 9 | **SSL Certificate Inspector** | Detailed certificate chain analysis and expiry monitoring |
| 10 | **Custom Scan Rules** | User-defined link validation rules and whitelists |
| 11 | **Webhook Integrations** | Push scan results to Slack, Discord, or custom endpoints |
| 12 | **Dark/Light Theme Toggle** | User-selectable UI theme preference |
| 13 | **Export to CSV/Excel** | Bulk export of scan and speed data for external analysis |
| 14 | **Competitor Comparison** | Side-by-side website health and performance benchmarking |
| 15 | **Browser Extension** | One-click scan initiation from any webpage |
| 16 | **Mobile Responsive Redesign** | Fully adaptive layout for mobile and tablet devices |
| 17 | **Two-Factor Authentication** | TOTP-based 2FA for enhanced account security |

---

## 📚 Learning Outcomes

This project demonstrates proficiency across multiple software engineering domains:

| Domain | Skills Demonstrated |
|--------|-------------------|
| **Full-Stack Development** | End-to-end application development with PHP backend, JavaScript frontend, and MySQL database |
| **Java Application Development** | Maven-based project structure, Selenium automation, HTTP client programming |
| **AI/ML Integration** | GPT API integration, prompt engineering, structured AI output parsing |
| **Database Design** | Normalized schema, prepared statements, user-scoped queries, caching strategies |
| **Cybersecurity** | Phishing detection heuristics, HTTPS validation, session security, password hashing |
| **Web Crawling & Automation** | Headless browser automation, DOM traversal, link extraction, multi-page crawling |
| **Real-Time Systems** | JSON-based polling, live progress tracking, concurrent file-based state management |
| **Data Visualization** | Chart.js integration for doughnut, bar, line, and radar charts |
| **API Development** | RESTful endpoint design, JSON request/response handling, API error management |
| **DevOps Awareness** | Git workflow, .gitignore configuration, deployment manifests (render.yaml) |
| **Performance Engineering** | Multi-run benchmarking, statistical analysis, stability scoring |
| **Protocol Knowledge** | HTTP status codes, RDAP protocol, cURL options, session cookie management |

---

## 📊 Project Statistics

*Approximate project metrics based on codebase analysis.*

| Metric | Value |
|--------|-------|
| Total PHP Files | 49 |
| Total Java Files | 8 |
| Total CSS Files | 12 |
| Core Modules | 8 |
| Database Tables | 10 |
| User-Facing Pages | 14 |
| API Endpoints | 5 |
| AI Integrations | 2 (Risk Insight + Chatbot) |
| Trust Analysis Vectors | 10 |
| Technologies Used | 15+ |
| Lines of PHP Code | ~3,500+ |
| Lines of Java Code | ~300+ |
| External APIs | 2 (OpenRouter, RDAP) |

---

## 👨‍💻 Author

<div align="center">

### **Rohan Gupta**

**Full-Stack Developer | AI Integration Specialist | Automation Engineer**

[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://linkedin.com/in/your-profile)
[![GitHub](https://img.shields.io/badge/GitHub-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/your-username)
[![Email](https://img.shields.io/badge/Email-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:your-email@example.com)

</div>

---

## 📄 License

This project is licensed under the **MIT License**.

```
MIT License

Copyright (c) 2026 Rohan Gupta

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### Contribution Guidelines

- Follow existing code style and conventions
- Write descriptive commit messages
- Update documentation for new features
- Test your changes before submitting
- One feature per pull request

---

## 🙏 Acknowledgements

| Technology | Acknowledgement |
|-----------|----------------|
| [OpenRouter](https://openrouter.ai) | AI gateway for GPT model access |
| [Selenium](https://selenium.dev) | Browser automation framework |
| [Chart.js](https://chartjs.org) | Data visualization library |
| [FPDF](http://www.fpdf.org) | PDF generation library for PHP |
| [WebDriverManager](https://github.com/bonigarcia/webdrivermanager) | Automatic Selenium driver management |
| [Gson](https://github.com/google/gson) | JSON serialization for Java |
| [RDAP.org](https://rdap.org) | Domain registration data protocol |
| [XAMPP](https://apachefriends.org) | Local development environment |

---

<div align="center">

**Built with ❤️ by Rohan Gupta**

⭐ Star this repository if you found it useful!

</div>
