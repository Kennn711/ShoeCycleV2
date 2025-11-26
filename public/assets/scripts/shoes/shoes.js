// assets/scripts/shoes/shoes.js
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

        isFormShoeValid = false;

        if (name.length >= 3 && name.length <= 100 && category) {
            isFormShoeValid = true;
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

    // ✅ PENTING: Handle form submit - JANGAN PAKAI e.preventDefault() jika validasi lolos
    $("#form-tambah-sepatu").on("submit", function (e) {
        const name = $("#shoe_name").val().trim();
        const category = $("#shoe_category").val();

        // Validasi final
        if (!name || name.length < 3 || name.length > 100) {
            e.preventDefault(); // Hanya prevent jika error
            showError("error-shoe-name", "Harap perbaiki nama sepatu");
            $("#shoe_name").addClass("input-error");
            return false;
        }

        if (!category) {
            e.preventDefault(); // Hanya prevent jika error
            showError("error-shoe-category", "Harap pilih kategori");
            $("#shoe_category").addClass("input-error");
            return false;
        }

        // ✅ Jika sampai sini, validasi lolos
        // Tampilkan loading state
        const btnSubmit = $("#btn-submit-shoe");
        const btnText = $("#btn-text-shoe");
        const btnLoading = $("#btn-loading-shoe");

        btnSubmit
            .prop("disabled", true)
            .removeClass("cursor-pointer")
            .addClass("cursor-not-allowed opacity-50");
        btnText.addClass("hidden");
        btnLoading.removeClass("hidden");

        // ✅ BIARKAN FORM SUBMIT NORMAL - JANGAN return false atau e.preventDefault()
        // Form akan submit ke server dan redirect akan bekerja
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

    // Auto-open modal jika ada error dari backend
    if (modalShoe && modalShoe.hasAttribute("open")) {
        setTimeout(() => {
            if (!modalShoe.open) {
                modalShoe.showModal();
            }
            validateAllFields();
        }, 100);
    }
});
