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
        background: #f8f9fa;
        border-color: #d0d0d0;
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
        color: #666;
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
        background: #f8f9fa;
    }
</style>

<div class="language-switcher">
    <button class="current-lang" onclick="toggleLanguageMenu()">
        <img id="current-flag" src="{{ $currentLang['flag'] }}" alt="{{ $currentLang['code'] }}" class="flag-img">
        <span id="current-lang-name" class="lang-name">
            <span class="lang-code">{{ $currentLang['code'] }}</span>
            <span class="lang-full-name">{{ $currentLang['name'] }}</span>
        </span>
        <span class="dropdown-arrow">▼</span>
    </button>

    <div id="languageMenu" class="language-menu">
        <a href="#" onclick="changeLanguage('vi', 'https://flagcdn.com/w40/vn.png', 'VN'); return false;"
            class="lang-option">
            <img src="https://flagcdn.com/w40/vn.png" alt="VN" class="flag-img">
            <span class="lang-name">
                <span class="lang-code">VN</span>
                <span class="lang-full-name">Tiếng Việt</span>
            </span>
        </a>
        <a href="#" onclick="changeLanguage('de', 'https://flagcdn.com/w40/de.png', 'DE'); return false;"
            class="lang-option">
            <img src="https://flagcdn.com/w40/de.png" alt="DE" class="flag-img">
            <span class="lang-name">
                <span class="lang-code">DE</span>
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
        console.log('Translation state reset');
    }

    // Add function to clear session storage
    function clearTranslationCache() {
        sessionStorage.removeItem('lang_translated');
        console.log('Translation cache cleared');
    }

    function toggleLanguageMenu() {
        const menu = document.getElementById('languageMenu');
        const isHidden = menu.style.display === 'none' || !menu.style.display;
        menu.style.display = isHidden ? 'block' : 'none';
    }

    function changeLanguage(locale, flagUrl, code) {
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

        // Update UI first - Update flag image
        const flagImg = document.getElementById('current-flag');
        if (flagImg) {
            flagImg.src = flagUrl;
            flagImg.alt = code;
        }

        // Update language name display
        const langNames = {
            'vi': ['VN', 'Tiếng Việt'],
            'de': ['DE', 'Deutsch']
        };

        const currentLangName = document.getElementById('current-lang-name');
        if (currentLangName && langNames[locale]) {
            currentLangName.innerHTML = `
            <span class="lang-code">${langNames[locale][0]}</span>
            <span class="lang-full-name">${langNames[locale][1]}</span>
        `;
        }

        document.getElementById('languageMenu').style.display = 'none';

        // Map to Google Translate language codes
        const langMap = {
            'vi': 'vi',
            'de': 'de'
        };

        const targetLang = langMap[locale];

        // Check immediately if Google Translate is already loaded
        const select = document.querySelector('select.goog-te-combo');

        if (select && select.options && select.options.length > 0) {
            try {
                // Check if the target language option exists
                const targetOption = Array.from(select.options).find(option => option.value === targetLang);
                if (targetOption) {
                    select.value = targetLang;
                    select.dispatchEvent(new Event('change'));
                    isTranslating = false;
                    return;
                }
            } catch (e) {
                console.error('Error setting language:', e);
                isTranslating = false;
            }
        }

        // If not found, wait for it with shorter intervals
        let attempts = 0;
        const maxAttempts = 10;

        translateInterval = setInterval(function() {
            attempts++;

            const select = document.querySelector('select.goog-te-combo');

            if (select && select.options && select.options.length > 0) {
                try {
                    // Ensure the select element is fully loaded
                    if (select.options && select.options.length > 0) {
                        select.value = targetLang;
                        select.dispatchEvent(new Event('change'));
                        clearInterval(translateInterval);
                        translateInterval = null;
                        isTranslating = false;
                        return;
                    }
                } catch (e) {
                    console.error('Error setting language:', e);
                    clearInterval(translateInterval);
                    translateInterval = null;
                    isTranslating = false;
                }
            }

            if (attempts >= maxAttempts) {
                console.log('Google Translate not found');
                clearInterval(translateInterval);
                translateInterval = null;
                isTranslating = false;
            }
        }, 500);
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
            // Wait for Google Translate to load with better retry mechanism
            let attempts = 0;
            const maxAttempts = 20;

            const autoTranslateInterval = setInterval(function() {
                attempts++;
                const select = document.querySelector('select.goog-te-combo');

                if (select && select.options && select.options.length > 0) {
                    // Check if the target language option exists
                    const targetOption = Array.from(select.options).find(option => option.value ===
                        defaultLang);
                    if (targetOption) {
                        select.value = defaultLang;
                        select.dispatchEvent(new Event('change'));
                        sessionStorage.setItem('lang_translated', 'true');
                        console.log('Auto-translated to:', defaultLang);
                        clearInterval(autoTranslateInterval);
                    }
                }

                if (attempts >= maxAttempts) {
                    console.log('Auto-translate failed');
                    clearInterval(autoTranslateInterval);
                }
            }, 500);
        }
    })();
</script>
