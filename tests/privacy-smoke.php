<?php
/**
 * SearchRank 1.4.0 isolated privacy/service smoke checks.
 * Extracts the canonical function bodies from functions.inc without starting
 * a Geeklog instance or touching a real database.
 */
$source = file_get_contents(__DIR__ . '/../functions.inc');
foreach (array('SEARCHRANK_isSensitiveQuery', 'SEARCHRANK_serviceAuthorized') as $name) {
    $pattern = '/function ' . preg_quote($name, '/') . '\\([^)]*\\)\\s*\\{.*?^\\}/ms';
    if (!preg_match($pattern, $source, $match)) {
        fwrite(STDERR, 'Missing function: ' . $name . PHP_EOL);
        exit(1);
    }
    eval($match[0]);
}
function SEC_hasRights($right) { return false; }
$bad = array(
    'alice@example.com',
    'password=SuperSecret123',
    'api_key: abcd1234',
    'Authorization: Bearer abcdef1234567890',
    'https://user:secret@example.org/',
    '4111 1111 1111 1111',
    'ghp_abcdefghijklmnopqrstuvwxyz12345'
);
$good = array('libreoffice writer', 'how to create a pdf', 'version 26.8', '2026', 'invoice template');
foreach ($bad as $q) {
    if (!SEARCHRANK_isSensitiveQuery($q)) {
        fwrite(STDERR, 'Expected sensitive: ' . $q . PHP_EOL);
        exit(1);
    }
}
foreach ($good as $q) {
    if (SEARCHRANK_isSensitiveQuery($q)) {
        fwrite(STDERR, 'Unexpected sensitive: ' . $q . PHP_EOL);
        exit(1);
    }
}
if (SEARCHRANK_serviceAuthorized(array('gl_svc' => true)) ||
    SEARCHRANK_serviceAuthorized(array('gl_svc' => false))) {
    fwrite(STDERR, 'Service permission gate failed' . PHP_EOL);
    exit(1);
}
echo "Privacy and authorization checks passed" . PHP_EOL;
