CKEDITOR.replace('content_noidung', {
    toolbar: [{
        name: 'document',
        items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates']
    },
    {
        name: 'clipboard',
        items: ['Undo', 'Redo']
    },
    {
        name: 'editing',
        items: ['Find', 'Replace', '-', 'SelectAll', '-', 'SpellChecker', 'Scayt']
    },
    {
        name: 'forms',
        items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button',
            'ImageButton', 'HiddenField'
        ]
    },
        '/',
    {
        name: 'basicstyles',
        items: ['Bold', 'Italic', 'Underline', '-', 'Subscript', 'Superscript', '-', 'Strike',
            'RemoveFormat'
        ]
    },
    {
        name: 'paragraph',
        items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote',
            'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock',
            '-', 'BidiLtr', 'BidiRtl', 'Language'
        ]
    },
    {
        name: 'links',
        items: ['Link', 'Unlink', 'Anchor']
    },
    {
        name: 'insert',
        items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak',
            'Iframe'
        ]
    },
        '/',
    {
        name: 'styles',
        items: ['Styles', 'Format', 'Font', 'FontSize']
    },
    {
        name: 'colors',
        items: ['TextColor', 'BGColor']
    },
    {
        name: 'tools',
        items: ['Maximize', 'ShowBlocks', '-']
    },
    {
        name: 'about',
        items: ['About']
    }
    ],
    extraPlugins: 'font,colorbutton,justify',
    fontSize_sizes: '11px;12px;13px;14px;15px;16px;18px;20px;22px;24px;26px;28px;30px;32px;34px;36px',
});


function openModal(id) {
    CKEDITOR.instances['content_noidung'].setData('');
    document.getElementById('contentModalLabel').innerText = 'Nội dung';
    document.getElementById('contentModalLabel').setAttribute('data-id', id);
    fetch(`/service/getContent/${id}`)
        .then(response => response.json())
        .then(data => {
            CKEDITOR.instances['content_noidung'].setData(data.content);
        })
        .catch(error => {
            console.error('Error fetching content:', error);
        });

    // Mở modal bằng Bootstrap 5
    var myModal = new bootstrap.Modal(document.getElementById('contentModal'), {});
    myModal.show();
}

function openModalStatus(id) {

    fetch(`/service/status/${id}`)
        .then(response => response.json())
        .then(data => {
            const toggleCheckbox = document.getElementById('toggleStatus');
            toggleCheckbox.setAttribute('data-id', id);
            if (data.status === 'active') {
                toggleCheckbox.checked = true;
            } else {
                toggleCheckbox.checked = false;
            }
        })
        .catch(error => {
            console.error('Error fetching content:', error);
        });
    var myModal = new bootstrap.Modal(document.getElementById('statusModal'), {});
    myModal.show();
}

// Lắng nghe sự kiện khi bấm nút Lưu
document.getElementById('saveButton').addEventListener('click', function () {
    // Lấy nội dung từ CKEditor
    var content = CKEDITOR.instances['content_noidung'].getData();
    var id = document.getElementById('contentModalLabel').getAttribute('data-id');
    // Kiểm tra nội dung trước khi gửi
    // Gửi dữ liệu lên server qua AJAX (Sử dụng fetch hoặc XMLHttpRequest)
    fetch('/service/saveContent', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                'content') // Nếu sử dụng Laravel, thêm CSRF token
        },
        body: JSON.stringify({
            content: content,
            id: id
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var myModal = bootstrap.Modal.getInstance(document.getElementById('contentModal'));
                myModal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Lưu thành công!',
                    text: 'Nội dung đã được lưu thành công.',
                    confirmButtonText: 'OK'
                });
            } else {
                console.error('Lỗi khi lưu nội dung');
            }
        })
        .catch(error => {
            console.error('Error saving content:', error);
        });
});

document.addEventListener('DOMContentLoaded', function () {
    $(document).on('click', '.btn-transfer', function () {
        var id = $(this).data('id');
        var hosting = $(this).data('hosting');
        var email = $(this).data('email');
        $('#data-hosting').val(hosting);
        $('#data-id').val(id);
        $('#data-email').val(email);
        $('#transferModal').modal('show');
    });

    $(document).on('click', '.close-modal, .close', function () {
        const modal = $('#transferModal');
        if (modal.length) {
            modal.modal('hide');
        }
    });

});

function toggleMenu(id) {
    var menu = document.getElementById('menu-' + id);
    var isMenuVisible = menu.style.display === 'block';
    menu.style.display = isMenuVisible ? 'none' : 'block';
}

document.addEventListener('click', function (event) {

    var dropdownMenus = document.querySelectorAll('.dropdown-menu');
    var dropdownToggles = document.querySelectorAll('.action');

    dropdownMenus.forEach(function (menu) {
        if (!menu.contains(event.target) && !event.target.closest('.action')) {
            menu.style.display = 'none';
        }
    });
});
