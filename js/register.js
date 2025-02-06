$(document).ready(function() {
    // $("form[name='register']").submit(function(e) {
    //     let valid = true;
        
    //     $(".form-control").each(function() {
    //         if ($(this).val().trim() === "") {
    //             valid = false;
    //             $(this).addClass("is-invalid");
    //         } else {
    //             $(this).removeClass("is-invalid");
    //         }
    //     });

    //     if (!valid) {
    //         e.preventDefault();
    //         showToast("error", "Please fill all fields correctly.");
    //     }
    // });

    let toastMessage = $("#toast-container").data("message");
    let toastType = $("#toast-container").data("type");

    if (toastMessage) {
        showToast(toastType, toastMessage);
    }

    function showToast(type, message) {
        let bgColor = type === "success" ? "bg-success" : "bg-danger";
        
        let toastContainer = $("#toast-container");
    
        let toastHTML = `
            <div class="toast align-items-center text-white ${bgColor} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        
        toastContainer.html(toastHTML);
        
        let toastElement = document.querySelector(".toast");
        let toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
});
