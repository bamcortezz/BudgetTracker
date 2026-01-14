<?php

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

// Error reporting - log errors but don't display them to avoid breaking JSON responses
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1); // Log errors instead
ini_set('error_log', __DIR__ . '/../config/error.log'); // Log to file

// Set timezone (adjust as needed)
date_default_timezone_set('UTC');