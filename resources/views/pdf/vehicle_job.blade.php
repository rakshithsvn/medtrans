<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vehicle Job PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f2f2f2;
            padding: 5px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td, table th {
            border: 1px solid #aaa;
            padding: 6px;
        }
        .no-border td {
            border: none;
        }
        .logo-section { text-align: center}
        .logo-section img { height: 60px; width: auto}
    </style>
</head>
<body>

<div class="section">
    <div class="logo-section"><img src="{{ asset('assets/images/logo/logo1.png') }}" alt="Logo"/></div>
    <h2>Job Card Report</h2>
    <div class="section-title">Job Details</div>
    <table class="no-border">
        <tr>
            <td><strong>Job Card No:</strong> {{ $vehicleJob->card_no }}</td>
            <td><strong>Date:</strong> {{ $vehicleJob->date }}</td>
        </tr>
        <tr>
            <td><strong>Warranty:</strong> {{ $vehicleJob->warranty }}</td>
            <td><strong>Insurance:</strong> {{ $vehicleJob->insurance }}</td>
        </tr>
        <tr>
            <td><strong>Service Type:</strong> {{ $vehicleJob->service_type }}</td>
            <td><strong>Description:</strong> {{ $vehicleJob->service_desc }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Vehicle Information</div>
    <table class="no-border">
        <tr>
            <td><strong>Vehicle No:</strong> {{ $vehicleJob->vehicle_reg_no ?? '' }}</td>
            <td><strong>Vehicle Type:</strong> {{ $vehicleJob->vehicle_type ?? '' }}</td>
        </tr>
        <tr>
            <td><strong>Insurance Due Date:</strong> {{ @$vehicleJob->insurance_expiry_date ?? '' }}</td>
            <td><strong>KM Recorded:</strong> {{ $vehicleJob->km_recorded }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Check Out</div>
    <table>
        <tr>
            <td><strong>Driver:</strong> {{ $vehicleJob->checkout_driver_name ?? '' }}</td>
            <td><strong>Supervisor:</strong> {{ $vehicleJob->checkout_supervisor_name ?? '' }}</td>
        </tr>
        <tr>
            <td><strong>Date:</strong> {{ $vehicleJob->checkout_date }}</td>
            <td><strong>Time:</strong> {{ $vehicleJob->checkout_time }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Gate Pass No:</strong> {{ $vehicleJob->gatepass_no }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Service Center Details</div>
    <table>
        <tr>
            <td><strong>Centre Name:</strong> {{ $vehicleJob->service_center }}</td>
            <td><strong>Contact No:</strong> {{ $vehicleJob->contact_no }}</td>
        </tr>
        <tr>
            <td><strong>Service Date:</strong> {{ $vehicleJob->service_date }}</td>
            <td><strong>Last Service Date:</strong> {{ $vehicleJob->last_service_date }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Service Details</div>
    <table>
        <tr>
            <td><strong>Estimation:</strong> {{ $vehicleJob->estimation }}</td>
            <td><strong>Estimation Cost:</strong> {{ $vehicleJob->estimation_cost }}</td>
        </tr>
        <tr>
            <td><strong>Estimated Repair Time:</strong> {{ $vehicleJob->est_repair_time }}</td>
            <td><strong>Substitute Vehicle:</strong> {{ $vehicleJob->substitute_vehicle }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Check In</div>
    <table>
        <tr>
            <td><strong>Driver:</strong> {{ $vehicleJob->checkin_driver_name ?? '' }}</td>
            <td><strong>Supervisor:</strong> {{ $vehicleJob->checkin_supervisor_name ?? '' }}</td>
        </tr>
        <tr>
            <td><strong>Date:</strong> {{ $vehicleJob->checkin_date }}</td>
            <td><strong>Time:</strong> {{ $vehicleJob->checkin_time }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Financial Summary</div>
    <table>
        <tr>
            <td><strong>Insurance Claim Desc:</strong> {{ $vehicleJob->insurance_desc }}</td>
            <td><strong>Amount:</strong> {{ $vehicleJob->insurance_amount }}</td>
        </tr>
        <tr>
            <td><strong>Bill Desc:</strong> {{ $vehicleJob->bill_desc }}</td>
            <td><strong>Amount:</strong> {{ $vehicleJob->bill_amount }}</td>
        </tr>
    </table>
</div>

</body>
</html>
