// Időpontfoglalás - egyszerű JavaScript

var API_URL = "../api/";
var selectedAppointment = null;
var selectedService = null;

// Oldal betöltésekor futtatódik
document.addEventListener('DOMContentLoaded', function() {
    setupDatePicker();
    loadHairdressersWithSlots();
    setupEventListeners();
});

// Dátumválasztó beállítása
function setupDatePicker() {
    var datePicker = document.getElementById('appointment-date');
    var today = new Date();
    var oneMonthLater = new Date(today);
    oneMonthLater.setMonth(today.getMonth() + 1);

    var todayStr = today.toISOString().split('T')[0];
    var oneMonthLaterStr = oneMonthLater.toISOString().split('T')[0];

    datePicker.min = todayStr;
    datePicker.max = oneMonthLaterStr;
    datePicker.value = todayStr;

    datePicker.addEventListener('change', function() {
        loadHairdressersWithSlots();
    });
}

// Gombok eseménykezelői
function setupEventListeners() {
    document.getElementById('btn-back').addEventListener('click', showHairdressers);
    document.getElementById('btn-confirm').addEventListener('click', confirmBooking);
}

// Fodrászok megjelenítése
function showHairdressers() {
    document.getElementById('hairdressers-list').parentElement.classList.remove('hidden');
    document.getElementById('services-section').classList.add('hidden');
    selectedAppointment = null;
    selectedService = null;
}

// Szolgáltatások megjelenítése
function showServices() {
    document.getElementById('hairdressers-list').parentElement.classList.add('hidden');
    document.getElementById('services-section').classList.remove('hidden');
    loadServices();
}

// Fodrászok letöltése az API-ból
function loadHairdressersWithSlots() {
    var selectedDate = document.getElementById('appointment-date').value;
    fetch(API_URL + 'appointments.php?action=get_hairdressers_with_slots&date=' + selectedDate)
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                showHairdressersOnPage(data.hairdressers);
            } else {
                console.error('Hiba:', data.error);
            }
        })
        .catch(function(error) {
            console.error('Hiba:', error);
        });
}

// Fodrászok kiírása az oldalra
function showHairdressersOnPage(hairdressers) {
    var container = document.getElementById('hairdressers-list');
    container.innerHTML = '';

    if (hairdressers.length === 0) {
        container.innerHTML = '<p>Nincs elérhető fodrász.</p>';
        return;
    }

    for (var i = 0; i < hairdressers.length; i++) {
        var hairdresser = hairdressers[i];
        var html = '<div class="hairdresser-section">';
        html += '<div class="hairdresser-header">';
        html += '<img src="' + (hairdresser.image || 'default.jpg') + '" alt="' + hairdresser.name + '">';
        html += '<h3>' + hairdresser.name + '</h3>';
        html += '</div>';
        html += '<div class="time-slots-grid" id="slots-' + hairdresser.id + '"></div>';
        html += '</div>';

        var div = document.createElement('div');
        div.innerHTML = html;
        container.appendChild(div.firstChild);

        loadTimeSlots(hairdresser.id, 'slots-' + hairdresser.id);
    }
}

// Egy fodrász időpontjainak letöltése
function loadTimeSlots(hairdresserId, containerId) {
    var selectedDate = document.getElementById('appointment-date').value;

    fetch(API_URL + 'appointments.php?action=get_time_slots&date=' + selectedDate + '&hairdresser_id=' + hairdresserId)
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                showTimeSlots(hairdresserId, containerId, selectedDate, data.time_slots);
            } else {
                console.error('Hiba:', data.error);
            }
        })
        .catch(function(error) {
            console.error('Hiba:', error);
        });
}

