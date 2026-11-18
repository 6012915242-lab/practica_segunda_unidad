// Pide confirmación antes de enviar los formularios de eliminación.
document.querySelectorAll("form[data-confirmar]").forEach(function (form) {
    form.addEventListener("submit", function (e) {
        if (!confirm(form.dataset.confirmar)) {
            e.preventDefault();
        }
    });
});
