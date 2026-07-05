const HttpService = {
    // Pengaturan global untuk menyertakan token di setiap request
    init: function () {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
    },

    post: function (address, data, successCallback, errorCallback) {
        $.ajax({
            url: address,
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: JSON.stringify(data),
            contentType: "application/json",
            success: successCallback,
            error: errorCallback,
        });
    },

    put: function (address, data, successCallback, errorCallback) {
        $.ajax({
            url: address,
            method: "PUT",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: JSON.stringify(data),
            contentType: "application/json",
            success: successCallback,
            error: errorCallback,
        });
    },

    getUser: function (successCallback) {
        $.ajax({
            url: "/api/user",
            method: "GET",
            success: successCallback,
        });
    },
};

HttpService.init();
// BASE CLASS
