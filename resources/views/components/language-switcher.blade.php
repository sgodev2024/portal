<div class="language-switcher" style="position: relative; display: inline-block;">
    <button class="current-lang" onclick="toggleLanguageMenu()" style="
        background: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 8px 15px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: bold;
        min-width: 100px;
    ">
        <span id="current-flag">🇻🇳</span>
        <span id="current-lang-code">VI</span>
        <span style="font-size: 10px;">▼</span>
    </button>
    
    <div id="languageMenu" class="language-menu" style="
        position: absolute;
        top: 100%;
        left: 0;
        margin-top: 4px;
        background: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-width: 100px;
        z-index: 99999;
        display: none;
    ">
        <a href="#" onclick="changeLanguage('vi', '🇻🇳', 'VI'); return false;" class="lang-option" style="
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            border-bottom: 1px solid #eee;
        ">
            <span style="font-size: 20px;">🇻🇳</span>
            <span>VI</span>
        </a>
        <a href="#" onclick="changeLanguage('de', '🇩🇪', 'DE'); return false;" class="lang-option" style="
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        ">
            <span style="font-size: 20px;">🇩🇪</span>
            <span>DE</span>
        </a>
    </div>
</div>

<script>
function toggleLanguageMenu() {
    const menu = document.getElementById('languageMenu');
    const isHidden = menu.style.display === 'none' || !menu.style.display;
    menu.style.display = isHidden ? 'block' : 'none';
}

function changeLanguage(locale, flag, code) {
    // Update UI first
    document.getElementById('current-flag').textContent = flag;
    document.getElementById('current-lang-code').textContent = code;
    document.getElementById('languageMenu').style.display = 'none';
    
    // Map to Google Translate language codes
    const langMap = {
        'vi': 'vi',
        'de': 'de'
    };
    
    const targetLang = langMap[locale];
    console.log('Changing language to:', targetLang);
    
    // Try to use Google Translate select element
    let attempts = 0;
    const maxAttempts = 15;
    
    const tryTranslate = setInterval(function() {
        attempts++;
        
        // Check for the Google Translate select element
        const select = document.querySelector('select.goog-te-combo');
        
        if (select) {
            console.log('Found Google Translate select, setting language to:', targetLang);
            select.value = targetLang;
            select.dispatchEvent(new Event('change'));
            
            // Also try trigger event
            if (select.onchange) {
                select.onchange();
            }
            
            clearInterval(tryTranslate);
            return;
        }
        
        if (attempts >= maxAttempts) {
            console.log('Google Translate select not found after', maxAttempts, 'attempts');
            console.log('Reloading page with language parameter');
            const url = new URL(window.location.href);
            url.searchParams.set('hl', targetLang);
            window.location.href = url.toString();
            clearInterval(tryTranslate);
        }
    }, 300);
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const switcher = document.querySelector('.language-switcher');
    if (switcher && !switcher.contains(event.target)) {
        document.getElementById('languageMenu').style.display = 'none';
    }
});
</script>
