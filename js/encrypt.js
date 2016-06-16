/**
 * Created by toinebakkeren on 06-06-16.
 */

$(document).ready(function() {
    $("#encrypt-btn").click(function () {
        var username = $("#username").val();
        var password = $("#password").val();
        var text = $("#text").val();

        $.ajax({
            type: "POST",
            url: 'api/put.php',
            dataType: 'json',
            data: {
                username: username,
                password: password,
                text: text
            },
            success: function (obj, textstatus) {
                if (!('error' in obj)) {
                    console.log(obj);
                }
                else {
                    console.log(obj.error);
                }
            }
        });
    });
});