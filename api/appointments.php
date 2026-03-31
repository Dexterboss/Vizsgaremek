<?php
// Időpontfoglalás API

header('Content-Type: application/json');
session_start();
require_once '../config/db.php';

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Bejelentkezés szükséges']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Műveletek
if ($action == 'get_services') {
    getServices();
}
elseif ($action == 'get_hairdressers') {
    getHairdressers();
}
elseif ($action == 'get_hairdressers_with_slots') {
    getHairdressersWithSlots();
}
elseif ($action == 'get_time_slots') {
    getTimeSlots();
}
elseif ($action == 'book_appointment') {
    bookAppointment();
}
else {
    echo json_encode(['success' => false, 'error' => 'Ismeretlen művelet']);
}

// Összes szolgáltatás
function getServices() {
    global $conn;
    
    $sql = "SELECT id, name, description, price FROM services ORDER BY name";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $services = array();
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        echo json_encode(['success' => true, 'services' => $services]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nincsenek szolgáltatások']);
    }
}

// Összes fodrász
function getHairdressers() {
    global $conn;
    
    $sql = "SELECT id, name, image FROM hairdressers ORDER BY name";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $hairdressers = array();
        while ($row = $result->fetch_assoc()) {
            $hairdressers[] = $row;
        }
        echo json_encode(['success' => true, 'hairdressers' => $hairdressers]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nincsenek fodrászok']);
    }
}

// Fodrászok listája (ugyanaz mint getHairdressers)
function getHairdressersWithSlots() {
    getHairdressers();
}

// Foglalt időpontok egy napon
function getTimeSlots() {
    global $conn;
    
    $date = isset($_GET['date']) ? $_GET['date'] : '';
    $hairdresser_id = isset($_GET['hairdresser_id']) ? (int)$_GET['hairdresser_id'] : 0;
    
    if (empty($date) || $hairdresser_id == 0) {
        echo json_encode(['success' => false, 'error' => 'Hiányzó adatok']);
        return;
    }
    
    $sql = "SELECT time FROM appointments WHERE date = '$date' AND hairdresser_id = $hairdresser_id AND status = 'foglalt'";
    $result = $conn->query($sql);
    
    $booked_slots = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Csak HH:MM formátum (09:30 formátum)
            $time = substr($row['time'], 0, 5);
            $booked_slots[] = $time;
        }
    }
    
    echo json_encode(['success' => true, 'time_slots' => $booked_slots]);
}

// Foglalás mentése
function bookAppointment() {
    global $conn, $user_id;
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $service_id = isset($data['service_id']) ? (int)$data['service_id'] : 0;
    $hairdresser_id = isset($data['hairdresser_id']) ? (int)$data['hairdresser_id'] : 0;
    $date = isset($data['date']) ? $data['date'] : '';
    $time = isset($data['time']) ? $data['time'] : '';
    
    // Ellenőrzés
    if ($service_id == 0 || $hairdresser_id == 0 || $date == '' || $time == '') {
        echo json_encode(['success' => false, 'error' => 'Hiányzó adatok']);
        return;
    }
    
    // Szabad-e az időpont?
    $sql = "SELECT id FROM appointments WHERE date = '$date' AND time = '$time' AND hairdresser_id = $hairdresser_id AND status = 'foglalt'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Ez az időpont már foglalt']);
        return;
    }
    
    // Foglalás mentése
    $status = 'foglalt';
    $sql = "INSERT INTO appointments (user_id, service_id, hairdresser_id, date, time, status) VALUES ($user_id, $service_id, $hairdresser_id, '$date', '$time', '$status')";
    
    if ($conn->query($sql)) {
        $appointment_id = $conn->insert_id;
        echo json_encode(['success' => true, 'appointment_id' => $appointment_id]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Hiba a foglalás során']);
    }
}

?>