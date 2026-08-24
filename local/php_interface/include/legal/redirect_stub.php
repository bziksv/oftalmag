<?php

require_once __DIR__ . '/redirects.php';
oftalmagLegalRedirectPerform($_SERVER['REQUEST_URI'] ?? '/');
