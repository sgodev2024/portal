function applyPriceFormatter(selector) {
    const priceInputs = document.querySelectorAll(selector);

    if (!priceInputs.length) return;

    priceInputs.forEach(function (priceInput) {
        // Format giá trị khi load trang (nếu có)
        if (priceInput.value) {
            priceInput.value = formatNumber(priceInput.value);
        }

        // Ngăn nhập chữ
        priceInput.addEventListener('keydown', function (e) {
            // Cho phép các phím điều hướng (Arrow, Backspace, Tab, Delete…)
            const allowedKeys = [
                'Backspace', 'Tab', 'Delete', 'ArrowLeft', 'ArrowRight',
                'Home', 'End'
            ];
            // Cho phép tổ hợp Ctrl+A, Ctrl+C, Ctrl+V
            if (
                allowedKeys.includes(e.key) ||
                (e.ctrlKey && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase()))
            ) {
                return;
            }

            // Chặn phím nếu không phải số
            if (!/^\d$/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Khi người dùng nhập
        priceInput.addEventListener('input', function () {
            const unformatted = priceInput.value.replace(/[^\d]/g, '');
            priceInput.value = formatNumber(unformatted);
        });

        // Trước khi submit form, bỏ dấu phân cách
        priceInput.form.addEventListener('submit', function () {
            const unformatted = priceInput.value.replace(/[^\d]/g, '');
            priceInput.value = unformatted;
        });
    });
}

// Hàm formatNumber() giữ nguyên:
function formatNumber(value) {
    return value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
