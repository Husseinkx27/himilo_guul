<?php
// Simple server-side validators
function is_valid_email($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function sanitize($value){
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Add more validators (password strength, phone, etc.) as needed
?>