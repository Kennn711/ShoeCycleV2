$(document).ready(function () {
    // Setup CSRF
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Global State
    let isFormValid = false;
    let editOriginalData = {}; // State untuk menyimpan data awal saat Edit

    // ==========================================
    // 1. GENERIC MODAL LOGIC (FIX)
    // ==========================================

    // Helper untuk menutup modal apa saja
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.close();
            // Reset form setelah animasi tutup (100ms)
            setTimeout(() => {
                if (modalId === "modal_create_driver") resetFormCreate();
                if (modalId === "modal_edit_driver") resetFormEdit();
            }, 100);
        }
    }

    // Handle Klik Backdrop untuk menutup modal
    $(document).on("click", ".modal-backdrop", function (e) {
        // Cek modal mana yang sedang terbuka
        const modals = [
            "modal_create_driver",
            "modal_edit_driver",
            "modal_hapus_driver",
        ];
        modals.forEach((id) => {
            const m = document.getElementById(id);
            if (m && m.open) {
                e.preventDefault();
                closeModal(id);
            }
        });
    });

    // ==========================================
    // 2. CREATE DRIVER LOGIC
    // ==========================================

    window.openCreateDriverModal = function () {
        resetFormCreate();
        document.getElementById("modal_create_driver").showModal();
    };

    window.closeCreateDriverModal = function () {
        closeModal("modal_create_driver");
    };

    function resetFormCreate() {
        $("#form-create-driver")[0].reset();
        $("#create-global-error").addClass("hidden");
        $('[id^="error-"]').addClass("hidden").text("");
        $(".input-error").removeClass("input-error");

        // Reset Image Preview (PENTING: Show Icon, Hide Image)
        $("#img-preview").attr("src", "").addClass("hidden");
        $("#icon-camera").removeClass("hidden");

        isFormValid = false;
        updateSubmitButton();
    }

    // Validasi Create
    function validateFormCreate() {
        const name = $("#input-name").val().trim();
        const email = $("#input-email").val().trim();
        const password = $("#input-password").val();
        const fileInput = document.getElementById("input-foto");

        let isValid = true;

        // Nama
        if (name.length < 3)
            isValid =
                toggleError("#input-name", "#error-name", "Min 3 karakter") &&
                isValid;
        else toggleError("#input-name", "#error-name", null);

        // Email
        if (!validateEmail(email))
            isValid =
                toggleError(
                    "#input-email",
                    "#error-email",
                    "Email tidak valid"
                ) && isValid;
        else toggleError("#input-email", "#error-email", null);

        // Password
        if (password.length < 6)
            isValid =
                toggleError(
                    "#input-password",
                    "#error-password",
                    "Min 6 karakter"
                ) && isValid;
        else toggleError("#input-password", "#error-password", null);

        // Gambar
        if (fileInput.files.length === 0) isValid = false;
        else $("#error-profile_picture").addClass("hidden");

        isFormValid = isValid;
        updateSubmitButton();
    }

    function updateSubmitButton() {
        const btn = $("#btn-submit-create");
        const hasFile = document.getElementById("input-foto").files.length > 0;
        if (isFormValid && hasFile)
            btn.prop("disabled", false).removeClass(
                "opacity-50 cursor-not-allowed"
            );
        else
            btn.prop("disabled", true).addClass(
                "opacity-50 cursor-not-allowed"
            );
    }

    // Listeners Create
    $("#input-name, #input-email, #input-password").on(
        "input blur",
        validateFormCreate
    );
    $("#input-foto").on("change", function (e) {
        handleImagePreview(this, "#img-preview", "#icon-camera");
        validateFormCreate();
    });

    // AJAX Create
    $("#form-create-driver").on("submit", function (e) {
        e.preventDefault();
        if (document.getElementById("input-foto").files.length === 0) {
            $("#error-profile_picture")
                .text("Wajib upload foto")
                .removeClass("hidden");
            return;
        }
        submitForm(
            $(this),
            "#btn-submit-create",
            "#btn-loading",
            "modal_create_driver"
        );
    });

    // ==========================================
    // 3. EDIT DRIVER LOGIC (SUDAH DITAMBAHKAN)
    // ==========================================

    window.openEditDriverModal = function (id) {
        resetFormEdit();
        // Fetch Data
        $.ajax({
            url: `/driver/${id}`,
            method: "GET",
            success: function (response) {
                const data = response.data;
                const photoUrl = data.profile_picture
                    ? `/storage/${data.profile_picture}`
                    : "";

                // Populate Form
                $("#edit-id").val(data.id);
                $("#edit-input-name").val(data.name);
                $("#edit-input-email").val(data.email);
                $("#edit-input-password").val(""); // Reset password field

                // Show Existing Image
                if (photoUrl) {
                    $("#edit-img-preview")
                        .attr("src", photoUrl)
                        .removeClass("hidden");
                } else {
                    $("#edit-img-preview").addClass("hidden");
                }

                // Simpan state awal untuk perbandingan (Dirty Checking)
                editOriginalData = {
                    name: data.name,
                    email: data.email,
                    photoUrl: photoUrl,
                };

                document.getElementById("modal_edit_driver").showModal();
            },
            error: function () {
                alert("Gagal mengambil data.");
            },
        });
    };

    window.closeEditDriverModal = function () {
        closeModal("modal_edit_driver");
    };

    function resetFormEdit() {
        $("#form-edit-driver")[0].reset();
        $("#edit-global-error").addClass("hidden");
        $('[id^="edit-error-"]').addClass("hidden").text("");
        $(".input-error").removeClass("input-error");

        // Disable button by default
        $("#btn-submit-edit")
            .prop("disabled", true)
            .addClass("opacity-50 cursor-not-allowed");
    }

    // Validasi Edit (Logic Comparison / Perbandingan)
    function validateFormEdit() {
        const name = $("#edit-input-name").val().trim();
        const email = $("#edit-input-email").val().trim();
        const password = $("#edit-input-password").val();
        const fileInput = document.getElementById("edit-input-foto");

        let isValidFormat = true;
        let hasChanges = false;

        // 1. Validasi Format
        // Nama
        if (name.length < 3) {
            toggleError(
                "#edit-input-name",
                "#edit-error-name",
                "Min 3 karakter"
            );
            isValidFormat = false;
        } else toggleError("#edit-input-name", "#edit-error-name", null);

        // Email
        if (!validateEmail(email)) {
            toggleError(
                "#edit-input-email",
                "#edit-error-email",
                "Email tidak valid"
            );
            isValidFormat = false;
        } else toggleError("#edit-input-email", "#edit-error-email", null);

        // Password (Optional, validate only if filled)
        if (password !== "" && password.length < 6) {
            toggleError(
                "#edit-input-password",
                "#edit-error-password",
                "Min 6 karakter"
            );
            isValidFormat = false;
        } else toggleError("#edit-input-password", "#edit-error-password", null);

        // 2. Cek Perubahan
        if (name !== editOriginalData.name) hasChanges = true;
        if (email !== editOriginalData.email) hasChanges = true;
        if (password !== "") hasChanges = true; // Kalau password diisi, dianggap ada perubahan
        if (fileInput.files.length > 0) hasChanges = true; // Kalau ada file baru dipilih

        // 3. Update Button State
        const btn = $("#btn-submit-edit");
        if (isValidFormat && hasChanges) {
            btn.prop("disabled", false).removeClass(
                "opacity-50 cursor-not-allowed"
            );
        } else {
            btn.prop("disabled", true).addClass(
                "opacity-50 cursor-not-allowed"
            );
        }
    }

    // Listeners Edit
    $("#edit-input-name, #edit-input-email, #edit-input-password").on(
        "input blur",
        validateFormEdit
    );

    // Image Preview untuk Edit
    $("#edit-input-foto").on("change", function () {
        const file = this.files[0];
        if (file) {
            // Validasi tipe & ukuran
            if (!file.type.startsWith("image/")) {
                alert("File harus gambar!");
                this.value = "";
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert("Max 2MB!");
                this.value = "";
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                $("#edit-img-preview").attr("src", e.target.result);
                validateFormEdit(); // Re-check button state
            };
            reader.readAsDataURL(file);
        } else {
            // Jika cancel select file, kembalikan ke foto lama
            $("#edit-img-preview").attr("src", editOriginalData.photoUrl);
            validateFormEdit();
        }
    });

    // AJAX Update Submission
    $("#form-edit-driver").on("submit", function (e) {
        e.preventDefault();
        const id = $("#edit-id").val();

        // Ubah action URL secara manual karena ID dinamis
        $(this).attr("action", `/driver/${id}`);

        submitForm(
            $(this),
            "#btn-submit-edit",
            "#btn-loading-edit",
            "modal_edit_driver"
        );
    });

    // ==========================================
    // 4. HELPER FUNCTIONS
    // ==========================================

    function toggleError(inputId, errorId, message) {
        if (message) {
            $(inputId).addClass("input-error");
            $(errorId).text(message).removeClass("hidden");
            return false;
        } else {
            $(inputId).removeClass("input-error");
            $(errorId).addClass("hidden").text("");
            return true;
        }
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function handleImagePreview(input, imgId, iconId) {
        const file = input.files[0];
        if (file) {
            if (!file.type.startsWith("image/")) {
                alert("File harus gambar!");
                input.value = "";
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert("Max 2MB!");
                input.value = "";
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                if (iconId) $(iconId).addClass("hidden");
                $(imgId).attr("src", e.target.result).removeClass("hidden");
            };
            reader.readAsDataURL(file);
        } else {
            // Logic reset preview create jika cancel
            if (iconId) $(iconId).removeClass("hidden");
            $(imgId).addClass("hidden").attr("src", "");
        }
    }

    function submitForm(form, btnId, loadingId, modalId) {
        const btn = $(btnId);
        const loading = $(loadingId);

        btn.prop("disabled", true).addClass("cursor-not-allowed");
        loading.removeClass("hidden");

        // Reset global errors
        const errPrefix = modalId === "modal_create_driver" ? "create" : "edit";
        $(`#${errPrefix}-global-error`).addClass("hidden");

        let formData = new FormData(form[0]);

        $.ajax({
            url: form.attr("action"),
            method: "POST", // Method POST (Laravel _method handles PUT/DELETE in FormData)
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                closeModal(modalId);
                // alert("Berhasil!"); // Pesan sukses dihapus sesuai request, langsung reload
                window.location.reload();
            },
            error: function (xhr) {
                btn.prop("disabled", false).removeClass("cursor-not-allowed");
                loading.addClass("hidden");

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    const prefix =
                        modalId === "modal_create_driver" ? "" : "edit-";

                    $.each(errors, function (key, value) {
                        if (key === "profile_picture") {
                            $(`#${prefix}error-profile_picture`)
                                .text(value[0])
                                .removeClass("hidden");
                        } else {
                            toggleError(
                                `#${prefix}input-${key}`,
                                `#${prefix}error-${key}`,
                                value[0]
                            );
                        }
                    });
                } else {
                    $(`#${errPrefix}-error-message`).text(
                        "Terjadi kesalahan server: " + xhr.statusText
                    );
                    $(`#${errPrefix}-global-error`).removeClass("hidden");
                }
            },
        });
    }

    // ==========================================
    // 5. DELETE LOGIC
    // ==========================================

    window.openDeleteDriverModal = function (id, name, email) {
        $("#btn-confirm-delete").prop("disabled", false);
        $("#loading-delete").addClass("hidden");
        $("#icon-delete").removeClass("hidden");
        $("#delete-id").val(id);
        $("#delete-name").text(name);
        $("#delete-email").text(email);
        document.getElementById("modal_hapus_driver").showModal();
    };

    window.closeDeleteDriverModal = function () {
        closeModal("modal_hapus_driver");
    };

    $("#form-hapus-driver").on("submit", function (e) {
        e.preventDefault();
        const id = $("#delete-id").val();
        $("#btn-confirm-delete").prop("disabled", true);
        $("#loading-delete").removeClass("hidden");
        $("#icon-delete").addClass("hidden");

        $.ajax({
            url: `/driver/destroy/${id}`,
            method: "POST",
            data: $(this).serialize(),
            success: function () {
                closeModal("modal_hapus_driver");
                window.location.reload();
            },
            error: function () {
                alert("Gagal menghapus.");
                $("#btn-confirm-delete").prop("disabled", false);
                $("#loading-delete").addClass("hidden");
                $("#icon-delete").removeClass("hidden");
            },
        });
    });
});
