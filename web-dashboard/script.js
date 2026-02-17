// ===== URL VALIDATION =====
function validateURL() {
    const urlInput = document.getElementById("website").value;
    const message = document.getElementById("msg");

    const pattern = /^(https?:\/\/)?([\w\-])+\.{1}[a-zA-Z]{2,}(\/.*)?$/;

    if (!pattern.test(urlInput)) {
        message.innerHTML = "❌ Please enter a valid URL (Example: https://example.com)";
        message.style.color = "red";
        return false;
    }

    message.innerHTML = "✅ Valid URL. Ready to scan!";
    message.style.color = "green";

    showLoader();
    return false; // stop form submit for demo
}

// ===== FAKE SCAN LOADER (for project demo) =====
function showLoader() {
    const loader = document.getElementById("loader");
    loader.style.display = "block";

    let progress = 0;
    const bar = document.getElementById("progress");

    const interval = setInterval(() => {
        progress += 10;
        bar.style.width = progress + "%";

        if (progress >= 100) {
            clearInterval(interval);
            loader.innerHTML = "✅ Scan Completed (Demo Mode)";
        }
    }, 300);
}

// ===== BLOG READ MORE TOGGLE =====
function toggleBlog(id) {
    const moreText = document.getElementById(id);

    if (moreText.style.display === "none") {
        moreText.style.display = "block";
    } else {
        moreText.style.display = "none";
    }
}

// ===== TOOL SEARCH FILTER =====
function searchTools() {
    const input = document.getElementById("toolSearch").value.toLowerCase();
    const cards = document.querySelectorAll(".card");

    cards.forEach(card => {
        const title = card.innerText.toLowerCase();
        if (title.includes(input)) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
}

// ===== CARD HOVER ANIMATION =====
const allCards = document.querySelectorAll(".card");
allCards.forEach(card => {
    card.addEventListener("mouseenter", () => {
        card.style.boxShadow = "0 10px 25px rgba(0,0,0,0.2)";
    });

    card.addEventListener("mouseleave", () => {
        card.style.boxShadow = "0 4px 12px rgba(0,0,0,0.08)";
    });
});