// Időpontok kiírása
function showTimeSlots(hairdresserId, containerId, date, bookedSlots) {
    var container = document.getElementById(containerId);
    container.innerHTML = '';

    var workingHours = [
        '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
        '12:00', '12:30', '13:00', '13:30', '14:00', '14:30',
        '15:00', '15:30', '16:00', '16:30', '17:00', '17:30'
    ];

    var today = new Date();
    var todayStr = today.toISOString().split('T')[0];
    var currentTime = today.getHours() * 60 + today.getMinutes(); // percekben

    for (var i = 0; i < workingHours.length; i++) {
        var time = workingHours[i];
        var isBooked = bookedSlots.includes(time);
        
        // Foglalt időpontokat nem írjuk ki
        if (isBooked) {
            continue;
        }

        // Ha ma van, és az időpont már elmúlt, nem írjuk ki
        if (date === todayStr) {
            var timeParts = time.split(':');
            var timeMinutes = parseInt(timeParts[0]) * 60 + parseInt(timeParts[1]);
            if (timeMinutes <= currentTime) {
                continue;
            }
        }
        
        var button = document.createElement('button');
        button.className = 'time-slot-btn';
        button.textContent = time;

        button.onclick = (function(hId, d, t) {
            return function() {
                selectTimeSlot(hId, d, t);
            };
        })(hairdresserId, date, time);

        container.appendChild(button);
    }
}

// Időpont kiválasztása
function selectTimeSlot(hairdresserId, date, time) {
    fetch(API_URL + 'appointments.php?action=get_hairdressers')
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                var hairdresser = null;
                for (var i = 0; i < data.hairdressers.length; i++) {
                    if (data.hairdressers[i].id == hairdresserId) {
                        hairdresser = data.hairdressers[i];
                        break;
                    }
                }
                selectedAppointment = {
                    hairdresser: hairdresser,
                    date: date,
                    time: time
                };
                showServices();
            }
        })
        .catch(function(error) {
            console.error('Hiba:', error);
        });
}

// Szolgáltatások letöltése
function loadServices() {
    fetch(API_URL + 'appointments.php?action=get_services')
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                showServicesOnPage(data.services);
            } else {
                console.error('Hiba:', data.error);
            }
        })
        .catch(function(error) {
            console.error('Hiba:', error);
        });
}

// Szolgáltatások kiírása
function showServicesOnPage(services) {
    var container = document.getElementById('services-list');
    container.innerHTML = '';

    for (var i = 0; i < services.length; i++) {
        var service = services[i];
        var html = '<div class="service-item">';
        html += '<div class="service-info">';
        html += '<h4>' + service.name + '</h4>';
        html += '<p>' + service.description + '</p>';
        html += '<span class="price">' + service.price.toLocaleString() + ' Ft</span>';
        html += '</div>';
        html += '<button class="select-service-btn" onclick="selectService(' + service.id + ', ' + i + ')">Kiválasztás</button>';
        html += '</div>';

        var div = document.createElement('div');
        div.innerHTML = html;
        container.appendChild(div.firstChild);
    }
}

// Szolgáltatás kiválasztása
function selectService(serviceId, index) {
    var services = document.querySelectorAll('.service-item');
    for (var i = 0; i < services.length; i++) {
        services[i].classList.remove('selected');
    }
    services[index].classList.add('selected');

    var selectedDiv = services[index];
    var name = selectedDiv.querySelector('h4').textContent;
    var priceText = selectedDiv.querySelector('.price').textContent;
    var price = parseInt(priceText.replace(' Ft', ''));

    selectedService = {
        id: serviceId,
        name: name,
        price: price
    };
}

// Foglalás megerősítése
function confirmBooking() {
    if (!selectedAppointment || !selectedService) {
        alert('Kérjük válassz szolgáltatást!');
        return;
    }

    var bookingData = {
        service_id: selectedService.id,
        hairdresser_id: selectedAppointment.hairdresser.id,
        date: selectedAppointment.date,
        time: selectedAppointment.time
    };

    fetch(API_URL + 'appointments.php?action=book_appointment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(bookingData)
    })
    .then(function(response) {
        return response.text();
    })
    .then(function(text) {
        try {
            var data = JSON.parse(text);
            if (data.success) {
                alert('Időpont sikeresen lefoglalva!');
                window.location.href = 'index.php';
            } else {
                alert('Hiba: ' + data.error);
            }
        } catch (e) {
            alert('Hiba: ' + text);
            console.error('Hiba:', e);
        }
    })
    .catch(function(error) {
        alert('Hiba történt a foglalás során. Kérjük, próbáld újra, vagy lépj kapcsolatba az ügyfélszolgálattal.');
        console.error('Hiba:', error);
    });
}