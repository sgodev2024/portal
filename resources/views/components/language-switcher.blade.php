@php
    // Get default language from company settings or use 'vi' as fallback
    $defaultLang = isset($company) && $company->default_language ? $company->default_language : 'vi';

    // Define language data with flag images
    $langData = [
        'vi' => ['flag' => 'https://flagcdn.com/w40/vn.png', 'code' => 'VN', 'name' => 'Tiếng Việt'],
        'de' => ['flag' => 'https://flagcdn.com/w40/de.png', 'code' => 'DE', 'name' => 'Deutsch'],
    ];

    $currentLang = $langData[$defaultLang] ?? $langData['vi'];
@endphp

<style>
    .language-switcher {
        position: relative;
        display: inline-block;
    }

    .current-lang {
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
    }

    .current-lang:hover {
        /* Removed hover effect */
    }

    .flag-img {
        width: 24px;
        height: 18px;
        object-fit: cover;
        border-radius: 2px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .lang-name {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .lang-code {
        font-weight: 600;
    }

    .lang-full-name {
        display: none;
    }

    .dropdown-arrow {
        font-size: 9px;
        margin-left: auto;
        color: #888;
    }

    .language-menu {
        position: absolute;
        top: 100%;
        left: 0;
        margin-top: 4px;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 140px;
        z-index: 99999;
        display: none;
        overflow: hidden;
    }

    .lang-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        transition: background 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .lang-option:last-child {
        border-bottom: none;
    }

    .lang-option:hover {
        /* Removed hover effect */
    }
</style>

<div class="language-switcher notranslate">
    <button class="current-lang" onclick="toggleLanguageMenu()">
        <img id="current-flag" src="{{ $currentLang['flag'] }}" alt="{{ $currentLang['code'] }}" class="flag-img">
        <span id="current-lang-name" class="lang-name notranslate">
            <span class="lang-code notranslate">{{ $currentLang['code'] }}</span>
            <span class="lang-full-name">{{ $currentLang['name'] }}</span>
        </span>
        <span class="dropdown-arrow">▼</span>
    </button>

    <div id="languageMenu" class="language-menu notranslate">
        <a href="#" onclick="changeLanguage('vi', 'https://flagcdn.com/w40/vn.png', 'VN'); return false;"
            class="lang-option">
            <img src="https://flagcdn.com/w40/vn.png" alt="VN" class="flag-img">
            <span class="lang-name notranslate">
                <span class="lang-code notranslate">VN</span>
                <span class="lang-full-name">Tiếng Việt</span>
            </span>
        </a>
        <a href="#" onclick="changeLanguage('de', 'https://flagcdn.com/w40/de.png', 'DE'); return false;"
            class="lang-option">
            <img src="https://flagcdn.com/w40/de.png" alt="DE" class="flag-img">
            <span class="lang-name notranslate">
                <span class="lang-code notranslate">DE</span>
                <span class="lang-full-name">Deutsch</span>
            </span>
        </a>
    </div>
</div>

<script>
    // Global variables
    let translateInterval = null;
    let isTranslating = false;

    // Add function to reset translation state
    function resetTranslationState() {
        isTranslating = false;
        if (translateInterval) {
            clearInterval(translateInterval);
            translateInterval = null;
        }
    }

    // Add function to clear session storage
    function clearTranslationCache() {
        sessionStorage.removeItem('lang_translated');
    }

    function toggleLanguageMenu() {
        const menu = document.getElementById('languageMenu');
        const isHidden = menu.style.display === 'none' || !menu.style.display;
        menu.style.display = isHidden ? 'block' : 'none';
    }

    function changeLanguage(locale, flagUrl, code) {
        // Update UI first - Update flag image
        const flagImg = document.getElementById('current-flag');
        if (flagImg) {
            flagImg.src = flagUrl;
            flagImg.alt = code;
        }

        // Update language name display
        const langNames = {
            'vi': 'VN',
            'de': 'DE'
        };

        const currentLangName = document.getElementById('current-lang-name');
        if (currentLangName && langNames[locale]) {
            currentLangName.innerHTML = `
            <span class="lang-code notranslate">${langNames[locale]}</span>
        `;
        }

        document.getElementById('languageMenu').style.display = 'none';

        // If switching to Vietnamese, turn off Google Translate completely
        if (locale === 'vi') {
            // Clear storage
            localStorage.removeItem('selected_language');
            sessionStorage.removeItem('lang_translated');
            
            // Clear ALL Google Translate related cookies
            const cookiesToClear = ['googtrans', 'googtrans=/auto/vi', 'googtrans=/auto/de'];
            cookiesToClear.forEach(function(cookieName) {
                document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname + ";";
            });
            
            // Clear all cookies that start with googtrans
            document.cookie.split(";").forEach(function(c) {
                const cookieName = c.trim().split('=')[0];
                if (cookieName.includes('googtrans')) {
                    document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname + ";";
                }
            });
            
            // Always reload to ensure clean state
            window.location.reload();
            
            return;
        }
        
        // Save selected language to localStorage for non-Vietnamese languages
        localStorage.setItem('selected_language', locale);
        
        // Mark that translation is needed
        sessionStorage.setItem('lang_translated', 'true');

        // Prevent multiple simultaneous translations
        if (isTranslating) {
            // Reset the flag after a short delay to prevent permanent blocking
            setTimeout(() => {
                isTranslating = false;
            }, 2000);
            return;
        }

        isTranslating = true;

        // Clear any existing interval first
        if (translateInterval) {
            clearInterval(translateInterval);
            translateInterval = null;
        }

        // Use cookie-based translation (works with Google Translate widget)
        const targetLang = locale; // 'vi' or 'de'
        
        // Set the Google Translate cookie
        document.cookie = 'googtrans=/vi/' + targetLang + '; path=/';
        document.cookie = 'googtrans=/vi/' + targetLang + '; path=/; domain=' + window.location.hostname;
        
        // Reload the page to apply translation
        window.location.reload();
        
        isTranslating = false;
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const switcher = document.querySelector('.language-switcher');
        if (switcher && !switcher.contains(event.target)) {
            document.getElementById('languageMenu').style.display = 'none';
        }
    });
    
    // Update UI based on localStorage immediately on page load
    (function updateUIOnLoad() {
        const selectedLang = localStorage.getItem('selected_language');
        
        if (selectedLang && selectedLang !== 'vi') {
            const flagImg = document.getElementById('current-flag');
            const currentLangName = document.getElementById('current-lang-name');
            
            const langData = {
                'de': {
                    flag: 'https://flagcdn.com/w40/de.png',
                    code: 'DE'
                }
            };
            
            if (langData[selectedLang]) {
                if (flagImg) {
                    flagImg.src = langData[selectedLang].flag;
                    flagImg.alt = langData[selectedLang].code;
                }
                if (currentLangName) {
                    currentLangName.innerHTML = `<span class="lang-code notranslate">${langData[selectedLang].code}</span>`;
                }
            }
        }
    })();
    
    // Monitor Google Translate changes and update UI
    setInterval(function() {
        const select = document.querySelector('select.goog-te-combo');
        const selectedLang = localStorage.getItem('selected_language');
        
        if (select) {
            const currentLang = select.value;
            const flagImg = document.getElementById('current-flag');
            const currentLangName = document.getElementById('current-lang-name');
            
            // Update UI based on actual Google Translate state
            if (currentLang === 'de') {
                if (flagImg && flagImg.alt !== 'DE') {
                    flagImg.src = 'https://flagcdn.com/w40/de.png';
                    flagImg.alt = 'DE';
                }
                if (currentLangName && !currentLangName.textContent.includes('DE')) {
                    currentLangName.innerHTML = '<span class="lang-code notranslate">DE</span>';
                }
            } else if (currentLang === '' || currentLang === 'vi') {
                if (flagImg && flagImg.alt !== 'VN') {
                    flagImg.src = 'https://flagcdn.com/w40/vn.png';
                    flagImg.alt = 'VN';
                }
                if (currentLangName && !currentLangName.textContent.includes('VN')) {
                    currentLangName.innerHTML = '<span class="lang-code notranslate">VN</span>';
                }
            }
        }
    }, 500);

    // Auto-translate on page load
    (function() {
        // Check if user has selected a language before
        const selectedLang = localStorage.getItem('selected_language');
        const defaultLang = '{{ $defaultLang }}';
        
        // Use selected language if exists, otherwise use default
        const targetLang = selectedLang || defaultLang;
        
        // Don't translate if target is Vietnamese or no language selected
        if (!selectedLang || targetLang === 'vi') {
            // Force clear any Google Translate cookie that might exist
            const googtransCookie = document.cookie.split(';').find(c => c.trim().startsWith('googtrans'));
            if (googtransCookie) {
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname + ";";
            }
            
            return;
        }
        
        const hasTranslatedBefore = sessionStorage.getItem('lang_translated') === 'true';

        // Only auto-translate if not translated in this session
        if (!hasTranslatedBefore) {
            // Update UI to show selected language
            const langData = {
                'de': {
                    flag: 'https://flagcdn.com/w40/de.png',
                    code: 'DE'
                }
            };
            
            if (langData[targetLang]) {
                const flagImg = document.getElementById('current-flag');
                const currentLangName = document.getElementById('current-lang-name');
                
                if (flagImg) {
                    flagImg.src = langData[targetLang].flag;
                    flagImg.alt = langData[targetLang].code;
                }
                
                if (currentLangName) {
                    currentLangName.innerHTML = `<span class="lang-code notranslate">${langData[targetLang].code}</span>`;
                }
            }
            
            // Use cookie-based auto-translation
            // Set the Google Translate cookie
            document.cookie = 'googtrans=/vi/' + targetLang + '; path=/';
            document.cookie = 'googtrans=/vi/' + targetLang + '; path=/; domain=' + window.location.hostname;
            
            sessionStorage.setItem('lang_translated', 'true');
            
            // Wait a bit for Google Translate to detect the cookie
            setTimeout(function() {
                window.location.reload();
            }, 100);
        }
    })();
</script>
