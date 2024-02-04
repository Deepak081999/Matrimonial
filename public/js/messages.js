$(document).ready(function () {
    $('#table-mess').DataTable({
        serverSide: true,
        ajax: {
            url: BASE_URL + "/messages/getMessages",
        },
    })
    $(document).on("click", ".show_mess", function () {
        let name = $(this).attr("name");
        $("#name").html(name);
        $("#mdl-show-mess").modal("show");
        $('#messages').html($(this).html());
    });
    $(document).on("click", ".dle-mess-btn", function () {
        let id = $(this).attr("uid");
        $("#dle-mess").modal("show");
        $("#delete-id").val(id);
    });

    $("#frm-delete").submit(function () {
        let dlt = {
            url: BASE_URL + "/messages/deletemess",
            method: "post",
            dataType: "json",
            data: $("#frm-delete").serialize(),
            success: function (res) {
                $("#dle-mess").modal("hide");
                $(".modal-backdrop").remove();
                if (res.status == 1) {
                    $("#success-msg").html(res.msg);
                    $("#success-msg").show();
                    $("#table-mess").DataTable().ajax.reload(null, false);
                    setTimeout(function () {
                        $("#success-msg").hide();

                    }, 3000);
                }
            },
            error: function (err) { },
        };
        $.ajax(dlt);
    });
})