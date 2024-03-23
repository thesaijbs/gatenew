<?php

require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\EscposImage;

// Include your database connection file
include 'connect.php';

// Check if the serial number is set in the query string
if (!isset($_GET['q'])) {
    echo "No record specified for printing.";
    exit();
}

$sno = $_GET['q'];

// Fetch the details of the permission record
$query = "SELECT * FROM `perpermissions_details` WHERE `sno` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $sno);
$stmt->execute();
$result = $stmt->get_result();
$permission = $result->fetch_assoc();

// Check if the permission details are found
if (!$permission) {
    echo "No permission details found for the specified record.";
    exit();
}

try {
    // Specify the printer name for TM-T81
    $printerName = 'TM-T81';

    // Initialize the printer connector with the printer name
    $connector = new WindowsPrintConnector($printerName);

    // Initialize the printer object
    $profile = CapabilityProfile::load("simple");
    $printer = new Printer($connector, $profile);

    // Load and print the logo
    $logoPath = './images/grace_logo_bg_rem.png';
    $logo = EscposImage::load($logoPath, false);
    $printer->graphics($logo);

    // Print the heading
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setEmphasis(true);
    $printer->text("Out Pass System\n");
    $printer->setEmphasis(false);
    $printer->text("\n");

    // Print the permission details
    $printer->text("Out Pass ID: " . $permission['sno'] . "\n");
    $printer->text("Roll Number: " . $permission['rollnumber'] . "\n");
    $printer->text("Permission Type: " . $permission['prmissiontype'] . "\n");
    $printer->text("Leaving Date/Time: " . $permission['leavingdatetime'] . "\n");
    $printer->text("Return Date/Time: " . $permission['returndatetime'] . "\n");
    $printer->text("Status/Reject Reason: " . $permission['status'] . "\n");
    // Add more details as needed

    // Cut the paper (if supported by the printer)
    $printer->cut();

    // Close the printer connection
    $printer->close();

    echo "Printing successful.";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}



// old below
// require __DIR__ . '/vendor/autoload.php';

// use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
// use Mike42\Escpos\Printer;
// use Mike42\Escpos\CapabilityProfile;

// try {
//     // Specify the printer name for TM-T81
//     $printerName = 'TM-T81';

//     // Initialize the printer connector with the printer name
//     $connector = new WindowsPrintConnector($printerName);

//     // Initialize the printer object
//     $profile = CapabilityProfile::load("simple");
//     $printer = new Printer($connector, $profile);

//     // Print the heading
//     $printer->setJustification(Printer::JUSTIFY_CENTER);
//     $printer->setEmphasis(true);
//     $printer->text("Out Pass System\n");
//     $printer->setEmphasis(false);
//     $printer->text("\n");

//     // Print the user information
//     $name = "John Doe";
//     $regNo = "123456";
//     $department = "IT";
//     $year = "2024";
//     $printer->text("Name: $name\n");
//     $printer->text("Reg No: $regNo\n");
//     $printer->text("Department: $department\n");
//     $printer->text("Year: $year\n");

//     // Cut the paper (if supported by the printer)
//     $printer->cut();

//     // Close the printer connection
//     $printer->close();

//     echo "Printing successful.";

// } catch (\Exception $e) {
//     echo "Error: " . $e->getMessage();
// }
