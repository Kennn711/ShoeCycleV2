$(document).ready(function () {
    let isFormShoeValid = false;

    // ========== FUNGSI MODAL TAMBAH SEPATU ==========

    function openModalTambahShoe() {
        const modal = document.getElementById("modal_tambah_sepatu");
        if (modal) {
            if (modal.open) {
                modal.close();
            }
            setTimeout(() => {
                modal.showModal();
                // Trigger validation untuk field yang sudah terisi (jika ada old values)
                validateAllFields();
            }, 100);
        }
    }

    function closeModalTambahShoe() {
        const modal = document.getElementById("modal_tambah_sepatu");
        if (modal && modal.open) {
            modal.close();
        }

        setTimeout(() => {
            resetModalShoeData();
        }, 100);
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

        // Reset validation state
        isFormShoeValid = false;

        // Validate name
        if (name.length >= 3 && name.length <= 100) {
            isFormShoeValid = true;
        }

        // Validate category
        if (!category) {
            isFormShoeValid = false;
        }

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

    // Event handler untuk tombol tambah sepatu
    $(document).on("click", "#btn-tambah-sepatu", function (e) {
        e.preventDefault();
        openModalTambahShoe();
    });

    // Event handler untuk tombol close
    $("#btn-close-modal-shoe, #btn-batal-shoe").on("click", function (e) {
        e.preventDefault();
        closeModalTambahShoe();
    });

    // Event handler untuk backdrop
    $(document).on("click", ".modal-backdrop", function (e) {
        const modalShoe = document.getElementById("modal_tambah_sepatu");

        if (modalShoe && modalShoe.open) {
            e.preventDefault();
            closeModalTambahShoe();
        }
    });

    // Event listener untuk close modal
    const modalShoe = document.getElementById("modal_tambah_sepatu");
    if (modalShoe) {
        modalShoe.addEventListener("close", function () {
            setTimeout(() => {
                resetModalShoeData();
            }, 50);
        });
    }

    // Live validation untuk nama sepatu
    $("#shoe_name").on("input", function () {
        const value = $(this).val().trim();
        const charCount = value.length;

        $("#error-shoe-name").addClass("hidden");
        $(this).removeClass("input-error");

        if (value === "") {
            showError("error-shoe-name", "Nama sepatu tidak boleh kosong");
            $(this).addClass("input-error");
            isFormShoeValid = false;
            updateSubmitButtonState();
            return;
        }

        if (charCount < 3) {
            showError("error-shoe-name", "Nama sepatu minimal 3 karakter");
            $(this).addClass("input-error");
            isFormShoeValid = false;
            updateSubmitButtonState();
            return;
        }

        if (charCount > 100) {
            showError("error-shoe-name", "Nama sepatu maksimal 100 karakter");
            $(this).addClass("input-error");
            isFormShoeValid = false;
            updateSubmitButtonState();
            return;
        }

        validateAllFields();
    });

    // Live validation untuk kategori
    $("#shoe_category").on("change", function () {
        const value = $(this).val();

        $("#error-shoe-category").addClass("hidden");
        $(this).removeClass("input-error");

        if (!value) {
            showError("error-shoe-category", "Kategori harus dipilih");
            $(this).addClass("input-error");
            isFormShoeValid = false;
            updateSubmitButtonState();
            return;
        }

        validateAllFields();
    });

    // Character counter untuk deskripsi
    $("#shoe_description").on("input", function () {
        const length = $(this).val().length;
        $("#desc-count").text(length);
    });

    // Helper function untuk show error
    function showError(elementId, message) {
        $("#" + elementId)
            .text(message)
            .removeClass("hidden");
    }

    // Handle form submit
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
        const btnText = $("#btn-text-shoe");
        const btnLoading = $("#btn-loading-shoe");

        btnSubmit
            .prop("disabled", true)
            .removeClass("cursor-pointer")
            .addClass("cursor-not-allowed opacity-50");
        btnText.addClass("hidden");
        btnLoading.removeClass("hidden");

        if (typeof window.reinitializeShoeTable === "function") {
            window.reinitializeShoeTable();
        }
    });

    // Auto-open modal jika ada error dari backend
    if (modalShoe && modalShoe.hasAttribute("open")) {
        setTimeout(() => {
            if (!modalShoe.open) {
                modalShoe.showModal();
            }
            validateAllFields();
        }, 100);
    }

    // ========== FUNGSI MODAL DESKRIPSI ==========

    function openModalDescription(shoeId, shoeName, description) {
        const modal = document.getElementById("modal_view_description");
        if (modal) {
            if (modal.open) {
                modal.close();
            }

            setTimeout(() => {
                // Set data ke modal
                $("#description-shoe-name").text(shoeName);

                // Format description - convert line breaks to <br>
                const formattedDescription = description
                    ? description.replace(/\n/g, "<br>")
                    : '<span class="text-gray-400 italic">Tidak ada deskripsi</span>';

                $("#description-content").html(formattedDescription);

                // Set character count
                $("#char-count").text(description ? description.length : 0);

                // Buka modal
                modal.showModal();
            }, 100);
        }
    }

    function closeModalDescription() {
        const modal = document.getElementById("modal_view_description");
        if (modal && modal.open) {
            modal.close();
        }

        setTimeout(() => {
            resetModalDescriptionData();
        }, 100);
    }

    function resetModalDescriptionData() {
        $("#description-shoe-name").text("-");
        $("#description-content").text("");
    }

    // Event handler untuk tombol view description
    $(document).on("click", ".btn-view-description", function (e) {
        e.preventDefault();
        const shoeId = $(this).data("id");
        const shoeName = $(this).data("name");
        const description = $(this).data("description");

        openModalDescription(shoeId, shoeName, description);
    });

    // Event handler untuk tombol close
    $("#btn-close-modal-description, #btn-tutup-description").on(
        "click",
        function (e) {
            e.preventDefault();
            closeModalDescription();
        }
    );

    // Event listener untuk close modal
    const modalDescription = document.getElementById("modal_view_description");
    if (modalDescription) {
        modalDescription.addEventListener("close", function () {
            setTimeout(() => {
                resetModalDescriptionData();
            }, 50);
        });
    }

    // Update backdrop handler untuk description modal
    $(document).on("click", ".modal-backdrop", function (e) {
        const modalShoe = document.getElementById("modal_tambah_sepatu");
        const modalDescription = document.getElementById(
            "modal_view_description"
        );

        if (modalShoe && modalShoe.open) {
            e.preventDefault();
            closeModalTambahShoe();
        } else if (modalDescription && modalDescription.open) {
            e.preventDefault();
            closeModalDescription();
        }
    });

    // ========== FUNGSI HAPUS SEPATU ==========

    function openModalDeleteShoe(id, name, category, brand, date) {
        const modal = document.getElementById("modal_hapus_sepatu");
        if (modal) {
            if (modal.open) {
                modal.close();
            }

            setTimeout(() => {
                // Set data ke modal
                $("#delete-shoe-name").text(name);
                $("#delete-shoe-category").text(category);
                $("#delete-shoe-brand").text(brand);
                $("#delete-shoe-date").text(date);

                // Set form action
                const deleteUrl = `/shoes/${id}`;
                $("#form-hapus-sepatu").attr("action", deleteUrl);

                // Buka modal
                modal.showModal();
            }, 100);
        }
    }

    function closeModalDeleteShoe() {
        const modal = document.getElementById("modal_hapus_sepatu");
        if (modal && modal.open) {
            modal.close();
        }

        setTimeout(() => {
            resetModalDeleteShoeData();
        }, 100);
    }

    function resetModalDeleteShoeData() {
        $("#delete-shoe-name").text("-");
        $("#delete-shoe-category").text("-");
        $("#delete-shoe-brand").text("-");
        $("#delete-shoe-date").text("-");
        $("#form-hapus-sepatu").attr("action", "");
        resetDeleteShoeButtonState();
    }

    function resetDeleteShoeButtonState() {
        const btnSubmit = $("#btn-konfirmasi-hapus-shoe");
        const btnText = $("#btn-hapus-text-shoe");
        const btnLoading = $("#btn-hapus-loading-shoe");

        btnSubmit.prop("disabled", false);
        btnText.removeClass("hidden");
        btnLoading.addClass("hidden");
    }

    // Event handler untuk tombol delete
    $(document).on("click", ".btn-delete-shoe", function (e) {
        e.preventDefault();
        const shoeId = $(this).data("id");
        const shoeName = $(this).data("name");
        const shoeCategory = $(this).data("category");
        const shoeBrand = $(this).data("brand");
        const shoeCreated = $(this).data("created");

        openModalDeleteShoe(
            shoeId,
            shoeName,
            shoeCategory,
            shoeBrand,
            shoeCreated
        );
    });

    // Event handler untuk tombol close dan batal
    $("#btn-close-modal-delete-shoe, #btn-batal-hapus-shoe").on(
        "click",
        function (e) {
            e.preventDefault();
            closeModalDeleteShoe();
        }
    );

    // Event listener untuk close modal
    const modalDeleteShoe = document.getElementById("modal_hapus_sepatu");
    if (modalDeleteShoe) {
        modalDeleteShoe.addEventListener("close", function () {
            setTimeout(() => {
                resetModalDeleteShoeData();
            }, 50);
        });
    }

    // Handle form submit delete
    $("#form-hapus-sepatu").on("submit", function (e) {
        const btnSubmit = $("#btn-konfirmasi-hapus-shoe");
        const btnText = $("#btn-hapus-text-shoe");
        const btnLoading = $("#btn-hapus-loading-shoe");

        btnSubmit.prop("disabled", true);
        btnText.addClass("hidden");
        btnLoading.removeClass("hidden");

        if (typeof window.reinitializeShoeTable === "function") {
            window.reinitializeShoeTable();
        }
    });

    // Update backdrop handler untuk semua modal
    $(document).on("click", ".modal-backdrop", function (e) {
        const modalShoe = document.getElementById("modal_tambah_sepatu");
        const modalDescription = document.getElementById(
            "modal_view_description"
        );
        const modalDeleteShoe = document.getElementById("modal_hapus_sepatu");

        if (modalShoe && modalShoe.open) {
            e.preventDefault();
            closeModalTambahShoe();
        } else if (modalDescription && modalDescription.open) {
            e.preventDefault();
            closeModalDescription();
        } else if (modalDeleteShoe && modalDeleteShoe.open) {
            e.preventDefault();
            closeModalDeleteShoe();
        }
    });

    // ========== FUNGSI EDIT SEPATU ==========

    let originalEditShoeValues = {
        name: "",
        category_id: "",
        brand: "",
        description: "",
        is_active: "",
    };
    let isFormEditShoeValid = false;

    function openModalEditShoe(
        id,
        name,
        categoryId,
        brand,
        description,
        isActive
    ) {
        const modal = document.getElementById("modal_edit_sepatu");
        if (modal) {
            if (modal.open) {
                modal.close();
            }

            setTimeout(() => {
                // Simpan nilai asli untuk perbandingan
                originalEditShoeValues = {
                    name: name,
                    category_id: categoryId,
                    brand: brand || "",
                    description: description || "",
                    is_active: isActive,
                };

                // Set data ke form
                $("#edit_shoe_name").val(name);
                $("#edit_shoe_category").val(categoryId);
                $("#edit_shoe_brand").val(brand || "");
                $("#edit_shoe_description").val(description || "");
                $("#edit_shoe_is_active").val(isActive);
                $("#edit_shoe_id").val(id);

                // Update character count
                $("#edit-desc-count").text(
                    description ? description.length : 0
                );

                // Set action form
                const editUrl = `/shoes/${id}`;
                $("#form-edit-sepatu").attr("action", editUrl);

                // Reset validasi
                $("#error-edit-shoe-name").addClass("hidden");
                $("#error-edit-shoe-category").addClass("hidden");
                $("#backend-error-edit-shoe").remove();
                $("#backend-error-edit-category").remove();
                $("#edit_shoe_name, #edit_shoe_category").removeClass(
                    "input-error"
                );

                // Validasi awal
                isFormEditShoeValid = true;
                updateButtonStateEditShoe();

                // Buka modal
                modal.showModal();
            }, 100);
        }
    }

    function closeModalEditShoe() {
        const modal = document.getElementById("modal_edit_sepatu");
        if (modal && modal.open) {
            modal.close();
        }

        setTimeout(() => {
            resetModalEditShoeData();
        }, 100);
    }

    function resetModalEditShoeData() {
        $("#form-edit-sepatu")[0].reset();
        $("#form-edit-sepatu").attr("action", "");
        $("#error-edit-shoe-name").addClass("hidden");
        $("#error-edit-shoe-category").addClass("hidden");
        $("#backend-error-edit-shoe").remove();
        $("#backend-error-edit-category").remove();
        $("#edit_shoe_name, #edit_shoe_category").removeClass("input-error");
        $("#edit-desc-count").text("0");

        originalEditShoeValues = {
            name: "",
            category_id: "",
            brand: "",
            description: "",
            is_active: "",
        };
        isFormEditShoeValid = false;
        resetEditShoeButtonState();
    }

    function resetEditShoeButtonState() {
        const btnSubmit = $("#btn-submit-edit-shoe");
        const btnText = $("#btn-text-edit-shoe");
        const btnLoading = $("#btn-loading-edit-shoe");

        btnSubmit
            .prop("disabled", true)
            .addClass("opacity-50 cursor-not-allowed")
            .removeClass("cursor-pointer");
        btnText.removeClass("hidden");
        btnLoading.addClass("hidden");
    }

    function updateButtonStateEditShoe() {
        const btnSubmit = $("#btn-submit-edit-shoe");
        const currentValues = {
            name: $("#edit_shoe_name").val().trim(),
            category_id: $("#edit_shoe_category").val(),
            brand: $("#edit_shoe_brand").val().trim(),
            description: $("#edit_shoe_description").val().trim(),
            is_active: $("#edit_shoe_is_active").val(),
        };

        // Cek apakah ada perubahan
        const hasChanges =
            currentValues.name !== originalEditShoeValues.name ||
            currentValues.category_id !== originalEditShoeValues.category_id ||
            currentValues.brand !== originalEditShoeValues.brand ||
            currentValues.description !== originalEditShoeValues.description ||
            currentValues.is_active !== originalEditShoeValues.is_active;

        // Button enabled HANYA jika:
        // 1. Format valid (isFormEditShoeValid = true)
        // 2. Ada perubahan (hasChanges = true)
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

    // Event handler untuk tombol edit
    $(document).on("click", ".btn-edit-shoe", function (e) {
        e.preventDefault();
        const shoeId = $(this).data("id");
        const shoeName = $(this).data("name");
        const categoryId = $(this).data("category");
        const brand = $(this).data("brand");
        const description = $(this).data("description");
        const isActive = $(this).data("active");

        openModalEditShoe(
            shoeId,
            shoeName,
            categoryId,
            brand,
            description,
            isActive
        );
    });

    // Event handler untuk tombol close
    $("#btn-close-modal-edit-shoe, #btn-batal-edit-shoe").on(
        "click",
        function (e) {
            e.preventDefault();
            closeModalEditShoe();
        }
    );

    // Event listener untuk close modal
    const modalEditShoe = document.getElementById("modal_edit_sepatu");
    if (modalEditShoe) {
        modalEditShoe.addEventListener("close", function () {
            setTimeout(() => {
                resetModalEditShoeData();
            }, 50);
        });
    }

    // Di function openModalEditShoe
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
            if (modal.open) {
                modal.close();
            }

            setTimeout(() => {
                // Simpan nilai asli untuk perbandingan
                originalEditShoeValues = {
                    name: name,
                    category_id: categoryId,
                    brand_name: brandName || "", // ✅ GANTI
                    description: description || "",
                    is_active: isActive,
                };

                // Set data ke form
                $("#edit_shoe_name").val(name);
                $("#edit_shoe_category").val(categoryId);
                $("#edit_shoe_brand").val(brandName || ""); // ✅ GANTI
                $("#edit_shoe_description").val(description || "");
                $("#edit_shoe_is_active").val(isActive);
                $("#edit_shoe_id").val(id);

                // Update character count
                $("#edit-desc-count").text(
                    description ? description.length : 0
                );

                // Set action form
                const editUrl = `/shoes/${id}`;
                $("#form-edit-sepatu").attr("action", editUrl);

                // Reset validasi
                $("#error-edit-shoe-name").addClass("hidden");
                $("#error-edit-shoe-category").addClass("hidden");
                $("#backend-error-edit-shoe").remove();
                $("#backend-error-edit-category").remove();
                $("#edit_shoe_name, #edit_shoe_category").removeClass(
                    "input-error"
                );

                // Validasi awal
                isFormEditShoeValid = true;
                updateButtonStateEditShoe();

                // Buka modal
                modal.showModal();
            }, 100);
        }
    }

    // Di function resetModalEditShoeData
    function resetModalEditShoeData() {
        $("#form-edit-sepatu")[0].reset();
        $("#form-edit-sepatu").attr("action", "");
        $("#error-edit-shoe-name").addClass("hidden");
        $("#error-edit-shoe-category").addClass("hidden");
        $("#backend-error-edit-shoe").remove();
        $("#backend-error-edit-category").remove();
        $("#edit_shoe_name, #edit_shoe_category").removeClass("input-error");
        $("#edit-desc-count").text("0");

        originalEditShoeValues = {
            name: "",
            category_id: "",
            brand_name: "", // ✅ GANTI
            description: "",
            is_active: "",
        };
        isFormEditShoeValid = false;
        resetEditShoeButtonState();
    }

    // Di function updateButtonStateEditShoe
    function updateButtonStateEditShoe() {
        const btnSubmit = $("#btn-submit-edit-shoe");
        const currentValues = {
            name: $("#edit_shoe_name").val().trim(),
            category_id: $("#edit_shoe_category").val(),
            brand_name: $("#edit_shoe_brand").val().trim(), // ✅ GANTI
            description: $("#edit_shoe_description").val().trim(),
            is_active: $("#edit_shoe_is_active").val(),
        };

        // Cek apakah ada perubahan
        const hasChanges =
            currentValues.name !== originalEditShoeValues.name ||
            currentValues.category_id !== originalEditShoeValues.category_id ||
            currentValues.brand_name !== originalEditShoeValues.brand_name || // ✅ GANTI
            currentValues.description !== originalEditShoeValues.description ||
            currentValues.is_active !== originalEditShoeValues.is_active;

        // Button enabled HANYA jika:
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

    // Di validation nama sepatu - cek perubahan
    $("#edit_shoe_name").on("input", function () {
        const value = $(this).val().trim();
        const charCount = value.length;

        // ... validasi lainnya ...

        // Cek apakah sama dengan nilai asli
        if (
            value === originalEditShoeValues.name &&
            $("#edit_shoe_category").val() ===
                originalEditShoeValues.category_id &&
            $("#edit_shoe_brand").val().trim() ===
                originalEditShoeValues.brand_name && // ✅ GANTI
            $("#edit_shoe_description").val().trim() ===
                originalEditShoeValues.description &&
            $("#edit_shoe_is_active").val() === originalEditShoeValues.is_active
        ) {
            showErrorEditShoe("Tidak ada perubahan yang dilakukan");
            updateButtonStateEditShoe();
            return;
        }

        // Semua validasi lolos
        isFormEditShoeValid = true;
        updateButtonStateEditShoe();
    });

    // Di form submit validation
    $("#form-edit-sepatu").on("submit", function (e) {
        const name = $("#edit_shoe_name").val().trim();
        const category = $("#edit_shoe_category").val();

        // ... validasi lainnya ...

        // Validasi perubahan
        const currentValues = {
            name: name,
            category_id: category,
            brand_name: $("#edit_shoe_brand").val().trim(), // ✅ GANTI
            description: $("#edit_shoe_description").val().trim(),
            is_active: $("#edit_shoe_is_active").val(),
        };

        const hasChanges =
            currentValues.name !== originalEditShoeValues.name ||
            currentValues.category_id !== originalEditShoeValues.category_id ||
            currentValues.brand_name !== originalEditShoeValues.brand_name || // ✅ GANTI
            currentValues.description !== originalEditShoeValues.description ||
            currentValues.is_active !== originalEditShoeValues.is_active;

        if (!hasChanges) {
            e.preventDefault();
            showErrorEditShoe("Tidak ada perubahan yang dilakukan");
            updateButtonStateEditShoe();
            return false;
        }

        // ... loading state ...

        if (typeof window.reinitializeShoeTable === "function") {
            window.reinitializeShoeTable();
        }
    });

    // Di auto-open modal edit
    if (modalEditShoe && modalEditShoe.hasAttribute("open")) {
        setTimeout(() => {
            if (!modalEditShoe.open) {
                modalEditShoe.showModal();
            }

            const oldName = $("#edit_shoe_name").val();
            const oldCategory = $("#edit_shoe_category").val();
            const oldBrand = $("#edit_shoe_brand").val(); // Tetap pakai ID element
            const oldDescription = $("#edit_shoe_description").val();
            const oldActive = $("#edit_shoe_is_active").val();

            if (oldName) {
                originalEditShoeValues = {
                    name: oldName,
                    category_id: oldCategory,
                    brand_name: oldBrand || "", // ✅ GANTI property name
                    description: oldDescription || "",
                    is_active: oldActive,
                };
                isFormEditShoeValid = true;
                updateButtonStateEditShoe();
            }
        }, 100);
    }

    // Update backdrop handler untuk semua modal
    $(document).on("click", ".modal-backdrop", function (e) {
        const modalShoe = document.getElementById("modal_tambah_sepatu");
        const modalEditShoe = document.getElementById("modal_edit_sepatu");
        const modalDescription = document.getElementById(
            "modal_view_description"
        );
        const modalDeleteShoe = document.getElementById("modal_hapus_sepatu");

        if (modalShoe && modalShoe.open) {
            e.preventDefault();
            closeModalTambahShoe();
        } else if (modalEditShoe && modalEditShoe.open) {
            e.preventDefault();
            closeModalEditShoe();
        } else if (modalDescription && modalDescription.open) {
            e.preventDefault();
            closeModalDescription();
        } else if (modalDeleteShoe && modalDeleteShoe.open) {
            e.preventDefault();
            closeModalDeleteShoe();
        }
    });
});
