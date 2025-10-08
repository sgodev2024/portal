$(document).ready(function () {
    $.fn.modal.Constructor.prototype._enforceFocus = function () {};
    $("#username").select2({
        dropdownParent: $("#transferModal"),
        placeholder: "Chọn một mục",
        width: "100%",
        allowClear: true,
        minimumResultsForSearch: 0,
    });

    let token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    $(document).on("change", ".toggleStatus", function () {
        let isChecked = $(this).is(":checked");
        let newStatus = isChecked ? "active" : "inactive";
        const id = this.getAttribute("data-id");
        let token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        $.ajax({
            url: `/service/update-status/${id}`,
            type: "POST",
            data: {
                status: newStatus,
                id: id,
                _token: token,
            },
            success: function (response) {
                console.log("Trạng thái đã được cập nhật:", response);

                Swal.fire({
                    icon: "success",
                    title: "Thành công!",
                    text: response.message || "Cập nhật trạng thái thành công.",
                });

                $("#categoryTable").DataTable().ajax.reload(null, false);
                $("#statusModal").modal("hide");
            },
            error: function (xhr, status, error) {
                console.error("Cập nhật trạng thái thất bại:", error);
                $(this).prop("checked", !isChecked);
                $("#categoryTable").DataTable().ajax.reload(null, false);
                Swal.fire({
                    icon: "error",
                    title: "Lỗi!",
                    text:
                        xhr.responseJSON?.message ||
                        "Không thể cập nhật trạng thái.",
                });
            },
        });
    });

    $("#transferForm").on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data: $(this).serialize(),
            success: function (response) {
                Swal.fire({
                    icon: "success",
                    title: "Thành công!",
                    text: "Chuyển thông tin thành công!",
                    confirmButtonText: "OK",
                }).then(() => {
                    $("#categoryTable").DataTable().ajax.reload(null, false);
                    $("#transferModal").modal("hide");
                });
            },
            error: function (xhr) {
                let errorMessage = "Có lỗi xảy ra. Vui lòng thử lại!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: "error",
                    title: "Lỗi!",
                    text: errorMessage,
                    confirmButtonText: "OK",
                });
            },
        });
    });

    $("#extendTime").on("change", function () {
        const monthsToAdd = parseInt($(this).val());
        const enddate = this.getAttribute("data-enddate");
        if (!monthsToAdd || !enddate) {
            $("#newEndDate").val("");
            return;
        }
        const [year, month, day] = enddate.split("-").map(Number);
        const newDate = new Date(year, month - 1, day);
        newDate.setMonth(newDate.getMonth() + monthsToAdd);

        const yyyy = newDate.getFullYear();
        const mm = String(newDate.getMonth() + 1).padStart(2, "0");
        const dd = String(newDate.getDate()).padStart(2, "0");

        const formattedDate = `${yyyy}-${mm}-${dd}`;
        $("#newEndDate").val(formattedDate);
    });

    $("#giaHanForm").on("submit", function (e) {
        e.preventDefault();

        const serviceId = $("#service_id").val();
        const extendTime = $("#extendTime").val();

        if (!serviceId || !extendTime) {
            alert("Vui lòng điền đầy đủ thông tin");
            return;
        }

        $.ajax({
            url: `/service/giahan`,
            method: "POST",
            data: {
                service_id: serviceId,
                extend_time: extendTime,
                _token: token,
            },
            success: function (response) {
                console.log("Gia hạn thành công:", response);
                $("#categoryTable").DataTable().ajax.reload(null, false);
                $("#giaHanModal").modal("hide");
            },
            error: function (xhr, status, error) {
                console.error("Lỗi khi gia hạn:", error);
                alert("Có lỗi xảy ra. Vui lòng thử lại!");
            },
        });
    });

    $("#resetPassword").on("submit", function (e) {
        e.preventDefault();

        $("#error-password").text("");
        $("#error-confirm").text("");
        const userId = $("#reset_user_id").val();
        const password = $("#new_password").val().trim();
        const confirm = $("#confirm_password").val().trim();
        let hasError = false;

        if (password === "") {
            $("#error-password").text("Vui lòng nhập mật khẩu mới");
            hasError = true;
        }

        if (confirm === "") {
            $("#error-confirm").text("Vui lòng xác nhận mật khẩu");
            hasError = true;
        } else if (password !== "" && password !== confirm) {
            $("#error-confirm").text("Mật khẩu xác nhận không khớp");
            hasError = true;
        }

        if (hasError) return;

        $.ajax({
            url: "/password/reset",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"), // csrf token
                id: userId,
                password: password,
                password_confirmation: confirm,
            },
            success: function (response) {
                if (response.success) {
                    $("#modalResetPassword").modal("hide");
                    $("#resetPassword")[0].reset();
                    $("#categoryTable").DataTable().ajax.reload(null, false);
                } else {
                    alert(response.message || "Đã xảy ra lỗi");
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.password) {
                        $("#error-password").text(errors.password[0]);
                    }
                    if (errors.password_confirmation) {
                        $("#error-confirm").text(
                            errors.password_confirmation[0]
                        );
                    }
                } else {
                    alert("Lỗi không xác định");
                }
            },
        });
    });

    $("#editForm").on("submit", function (e) {
        e.preventDefault();

        const serviceId = $("#service_edit_id").val();
        const startDate_edit = $("#startDate_edit").val();
        const price_edit = $("#price_edit").val();
        if (!serviceId || !extendTime) {
            alert("Vui lòng điền đầy đủ thông tin");
            return;
        }

        $.ajax({
            url: `/service/edit`,
            method: "POST",
            data: {
                service_id: serviceId,
                startDate_edit: startDate_edit,
                price_edit: price_edit,
                _token: token,
            },
            success: function (response) {
                console.log("Cập nhật thành công:", response);
                $("#categoryTable").DataTable().ajax.reload(null, false);
                $("#editModal").modal("hide");
            },
            error: function (xhr, status, error) {
                console.error("Lỗi khi gia hạn:", error);
                alert("Có lỗi xảy ra. Vui lòng thử lại!");
            },
        });
    });

    $("#checkboxAll").on("change", function () {
        $(".checkbox-item").prop("checked", this.checked);
        toggleDeleteButton();
    });

    $(document).on("change", ".checkbox-item", function () {
        $("#checkboxAll").prop(
            "checked",
            $(".checkbox-item:checked").length === $(".checkbox-item").length
        );
        toggleDeleteButton();
    });

    $(document).on("click", ".toggle-password", function () {
        const input = $($(this).attr("toggle"));
        const type = input.attr("type") === "password" ? "text" : "password";
        input.attr("type", type);

        // Đổi icon
        $(this).toggleClass("fa-eye fa-eye-slash");
    });

    function toggleDeleteButton() {
        if ($(".checkbox-item:checked").length > 0) {
            if ($(".btn-delete").length === 0) {
                $(
                    '<button class="btn-delete btn-danger">Xóa</button>'
                ).insertAfter(".dt-length");
                $(".btn-delete").on("click", function () {
                    Swal.fire({
                        title: "Bạn có chắc muốn xóa?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Có, xóa ngay!",
                        cancelButtonText: "Hủy",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(".checkbox-item:checked").closest("tr").remove();
                            $(".btn-delete").remove();
                            $("#checkboxAll").prop("checked", false);
                            Swal.fire(
                                "Đã xóa!",
                                "Các mục đã được xóa.",
                                "success"
                            );
                        }
                    });
                });
            }
        } else {
            $(".btn-delete").remove();
        }
    }
});
CKEDITOR.replace("content_noidung", {
    toolbar: [
        {
            name: "document",
            items: [
                "Source",
                "-",
                "Save",
                "NewPage",
                "Preview",
                "Print",
                "-",
                "Templates",
            ],
        },
        {
            name: "clipboard",
            items: ["Undo", "Redo"],
        },
        {
            name: "editing",
            items: [
                "Find",
                "Replace",
                "-",
                "SelectAll",
                "-",
                "SpellChecker",
                "Scayt",
            ],
        },
        {
            name: "forms",
            items: [
                "Form",
                "Checkbox",
                "Radio",
                "TextField",
                "Textarea",
                "Select",
                "Button",
                "ImageButton",
                "HiddenField",
            ],
        },
        "/",
        {
            name: "basicstyles",
            items: [
                "Bold",
                "Italic",
                "Underline",
                "-",
                "Subscript",
                "Superscript",
                "-",
                "Strike",
                "RemoveFormat",
            ],
        },
        {
            name: "paragraph",
            items: [
                "NumberedList",
                "BulletedList",
                "-",
                "Outdent",
                "Indent",
                "-",
                "Blockquote",
                "CreateDiv",
                "-",
                "JustifyLeft",
                "JustifyCenter",
                "JustifyRight",
                "JustifyBlock",
                "-",
                "BidiLtr",
                "BidiRtl",
                "Language",
            ],
        },
        {
            name: "links",
            items: ["Link", "Unlink", "Anchor"],
        },
        {
            name: "insert",
            items: [
                "Image",
                "Flash",
                "Table",
                "HorizontalRule",
                "Smiley",
                "SpecialChar",
                "PageBreak",
                "Iframe",
            ],
        },
        "/",
        {
            name: "styles",
            items: ["Styles", "Format", "Font", "FontSize"],
        },
        {
            name: "colors",
            items: ["TextColor", "BGColor"],
        },
        {
            name: "tools",
            items: ["Maximize", "ShowBlocks", "-"],
        },
        {
            name: "about",
            items: ["About"],
        },
    ],
    extraPlugins: "font,colorbutton,justify",
    fontSize_sizes:
        "11px;12px;13px;14px;15px;16px;18px;20px;22px;24px;26px;28px;30px;32px;34px;36px",
});
function openModal(id) {
    CKEDITOR.instances["content_noidung"].setData("");
    document.getElementById("contentModalLabel").innerText = "Nội dung";
    document.getElementById("contentModalLabel").setAttribute("data-id", id);
    fetch(`/service/getContent/${id}`)
        .then((response) => response.json())
        .then((data) => {
            CKEDITOR.instances["content_noidung"].setData(data.content);
        })
        .catch((error) => {
            console.error("Error fetching content:", error);
        });

    // Mở modal bằng Bootstrap 5
    var myModal = new bootstrap.Modal(
        document.getElementById("contentModal"),
        {}
    );
    myModal.show();
}

