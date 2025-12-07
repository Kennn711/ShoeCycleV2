// ========== SHOES.JS - COMPLETE & ORGANIZED VERSION ==========

$(document).ready(function () {
    // ========== GLOBAL VARIABLES ==========
    let isFormShoeValid = false;
    let originalEditShoeValues = {};
    let isFormEditShoeValid = false;

    // ========== MODAL TAMBAH SEPATU ==========

    function openModalTambahShoe() {
        const modal = document.getElementById("modal_tambah_sepatu");
        if (modal) {
            if (modal.open) modal.close();
            setTimeout(() => {
                modal.showModal();
                validateAllFields();
            }, 100);
        }
    }

    function closeModalTambahShoe() {
        const modal = document.getElementById("modal_tambah_sepatu");
        if (modal && modal.open) modal.close();
        setTimeout(() => resetModalShoeData(), 100);
    }

    function resetModalShoeData() {
        $("#form-tambah-sepatu")[0].reset();
        $(".label-text-alt.text-red-500").addClass("hidden");
        $("input, select, textarea").removeClass("input-error");
        $("#desc-count").text("0");
        isFormShoeValid = false;
        updateSubmitButtonState();
    }

    function validateAllFields() {
        const name = $("#shoe_name").val().trim();
        const category = $("#shoe_category").val();
        isFormShoeValid = name.length >= 3 && name.length <= 100 && category;
        updateSubmitButtonState();
    }

    function updateSubmitButtonState() {
        const btnSubmit = $("#btn-submit-shoe");
        if (isFormShoeValid) {
            btnSubmit
                .prop("disabled", false)
                .removeClass("opacity-50 cursor-not-allowed")
                .addClass("cursor-pointer");
        } else {
            btnSubmit
                .prop("disabled", true)
                .addClass("opacity-50 cursor-not-allowed")
                .removeClass("cursor-pointer");
        }
    }

    function showError(elementId, message) {
        $("#" + elementId)
            .text(message)
            .removeClass("hidden");
    }

    // Event Handlers - Tambah Sepatu
    $(document).on("click", "#btn-tambah-sepatu", function (e) {
        e.preventDefault();
        openModalTambahShoe();
    });

    $("#btn-close-modal-shoe, #btn-batal-shoe").on("click", function (e) {
        e.preventDefault();
        closeModalTambahShoe();
    });

    const modalShoe = document.getElementById("modal_tambah_sepatu");
    if (modalShoe) {
        modalShoe.addEventListener("close", () =>
            setTimeout(resetModalShoeData, 50)
        );
    }

    // Validation - Tambah Sepatu
    $("#shoe_name").on("input", function () {
        const value = $(this).val().trim();
        const charCount = value.length;

        $("#error-shoe-name").addClass("hidden");
        $(this).removeClass("input-error");

        if (value === "") {
            showError("error-shoe-name", "Nama sepatu tidak boleh kosong");
            $(this).addClass("input-error");
            isFormShoeValid = false;
        } else if (charCount < 3) {
            showError("error-shoe-name", "Nama sepatu minimal 3 karakter");
            $(this).addClass("input-error");
            isFormShoeValid = false;
        } else if (charCount > 100) {
            showError("error-shoe-name", "Nama sepatu maksimal 100 karakter");
            $(this).addClass("input-error");
            isFormShoeValid = false;
        } else {
            validateAllFields();
            return;
        }

        updateSubmitButtonState();
    });

    $("#shoe_category").on("change", function () {
        const value = $(this).val();
        $("#error-shoe-category").addClass("hidden");
        $(this).removeClass("input-error");

        if (!value) {
            showError("error-shoe-category", "Kategori harus dipilih");
            $(this).addClass("input-error");
            isFormShoeValid = false;
            updateSubmitButtonState();
        } else {
            validateAllFields();
        }
    });

    $("#shoe_description").on("input", function () {
        $("#desc-count").text($(this).val().length);
    });

    // Submit Form - Tambah Sepatu
    $("#form-tambah-sepatu").on("submit", function (e) {
        const name = $("#shoe_name").val().trim();
        const category = $("#shoe_category").val();

        if (!name || name.length < 3 || name.length > 100) {
            e.preventDefault();
            showError("error-shoe-name", "Harap perbaiki nama sepatu");
            $("#shoe_name").addClass("input-error");
            return false;
        }

        if (!category) {
            e.preventDefault();
            showError("error-shoe-category", "Harap pilih kategori");
            $("#shoe_category").addClass("input-error");
            return false;
        }

        const btnSubmit = $("#btn-submit-shoe");
        btnSubmit
            .prop("disabled", true)
            .removeClass("cursor-pointer")
            .addClass("cursor-not-allowed opacity-50");
        $("#btn-text-shoe").addClass("hidden");
        $("#btn-loading-shoe").removeClass("hidden");
    });

    // Auto-open modal jika ada error
    if (modalShoe && modalShoe.hasAttribute("open")) {
        setTimeout(() => {
            if (!modalShoe.open) modalShoe.showModal();
            validateAllFields();
        }, 100);
    }

    // ========== MODAL LIHAT DESKRIPSI ==========

    function openModalDescription(shoeId, shoeName, description) {
        const modal = document.getElementById("modal_view_description");
        if (modal) {
            if (modal.open) modal.close();
            setTimeout(() => {
                $("#description-shoe-name").text(shoeName);
                const formattedDescription = description
                    ? description.replace(/\n/g, "<br>")
                    : '<span class="text-gray-400 italic">Tidak ada deskripsi</span>';
                $("#description-content").html(formattedDescription);
                $("#char-count").text(description ? description.length : 0);
                modal.showModal();
            }, 100);
        }
    }

    function closeModalDescription() {
        const modal = document.getElementById("modal_view_description");
        if (modal && modal.open) modal.close();
        setTimeout(() => {
            $("#description-shoe-name").text("-");
            $("#description-content").text("");
        }, 100);
    }

    $(document).on("click", ".btn-view-description", function (e) {
        e.preventDefault();
        openModalDescription(
            $(this).data("id"),
            $(this).data("name"),
            $(this).data("description")
        );
    });

    $("#btn-close-modal-description, #btn-tutup-description").on(
        "click",
        function (e) {
            e.preventDefault();
            closeModalDescription();
        }
    );

    const modalDescription = document.getElementById("modal_view_description");
    if (modalDescription) {
        modalDescription.addEventListener("close", () =>
            setTimeout(() => {
                $("#description-shoe-name").text("-");
                $("#description-content").text("");
            }, 50)
        );
    }

    // ========== MODAL HAPUS SEPATU ==========

    function openModalDeleteShoe(id, name, category, brand, date) {
        const modal = document.getElementById("modal_hapus_sepatu");
        if (modal) {
            if (modal.open) modal.close();
            setTimeout(() => {
                $("#delete-shoe-name").text(name);
                $("#delete-shoe-category").text(category);
                $("#delete-shoe-brand").text(brand);
                $("#delete-shoe-date").text(date);
                $("#form-hapus-sepatu").attr("action", `/shoes/${id}`);
                modal.showModal();
            }, 100);
        }
    }

    function closeModalDeleteShoe() {
        const modal = document.getElementById("modal_hapus_sepatu");
        if (modal && modal.open) modal.close();
        setTimeout(() => {
            $(
                "#delete-shoe-name, #delete-shoe-category, #delete-shoe-brand, #delete-shoe-date"
            ).text("-");
            $("#form-hapus-sepatu").attr("action", "");
            $("#btn-konfirmasi-hapus-shoe").prop("disabled", false);
            $("#btn-hapus-text-shoe").removeClass("hidden");
            $("#btn-hapus-loading-shoe").addClass("hidden");
        }, 100);
    }

    $(document).on("click", ".btn-delete-shoe", function (e) {
        e.preventDefault();
        openModalDeleteShoe(
            $(this).data("id"),
            $(this).data("name"),
            $(this).data("category"),
            $(this).data("brand"),
            $(this).data("created")
        );
    });

    $("#btn-close-modal-delete-shoe, #btn-batal-hapus-shoe").on(
        "click",
        function (e) {
            e.preventDefault();
            closeModalDeleteShoe();
        }
    );

    const modalDeleteShoe = document.getElementById("modal_hapus_sepatu");
    if (modalDeleteShoe) {
        modalDeleteShoe.addEventListener("close", closeModalDeleteShoe);
    }

    $("#form-hapus-sepatu").on("submit", function () {
        $("#btn-konfirmasi-hapus-shoe").prop("disabled", true);
        $("#btn-hapus-text-shoe").addClass("hidden");
        $("#btn-hapus-loading-shoe").removeClass("hidden");
    });

    // ========== MODAL EDIT SEPATU ==========

    function openModalEditShoe(
        id,
        name,
        categoryId,
        brandName,
        description,
        isActive
    ) {
        const modal = document.getElementById("modal_edit_sepatu");
        if (modal) {
            if (modal.open) modal.close();

            setTimeout(() => {
                // Simpan nilai asli
                originalEditShoeValues = {
                    name: name,
                    category_id: String(categoryId),
                    brand_name: brandName || "",
                    description: description || "",
                    is_active: String(isActive),
                };

                // Set form values
                $("#edit_shoe_name").val(name);
                $("#edit_shoe_category").val(categoryId);
                $("#edit_shoe_brand").val(brandName || "");
                $("#edit_shoe_description").val(description || "");
                $("#edit_shoe_is_active").val(String(isActive));
                $("#edit_shoe_id").val(id);
                $("#edit-desc-count").text(
                    description ? description.length : 0
                );
                $("#form-edit-sepatu").attr("action", `/shoes/${id}`);

                // Reset validation
                $("#error-edit-shoe-name, #error-edit-shoe-category").addClass(
                    "hidden"
                );
                $(
                    "#backend-error-edit-shoe, #backend-error-edit-category"
                ).remove();
                $("#edit_shoe_name, #edit_shoe_category").removeClass(
                    "input-error"
                );

                isFormEditShoeValid = true;
                updateButtonStateEditShoe();
                modal.showModal();
            }, 100);
        }
    }

    function closeModalEditShoe() {
        const modal = document.getElementById("modal_edit_sepatu");
        if (modal && modal.open) modal.close();
        setTimeout(resetModalEditShoeData, 100);
    }

    function resetModalEditShoeData() {
        $("#form-edit-sepatu")[0].reset();
        $("#form-edit-sepatu").attr("action", "");
        $("#error-edit-shoe-name, #error-edit-shoe-category").addClass(
            "hidden"
        );
        $("#backend-error-edit-shoe, #backend-error-edit-category").remove();
        $("#edit_shoe_name, #edit_shoe_category").removeClass("input-error");
        $("#edit-desc-count").text("0");
        originalEditShoeValues = {};
        isFormEditShoeValid = false;

        const btnSubmit = $("#btn-submit-edit-shoe");
        btnSubmit
            .prop("disabled", true)
            .addClass("opacity-50 cursor-not-allowed")
            .removeClass("cursor-pointer");
        $("#btn-text-edit-shoe").removeClass("hidden");
        $("#btn-loading-edit-shoe").addClass("hidden");
    }

    function updateButtonStateEditShoe() {
        const btnSubmit = $("#btn-submit-edit-shoe");
        const currentValues = {
            name: $("#edit_shoe_name").val().trim(),
            category_id: String($("#edit_shoe_category").val()),
            brand_name: $("#edit_shoe_brand").val().trim(),
            description: $("#edit_shoe_description").val().trim(),
            is_active: String($("#edit_shoe_is_active").val()),
        };

        const hasChanges =
            currentValues.name !== originalEditShoeValues.name ||
            currentValues.category_id !== originalEditShoeValues.category_id ||
            currentValues.brand_name !== originalEditShoeValues.brand_name ||
            currentValues.description !== originalEditShoeValues.description ||
            currentValues.is_active !== originalEditShoeValues.is_active;

        if (isFormEditShoeValid && hasChanges) {
            btnSubmit
                .prop("disabled", false)
                .removeClass("opacity-50 cursor-not-allowed")
                .addClass("cursor-pointer");
        } else {
            btnSubmit
                .prop("disabled", true)
                .addClass("opacity-50 cursor-not-allowed")
                .removeClass("cursor-pointer");
        }
    }

    function showErrorEditShoe(message) {
        $("#error-edit-shoe-name").text(message).removeClass("hidden");
        $("#edit_shoe_name").addClass("input-error");
        isFormEditShoeValid = false;
    }

    // Event Handlers - Edit Sepatu
    $(document).on("click", ".btn-edit-shoe", function (e) {
        e.preventDefault();
        openModalEditShoe(
            $(this).data("id"),
            $(this).data("name"),
            $(this).data("category"),
            $(this).data("brand"),
            $(this).data("description"),
            $(this).data("active")
        );
    });

    $("#btn-close-modal-edit-shoe, #btn-batal-edit-shoe").on(
        "click",
        function (e) {
            e.preventDefault();
            closeModalEditShoe();
        }
    );

    const modalEditShoe = document.getElementById("modal_edit_sepatu");
    if (modalEditShoe) {
        modalEditShoe.addEventListener("close", () =>
            setTimeout(resetModalEditShoeData, 50)
        );
    }

    // Validation - Edit Sepatu
    $("#edit_shoe_name").on("input", function () {
        const value = $(this).val().trim();
        $("#error-edit-shoe-name").addClass("hidden");
        $(this).removeClass("input-error");

        if (!value) {
            showErrorEditShoe("Nama sepatu tidak boleh kosong");
        } else if (value.length < 3) {
            showErrorEditShoe("Nama sepatu minimal 3 karakter");
        } else if (value.length > 100) {
            showErrorEditShoe("Nama sepatu maksimal 100 karakter");
        } else {
            isFormEditShoeValid = true;
        }

        updateButtonStateEditShoe();
    });

    $("#edit_shoe_category").on("change", function () {
        const value = $(this).val();
        $("#error-edit-shoe-category").addClass("hidden");
        $(this).removeClass("input-error");

        if (!value) {
            $("#error-edit-shoe-category")
                .text("Kategori harus dipilih")
                .removeClass("hidden");
            $(this).addClass("input-error");
            isFormEditShoeValid = false;
        } else {
            isFormEditShoeValid = true;
        }

        updateButtonStateEditShoe();
    });

    $("#edit_shoe_brand, #edit_shoe_description, #edit_shoe_is_active").on(
        "input change",
        function () {
            updateButtonStateEditShoe();
        }
    );

    $("#edit_shoe_description").on("input", function () {
        $("#edit-desc-count").text($(this).val().length);
    });

    // Submit Form - Edit Sepatu
    $("#form-edit-sepatu").on("submit", function (e) {
        const name = $("#edit_shoe_name").val().trim();
        const category = $("#edit_shoe_category").val();

        if (!name || name.length < 3 || name.length > 100) {
            e.preventDefault();
            showErrorEditShoe("Harap perbaiki nama sepatu");
            return false;
        }

        if (!category) {
            e.preventDefault();
            $("#error-edit-shoe-category")
                .text("Harap pilih kategori")
                .removeClass("hidden");
            $("#edit_shoe_category").addClass("input-error");
            return false;
        }

        const currentValues = {
            name: name,
            category_id: String(category),
            brand_name: $("#edit_shoe_brand").val().trim(),
            description: $("#edit_shoe_description").val().trim(),
            is_active: String($("#edit_shoe_is_active").val()),
        };

        const hasChanges =
            currentValues.name !== originalEditShoeValues.name ||
            currentValues.category_id !== originalEditShoeValues.category_id ||
            currentValues.brand_name !== originalEditShoeValues.brand_name ||
            currentValues.description !== originalEditShoeValues.description ||
            currentValues.is_active !== originalEditShoeValues.is_active;

        if (!hasChanges) {
            e.preventDefault();
            showErrorEditShoe("Tidak ada perubahan yang dilakukan");
            return false;
        }

        $("#btn-submit-edit-shoe")
            .prop("disabled", true)
            .removeClass("cursor-pointer")
            .addClass("cursor-not-allowed opacity-50");
        $("#btn-text-edit-shoe").addClass("hidden");
        $("#btn-loading-edit-shoe").removeClass("hidden");
    });

    // Auto-open modal edit jika ada error
    if (modalEditShoe && modalEditShoe.hasAttribute("open")) {
        setTimeout(() => {
            if (!modalEditShoe.open) modalEditShoe.showModal();

            originalEditShoeValues = {
                name: $("#edit_shoe_name").val(),
                category_id: String($("#edit_shoe_category").val()),
                brand_name: $("#edit_shoe_brand").val() || "",
                description: $("#edit_shoe_description").val() || "",
                is_active: String($("#edit_shoe_is_active").val()),
            };

            if (originalEditShoeValues.name) {
                isFormEditShoeValid = true;
                updateButtonStateEditShoe();
            }
        }, 100);
    }

    // ========== BACKDROP HANDLER ==========
    $(document).on("click", ".modal-backdrop", function (e) {
        if ($("#modal_tambah_sepatu")[0]?.open) {
            e.preventDefault();
            closeModalTambahShoe();
        } else if ($("#modal_edit_sepatu")[0]?.open) {
            e.preventDefault();
            closeModalEditShoe();
        } else if ($("#modal_view_description")[0]?.open) {
            e.preventDefault();
            closeModalDescription();
        } else if ($("#modal_hapus_sepatu")[0]?.open) {
            e.preventDefault();
            closeModalDeleteShoe();
        }
    });
});
//
