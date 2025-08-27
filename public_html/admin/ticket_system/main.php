<?php
// Admin Ticket System - Main Include File
// Include the admin main file for standard admin functionality
include_once '../assets/includes/main.php';

// Add ticket-specific function if needed
if (!function_exists('color_from_string')) {
    function color_from_string($string) {
        // The list of hex colors
        $colors = ['#34568B','#FF6F61','#6B5B95','#88B04B','#F7CAC9','#92A8D1','#955251','#B565A7','#009B77','#DD4124','#D65076','#45B8AC','#EFC050','#5B5EA6','#9B2335','#DFCFBE','#BC243C','#C3447A','#363945','#939597','#E0B589','#926AA6','#0072B5','#E9897E','#B55A30','#4B5335','#798EA4','#00758F','#FA7A35','#6B5876','#B89B72','#282D3C','#C48A69','#A2242F','#006B54','#6A2E2A','#6C244C','#755139','#615550','#5A3E36','#264E36','#577284','#6B5B95','#944743','#00A591','#6C4F3D','#BD3D3A','#7F4145','#485167','#5A7247','#D2691E','#F7786B','#91A8D0','#4C6A92','#838487','#AD5D5D','#006E51','#9E4624'];
        // Find color based on the string
        $colorIndex = hexdec(substr(sha1($string), 0, 10)) % count($colors);
        // Return the hex color
        return $colors[$colorIndex];
    }
}
?>