function openModalStatus(id) {
    fetch(`/service/status/${id}`)
        .then((response) => response.json())
        .then((data) => {
            const toggleCheckbox = document.getElementById("toggleStatus");
            toggleCheckbox.setAttribute("data-id", id);
            if (data.status === "active") {
                toggleCheckbox.checked = true;
            } else {
                toggleCheckbox.checked = false;
            }
        })
        .catch((error) => {
            console.error("Error fetching content:", error);
        });
    var myModal = new bootstrap.Modal(
        document.getElementById("statusModal"),
        {}
    );
    myModal.show();
}

function openModalGiaHan(id) {
    fetch(`/service/giahan/${id}`)
        .then((response) => response.json())
        .then((data) => {
            console.log(data);
            const startDate = document.getElementById("startDate");
            const endDate = document.getElementById("endDate");

            startDate.value = data.activeAt;
            endDate.value = data.expirationDate;

            const extendTime = document.getElementById("extendTime");
            extendTime.setAttribute("data-endDate", data.expirationDate);

            const service_id = document.getElementById("service_id");
            service_id.value = id;
        })
        .catch((error) => {
            console.error("Error fetching content:", error);
        });
    var myModal = new bootstrap.Modal(
        document.getElementById("giaHanModal"),
        {}
    );
    myModal.show();
}
//

