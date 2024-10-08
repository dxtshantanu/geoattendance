$('#attendance').submit(function(e) {
    e.preventDefault();
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var attendance = $(this).serialize() + '&latitude=' + position.coords.latitude + '&longitude=' + position.coords.longitude;
            $.ajax({
                type: 'POST',
                url: 'attendance.php', // Adjust this to your server-side script
                data: attendance,
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        $('.alert').hide();
                        $('.alert-danger').show();
                        $('.message').html(response.message);
                    } else {
                        $('.alert').hide();
                        $('.alert-success').show();
                        $('.message').html(response.message);
                        $('#employee').val('');
                    }
                },
                error: function(xhr, status, error) {
                    $('.alert').hide();
                    $('.alert-danger').show();
                    $('.message').html('An error occurred while processing your request.');
                }
            });
        }.bind(this), function() {
            $('.alert').hide();
            $('.alert-danger').show();
            $('.message').html('Unable to retrieve your location.');
        });
    } else {
        $('.alert').hide();
        $('.alert-danger').show();
        $('.message').html('Geolocation is not supported by this browser.');
    }
});
