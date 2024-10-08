<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <style>
        .alert {
            display: none;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <p id="date"></p>
            <p id="time" class="bold"></p>
        </div>

        <div class="login-box-body">
            <h4 class="login-box-msg">Enter Employee ID</h4>

            <form id="attendance">
                <div class="form-group">
                    <select class="form-control" name="status">
                        <option value="in">Time In</option>
                        <option value="out">Time Out</option>
                    </select>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" class="form-control input-lg" id="employee" name="employee" required>
                    <span class="glyphicon glyphicon-calendar form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat" name="signin">
                            <i class="fa fa-sign-in"></i> Sign In
                        </button>
                    </div>
                </div>
            </form>

            <div class="alert alert-success mt-2 text-center">
                <span class="result"><i class="icon fa fa-check"></i> <span class="message"></span></span>
            </div>
            <div class="alert alert-danger mt-2 text-center">
                <span class="result"><i class="icon fa fa-warning"></i> <span class="message"></span></span>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function () {
            var interval = setInterval(function () {
                var momentNow = moment();
                $('#date').html(momentNow.format('dddd').substring(0, 3).toUpperCase() + ' - ' + momentNow.format('MMMM DD, YYYY'));
                $('#time').html(momentNow.format('hh:mm:ss A'));
            }, 100);

            $('#attendance').submit(function (e) {
                e.preventDefault();

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        var latitude = position.coords.latitude;
                        var longitude = position.coords.longitude;

                        // Displaying coordinates for demonstration
                        $('.alert').hide();
                        $('.alert-success .message').html('Location retrieved: Latitude: ' + latitude + ', Longitude: ' + longitude);
                        $('.alert-success').show();

                    }, function () {
                        $('.alert').hide();
                        $('.alert-danger .message').html('Unable to retrieve your location.');
                        $('.alert-danger').show();
                    });
                } else {
                    $('.alert').hide();
                    $('.alert-danger .message').html('Geolocation is not supported by this browser.');
                    $('.alert-danger').show();
                }
            });
        });
    </script>
</body>
</html>
