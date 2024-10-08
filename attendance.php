<?php
if (isset($_POST['employee'])) {
    $output = array('error' => false);

    include 'conn.php';
    include 'timezone.php';

    $employee = $_POST['employee'];
    $status = $_POST['status'];
    $latitude = $_POST['latitude'];  // Employee's current latitude (from frontend)
    $longitude = $_POST['longitude']; // Employee's current longitude (from frontend)

    // Define the geofence center coordinates (office location) and radius
    $geofence_lat = 21.171653404667918;
    $geofence_lng = 79.07636576502225;
    $geofence_radius = 1; // Geofence radius in meters (e.g., 100 meters)

    // Function to calculate distance between two coordinates using Haversine formula
    function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earth_radius * $c; // Distance in meters
        return $distance;
    }

    // Calculate distance between the employee's current location and the geofence center
    $distance = calculateDistance($latitude, $longitude, $geofence_lat, $geofence_lng);

    // Check if the employee is within the geofence
    if ($distance > $geofence_radius) {
        $output['error'] = true;
        $output['message'] = 'You are outside the allowed geofence area.';
    } else {
        // Proceed with attendance logic if within the geofence
        $sql = "SELECT * FROM employees WHERE employee_id = '$employee'";
        $query = $conn->query($sql);

        if ($query->num_rows > 0) {
            $row = $query->fetch_assoc();
            $id = $row['id'];

            $date_now = date('Y-m-d');

            if ($status == 'in') {
                $sql = "SELECT * FROM attendance WHERE employee_id = '$id' AND date = '$date_now' AND time_in IS NOT NULL";
                $query = $conn->query($sql);
                if ($query->num_rows > 0) {
                    $output['error'] = true;
                    $output['message'] = 'You have timed in for today';
                } else {
                    // Handle time in
                    $sched = $row['schedule_id'];
                    $lognow = date('H:i:s');
                    $sql = "SELECT * FROM schedules WHERE id = '$sched'";
                    $squery = $conn->query($sql);
                    $srow = $squery->fetch_assoc();
                    $logstatus = ($lognow > $srow['time_in']) ? 0 : 1;

                    $sql = "INSERT INTO attendance (employee_id, date, time_in, status) VALUES ('$id', '$date_now', NOW(), '$logstatus')";
                    if ($conn->query($sql)) {
                        $output['message'] = 'Time in: ' . $row['firstname'] . ' ' . $row['lastname'];
                    } else {
                        $output['error'] = true;
                        $output['message'] = $conn->error;
                    }
                }
            } else {
                $sql = "SELECT *, attendance.id AS uid FROM attendance LEFT JOIN employees ON employees.id=attendance.employee_id WHERE attendance.employee_id = '$id' AND date = '$date_now'";
                $query = $conn->query($sql);
                if ($query->num_rows < 1) {
                    $output['error'] = true;
                    $output['message'] = 'Cannot time out. No time in.';
                } else {
                    $row = $query->fetch_assoc();
                    if ($row['time_out'] != '00:00:00') {
                        $output['error'] = true;
                        $output['message'] = 'You have timed out for today';
                    } else {
                        $sql = "UPDATE attendance SET time_out = NOW() WHERE id = '" . $row['uid'] . "'";
                        if ($conn->query($sql)) {
                            $output['message'] = 'Time out: ' . $row['firstname'] . ' ' . $row['lastname'];

                            $sql = "SELECT * FROM attendance WHERE id = '" . $row['uid'] . "'";
                            $query = $conn->query($sql);
                            $urow = $query->fetch_assoc();

                            $time_in = $urow['time_in'];
                            $time_out = $urow['time_out'];

                            $sql = "SELECT * FROM employees LEFT JOIN schedules ON schedules.id=employees.schedule_id WHERE employees.id = '$id'";
                            $query = $conn->query($sql);
                            $srow = $query->fetch_assoc();

                            if ($srow['time_in'] > $urow['time_in']) {
                                $time_in = $srow['time_in'];
                            }

                            if ($srow['time_out'] < $urow['time_in']) {
                                $time_out = $srow['time_out'];
                            }

                            $time_in = new DateTime($time_in);
                            $time_out = new DateTime($time_out);
                            $interval = $time_in->diff($time_out);
                            $hrs = $interval->format('%h');
                            $mins = $interval->format('%i');
                            $mins = $mins / 60;
                            $int = $hrs + $mins;
                            if ($int > 4) {
                                $int = $int - 1;
                            }

                            $sql = "UPDATE attendance SET num_hr = '$int' WHERE id = '" . $row['uid'] . "'";
                            $conn->query($sql);
                        } else {
                            $output['error'] = true;
                            $output['message'] = $conn->error;
                        }
                    }
                }
            }
        } else {
            $output['error'] = true;
            $output['message'] = 'Employee ID not found';
        }
    }

    echo json_encode($output);
}
?>
