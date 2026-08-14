<?php
declare(strict_types=1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// API: Get list of config files
if ($_SERVER['REQUEST_URI'] === '/api/config-files' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $configDirs = [
        __DIR__ . '/../data/configurations',
        __DIR__ . '/../app/configurations'
    ];
    
    $files = [];
    foreach ($configDirs as $dir) {
        if (is_dir($dir)) {
            $dirFiles = scandir($dir);
            foreach ($dirFiles as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_file($filePath)) {
                    $files[] = [
                        'name' => $file,
                        'path' => $filePath,
                        'relative' => str_replace(__DIR__ . '/../', '', $filePath)
                    ];
                }
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['files' => $files]);
    exit;
}

// API: Get a specific config file
if (preg_match('~^/api/config/(.+)$~', $_SERVER['REQUEST_URI'], $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $relativePath = urldecode($matches[1]);
    $filePath = __DIR__ . '/../' . $relativePath;
    
    // Security check: ensure the file is within the project directory
    $realPath = realpath($filePath);
    $projectRoot = realpath(__DIR__ . '/../');
    if (strpos($realPath, $projectRoot) !== 0) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Access denied']);
        exit;
    }
    
    if (!is_file($realPath)) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'File not found']);
        exit;
    }
    
    $config = json_decode(file_get_contents($realPath), true);
    if ($config === null) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode(['config' => $config]);
    exit;
}

// API: Save a config file
if ($_SERVER['REQUEST_URI'] === '/save-config' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $configFilePath = $_POST['config_file_path'] ?? '';
    $configJson = $_POST['config_json'] ?? '';
    
    if (empty($configFilePath) || empty($configJson)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit;
    }
    
    $filePath = __DIR__ . '/../' . $configFilePath;
    
    // Security check: ensure the file is within the project directory
    $realPath = realpath($filePath);
    $projectRoot = realpath(__DIR__ . '/../');
    if (strpos($realPath, $projectRoot) !== 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Access denied']);
        exit;
    }
    
    // Validate JSON
    $config = json_decode($configJson, true);
    if ($config === null) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit;
    }
    
    // Ensure directory exists
    $dir = dirname($realPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Save file
    if (file_put_contents($realPath, $configJson) === false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Failed to save file']);
        exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// If none of the above, continue to the main application
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Setup.php';

use App\StepFunction;
use App\FinTsFactory;
use App\ConfigurationFactory;
use App\TanHandler;
use App\TransactionsToFireflySender;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Step;
use GrumpyDictator\FFIIIApiSupport\Request\GetAccountsRequest;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../app/public/html');
$twig   = new \Twig\Environment($loader);
$automate_without_js = false;

$request = Request::createFromGlobals();

$current_step = new Step($request->request->get('step', Step::STEP0_SETUP));

$session = new Session();
$session->start();

if (isset($_GET['automate'])) {
    $automate_without_js = $_GET['automate'] == "true";
}

do
{
    switch ((string)$current_step) {
        case Step::STEP0_SETUP:
            $current_step = StepFunction\Setup();
            break;

        case Step::STEP1_COLLECTING_DATA:
            $current_step = StepFunction\CollectData();
            break;

        case Step::STEP1p5_CHOOSE_2FA_DEVICE:
            $current_step = StepFunction\Choose2FADevice();
            break;

        case Step::STEP2_LOGIN:
            $current_step = StepFunction\Login();
            break;

        case Step::STEP3_CHOOSE_ACCOUNT:
            $current_step = StepFunction\ChooseAccount();
            break;

        case Step::STEP4_GET_IMPORT_DATA:
            $current_step = StepFunction\GetImportData();
            break;

        case Step::STEP5_RUN_IMPORT_BATCHED:
            $current_step = StepFunction\RunImportBatched();
            break;

        default:
            $current_step = Step::DONE;
            break;
    }
} while ($current_step != Step::DONE);