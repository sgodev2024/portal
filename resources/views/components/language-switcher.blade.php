@php
    // Get default language from company settings or use 'vi' as fallback
    $defaultLang = isset($company) && $company->default_language ? $company->default_language : 'vi';
    
    // Define language data
    $langData = [
        'vi' => ['flag' => '🇻🇳', 'code' => 'VI', 'name' => 'Tiếng Việt'],
        'de' => ['flag' => '🇩🇪', 'code' => 'DE', 'name' => 'Deutsch'],
    ];
    
    $currentLang = $langData[$defaultLang] ?? $langData['vi'];
@endphp

<div class="language-switcher" style="position: relative; display: inline-block;">
    <button class="current-lang" onclick="toggleLanguageMenu()" style="
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 10px 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
        min-width: 140px;
        color: #333;
        transition: all 0.2s ease;
    ">
        <span id="current-flag">{{ $currentLang['flag'] }}</span>
        <span id="current-lang-name" style="display: flex; gap: 5px;">
            <span style="font-weight: 600;">{{ $currentLang['code'] }}</span>
            <span style="color: #666;">{{ $currentLang['name'] }}</span>
        </span>
        <span style="font-size: 9px; margin-left: auto; color: #888;">▼</span>
    </button>
    
    <div id="languageMenu" class="language-menu" style="
        position: absolute;
        top: 100%;
        left: 0;
        margin-top: 4px;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-width: 140px;
        z-index: 99999;
        display: none;
    ">
        <a href="#" onclick="changeLanguage('vi', '🇻🇳', 'VI'); return false;" class="lang-option" style="
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s ease;
        " onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
            <span style="font-size: 18px;">🇻🇳</span>
            <span style="display: flex; gap: 5px;">
                <span style="font-weight: 600;">VI</span>
                <span style="color: #666;">Tiếng Việt</span>
            </span>
        </a>
        <a href="#" onclick="changeLanguage('de', '🇩🇪', 'DE'); return false;" class="lang-option" style="
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: background 0.2s ease;
        " onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
            <span style="font-size: 18px;">🇩🇪</span>
            <span style="display: flex; gap: 5px;">
                <span style="font-weight: 600;">DE</span>
                <span style="color: #666;">Deutsch</span>
            </span>
        </a>
    </div>
</div>

<script>
// Global variables
let translateInterval = null;
let isTranslating = false;

function toggleLanguageMenu() {
    const menu = document.getElementById('languageMenu');
    const isHidden = menu.style.display === 'none' || !menu.style.display;
    menu.style.display = isHidden ? 'block' : 'none';
}

function changeLanguage(locale, flag, code) {
    // Prevent multiple simultaneous translations
    if (isTranslating) {
        console.log('Translation already in progress, please wait...');
        return;
    }
    
    isTranslating = true;
    
    // Clear any existing interval first
    if (translateInterval) {
        clearInterval(translateInterval);
        translateInterval = null;
    }
    
    // Update UI first
    document.getElementById('current-flag').textContent = flag;
    
    // Update language name display
    const langNames = {
        'vi': ['VI', 'Tiếng Việt'],
        'de': ['DE', 'Deutsch']
    };
    
    const currentLangName = document.getElementById('current-lang-name');
    if (currentLangName && langNames[locale]) {
        currentLangName.innerHTML = `
            <span style="font-weight: 600;">${langNames[locale][0]}</span>
            <span style="color: #666;">${langNames[locale][1]}</span>
        `;
    }
    
    document.getElementById('languageMenu').style.display = 'none';
    
    // Map to Google Translate language codes
    const langMap = {
        'vi': 'vi',
        'de': 'de'
    };
    
    const targetLang = langMap[locale];
    console.log('Changing language to:', targetLang);
    
    // Check immediately if Google Translate is already loaded
    const select = document.querySelector('select.goog-te-combo');
    
    if (select) {
        console.log('Found Google Translate select immediately, setting language to:', targetLang);
        try {
            select.value = targetLang;
            select.dispatchEvent(new Event('change'));
            isTranslating = false;
            return;
        } catch(e) {
            console.error('Error setting language:', e);
            isTranslating = false;
        }
    }
    
    // If not found, wait for it with shorter intervals
    let attempts = 0;
    const maxAttempts = 5; // Reduced from 10 to 5
    
    translateInterval = setInterval(function() {
        attempts++;
        
        const select = document.querySelector('select.goog-te-combo');
        
        if (select) {
            console.log('Found Google Translate select after', attempts, 'attempts');
            try {
                select.value = targetLang;
                select.dispatchEvent(new Event('change'));
                clearInterval(translateInterval);
                translateInterval = null;
                isTranslating = false;
                return;
            } catch(e) {
                console.error('Error setting language:', e);
                clearInterval(translateInterval);
                translateInterval = null;
                isTranslating = false;
            }
        }
        
        if (attempts >= maxAttempts) {
            console.log('Google Translate not found after', maxAttempts, 'attempts');
            clearInterval(translateInterval);
            translateInterval = null;
            isTranslating = false;
        }
    }, 200); // Slightly increased interval for better performance
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const switcher = document.querySelector('.language-switcher');
    if (switcher && !switcher.contains(event.target)) {
        document.getElementById('languageMenu').style.display = 'none';
    }
});

// Auto-translate on page load if default language is not Vietnamese
(function() {
    const defaultLang = '{{ $defaultLang }}';
    const hasTranslatedBefore = sessionStorage.getItem('lang_translated') === 'true';
    
    // Only auto-translate if default language is not 'vi' and not translated before
    if (defaultLang !== 'vi' && !hasTranslatedBefore) {
        // Wait for Google Translate to load
        setTimeout(function() {
            const select = document.querySelector('select.goog-te-combo');
            if (select) {
                select.value = defaultLang;
                select.dispatchEvent(new Event('change'));
                sessionStorage.setItem('lang_translated', 'true');
                console.log('Auto-translated to default language:', defaultLang);
            }
        }, 2000);
    }
})();
</script>
