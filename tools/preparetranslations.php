<?php

/**
 * MultiFlexi - Export Language strings
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2020 Vitex Software
 */

namespace MultiFlexi;

require_once '../vendor/autoload.php';
\Ease\Shared::init(['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'], '../.env');
define('EASE_LOGGER', 'console');

$apper = new Application();
$texts = $apper->listingQuery()->select(['name', 'description'], true)->fetchAll();

$configer = new Conffield();
$confs = $configer->listingQuery()->select(['description'], true)->fetchAll();

$translateFileContents = "<?php\n\n";

$translateFileContents .= "/**\n";
$translateFileContents .= " * MultiFlexi - Generated i18n translations\n";
$translateFileContents .= " *\n";
$translateFileContents .= " * @author Vítězslav Dvořák <info@vitexsoftware.cz>\n";
$translateFileContents .= " * @copyright  " . date('Y') . " Vitex Software\n";
$translateFileContents .= " */\n\n";

$translations = [];

foreach (array_merge($texts, $confs) as $text) {
    if (isset($text['name']) && !isset($translations[$text['name']])) {
        $translateFileContents .= "_('" . str_replace("'", "\'", $text['name']) . "');\n";
        $translations[$text['name']] = true;
    }
    if ($text['description'] && !isset($translations[$text['description']])) {
        $translateFileContents .= "_('" . str_replace("'", "\'", $text['description']) . "');\n";
        $translations[$text['description']] = true;
    }
}

file_put_contents('../src/translations.php', $translateFileContents);
$apper->addStatusMessage(sprintf(count($translations) === 1 ? LANG_STRING_EXPORTED_SINGULAR : LANG_STRING_EXPORTED_PLURAL, count($translations)), 'success');
