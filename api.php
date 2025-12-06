<?php
header('Content-Type: application/json; charset=utf-8');

// Simple API: expects GET params: destination, transportBudget, hotelBudget
$destination = isset($_GET['destination']) ? trim($_GET['destination']) : "Cox's Bazar";
$transportBudget = isset($_GET['transportBudget']) ? intval($_GET['transportBudget']) : 0;
$hotelBudget = isset($_GET['hotelBudget']) ? intval($_GET['hotelBudget']) : 0;

/*
 Budget rules (as requested):
 Transport:
   0 - 1500   -> low
   1500 - 4000 -> medium
   4000+ -> high
 Hotel per night:
   0 - 3000 -> low
   3000 - 10000 -> medium
   10000+ -> high
*/

// Determine categories
function transportCategory($b) {
    if ($b <= 1500) return 'Low';
    if ($b <= 4000) return 'Medium';
    return 'High';
}
function hotelCategory($b) {
    if ($b <= 3000) return 'Low';
    if ($b <= 10000) return 'Medium';
    return 'High';
}

$tc = transportCategory($transportBudget);
$hc = hotelCategory($hotelBudget);

// Build route suggestions (default logic for a few known destinations)
$routes = [];

// Example: if destination contains "Cox" or "Cox's"
$d_lower = mb_strtolower($destination);
if (strpos($d_lower, "cox") !== false) {
    // main artery: N1 (Dhaka -> Chattogram -> Cox's Bazar)
    $routes[] = [
        "name" => "N1 (via Dhaka → Chattogram → Cox's Bazar)",
        "traffic" => "Often busy (major highway / freight & buses)",
        "advice" => "Most common route; avoid peak holiday times. Night departures often smoother."
    ];
    $routes[] = [
        "name" => "Alternative local link roads (where available)",
        "traffic" => "Variable",
        "advice" => "Some local link-roads can save time if you know local shortcuts; check map-based live traffic before departure."
    ];
} elseif (strpos($d_lower, "bandarban") !== false) {
    $routes[] = [
        "name" => "Chittagong → Bandarban (via Ramu/Naikhongchhari routes)",
        "traffic" => "Medium",
        "advice" => "Hilly roads — drive carefully. Avoid heavy rains."
    ];
} else {
    // generic fallback
    $routes[] = [
        "name" => "Main national highway (common primary route)",
        "traffic" => "Variable",
        "advice" => "Highways carry most traffic — plan departure time to avoid rush hours."
    ];
}

// Transport suggestion based on budget
$transportSuggestion = [];
if ($tc === 'Low') {
    $transportSuggestion = [
        "name" => "Budget: Bus or Shared Train",
        "notes" => "Your budget is low, use bus or train. It seems hurry during night trip because of low trafic jam",
        "estimate" => "Approx BDT 800 - 1500 (one-way, as route & operator )"
    ];
} elseif ($tc === 'Medium') {
    $transportSuggestion = [
        "name" => "Medium: AC bus / private car rental (shared)",
        "notes" => "Comfortable bus or mini privet van. Adventage and cost are medium.",
        "estimate" => "Approx BDT 1500 - 4000"
    ];
} else {
    $transportSuggestion = [
        "name" => "High: Flight or private car with driver",
        "notes" => "If you want to save time, use flight. In privet vehicles, no need of extra carring cost and carring become easy.",
        "estimate" => "Flight: BDT 4500+ (season/advance, as ticket) ; Private car: varries on cost."
    ];
}

// Hotel suggestion
$hotelSuggestion = [];
if ($hc === 'Low') {
    $hotelSuggestion = [
        "name" => "Budget hotels / guesthouses",
        "notes" => "সাধারণত নির্দিষ্ট সুবিধা থাকবে (কখনো কখনো শেয়ার বাথরুম)।",
        "estimate" => "BDT 800 - 3000 per night"
    ];
} elseif ($hc === 'Medium') {
    $hotelSuggestion = [
        "name" => "3★ - 4★ hotels / comfortable guesthouses",
        "notes" => "আরামদায়ক রুম, হোটেল সুবিধা (বাফে ব্রেকফাস্ট, ওয়াইফাই)।",
        "estimate" => "BDT 3000 - 10000 per night"
    ];
} else {
    $hotelSuggestion = [
        "name" => "Premium hotels / resorts",
        "notes" => "লাক্সারি রুম, ফুল সার্ভিস — উপকূলীয় বা সিনারির উপর ভিত্তি করে প্রাইস বাড়ে।",
        "estimate" => "BDT 10000+ per night"
    ];
}

// Transport advice short text
$transportAdvice = ($tc === 'Low') ? 'Budget travel recommended (bus/train). Expect longer travel time.' : (($tc === 'Medium') ? 'Balanced cost and comfort.' : 'Faster & more comfort; flying is option.');

// General tips:
$tips = [
    "Major highways (e.g. N1 to Cox's Bazar) carry most inter-city buses; holidays and peak weekends see higher congestion. (Consider night departure if comfortable.)",
    "Advance-book bus/flight tickets on Shohoz/BDTickets/airlines to get better prices.",
    "For private car travel, check local roadworks and weather (monsoon may affect hilly routes).",
    "If you need real-time low-traffic routing, integrate Google Maps / Waze API for live traffic."
];

$response = [
    "destination" => $destination,
    "transportCategory" => $tc,
    "hotelCategory" => $hc,
    "transportAdvice" => $transportAdvice,
    "routes" => $routes,
    "transportSuggestion" => $transportSuggestion,
    "hotelSuggestion" => $hotelSuggestion,
    "tips" => $tips
];

echo json_encode($response, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
