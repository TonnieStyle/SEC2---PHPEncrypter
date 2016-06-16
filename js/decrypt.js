/**
 * Created by toinebakkeren on 16-06-16.
 */
$(document).ready(function() {
    $("#decrypt-btn").click(function () {
        var username = $("#username").val();
        var password = $("#password").val();

        $.ajax({
            type: "POST",
            url: 'api/get.php',
            dataType: 'json',
            data: {
                username: username,
                password: password
            },
            success: function (obj, textstatus) {
                if (!('error' in obj)) {
                    console.log(obj);
                    showData(obj);
                }
            }
        });
    });
    function showData(obj) {
        $("#result").html(obj["dataReceived"]);
    }
});