function openModalEdit(id) {
    fetch(`/service/edit/${id}`)
        .then((response) => response.json())
        .then((data) => {
            console.log(data);
            const startDate = document.getElementById("startDate_edit");
            const endDate = document.getElementById("endDate_edit");
            const price = document.getElementById("price_edit");
            startDate.value = data.activeAt;
            endDate.value = data.number;
            price.value = data.price;
            const extendTime = document.getElementById("extendTime");
            extendTime.setAttribute("data-endDate", data.expirationDate);

            const service_id = document.getElementById("service_edit_id");
            service_id.value = id;
        })
        .catch((error) => {
            console.error("Error fetching content:", error);
        });
    var myModal = new bootstrap.Modal(document.getElementById("editModal"), {});
    myModal.show();
}

function openModalPass(id) {
    var myModal = new bootstrap.Modal(
        document.getElementById("modalResetPassword"),
        {}
    );

    $("#error-password").text("");
    $("#error-confirm").text("");
    $("#new_password").text("");
    $("#confirm_password").text("");
    const user_id = document.getElementById("reset_user_id");
    user_id.value = id;
    myModal.show();
}

function confirmDeleteSweet(id) {
    let token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    Swal.fire({
        title: "Bạn có chắc muốn xóa?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Có, xóa đi!",
        cancelButtonText: "Hủy",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/service/delete/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": token,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        console.log(data);
                        Swal.fire("Đã xóa!", data.message, "success");
                        $("#categoryTable")
                            .DataTable()
                            .ajax.reload(null, false);
                    } else {
                        Swal.fire("Lỗi!", data.message, "error");
                    }
                })
                .catch(() => {
                    Swal.fire("Lỗi!", "Không thể xóa mục.", "error");
                });
        }
    });
}

document.getElementById("saveButton").addEventListener("click", function () {
    var content = CKEDITOR.instances["content_noidung"].getData();
    var id = document
        .getElementById("contentModalLabel")
        .getAttribute("data-id");

    fetch("/service/saveContent", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({
            content: content,
            id: id,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                var myModal = bootstrap.Modal.getInstance(
                    document.getElementById("contentModal")
                );
                myModal.hide();
                Swal.fire({
                    icon: "success",
                    title: "Lưu thành công!",
                    text: "Nội dung đã được lưu thành công.",
                    confirmButtonText: "OK",
                });
            } else {
                console.error("Lỗi khi lưu nội dung");
            }
        })
        .catch((error) => {
            console.error("Error saving content:", error);
        });
});

document.addEventListener("DOMContentLoaded", function () {
    $(document).on("click", ".btn-transfer", function () {
        var id = $(this).data("id");
        var hosting = $(this).data("hosting");
        var email = $(this).data("email");
        $("#data-hosting").val(hosting);
        $("#data-id").val(id);
        $("#data-email").val(email);
        $("#transferModal").modal("show");
    });

    $(document).on("click", ".close-modal, .close", function () {
        const modal = $("#transferModal");
        if (modal.length) {
            modal.modal("hide");
        }
    });
});

function toggleMenu(id) {
    var menu = document.getElementById("menu-" + id);

    document.querySelectorAll(".dropdown-menu-service").forEach(function (m) {
        if (m !== menu) {
            m.style.display = "none";
        }
    });

    menu.style.display = menu.style.display === "block" ? "none" : "block";
}

document.addEventListener("click", function (event) {
    const isToggleButton = event.target.closest(".action");

    if (isToggleButton) return;

    document.querySelectorAll(".dropdown-menu-service").forEach(function (menu) {
        menu.style.display = "none";
    });
});
