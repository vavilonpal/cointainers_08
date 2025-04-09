<?php

require_once __DIR__ . '/testframework.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modules/database.php';
require_once __DIR__ . '/../modules/page.php';

$testFramework = new TestFramework();
$config = require __DIR__ . '/../config.php';

// Вспомогательная функция: создание тестовой таблицы
function setupTestTable($db) {
    $db->Execute("DROP TABLE IF EXISTS test");
    $db->Execute("CREATE TABLE test (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, age INTEGER)");
}

// test 1: проверка подключения к базе данных
function testDbConnection() {
    global $config;
    $db = new Database($config['database']['path']);
    return $db instanceof Database;
}

// test 2: проверка метода Count()
function testDbCount() {
    global $config;
    $db = new Database($config['database']['path']);
    setupTestTable($db);
    return $db->Count('test') === 0;
}

// test 3: проверка метода Create()
function testDbCreate() {
    global $config;
    $db = new Database($config['database']['path']);
    setupTestTable($db);
    $id = $db->Create('test', ['name' => 'Alex', 'age' => 30]);
    return is_numeric($id) && $id > 0;
}

// test 4: проверка метода Read()
function testDbRead() {
    global $config;
    $db = new Database($config['database']['path']);
    setupTestTable($db);
    $id = $db->Create('test', ['name' => 'Alex', 'age' => 30]);
    $row = $db->Read('test', $id);
    return $row['name'] === 'Alex' && $row['age'] == 30;
}

// test 5: проверка метода Update()
function testDbUpdate() {
    global $config;
    $db = new Database($config['database']['path']);
    setupTestTable($db);
    $id = $db->Create('test', ['name' => 'Old Name', 'age' => 25]);
    $db->Update('test', $id, ['name' => 'New Name', 'age' => 26]);
    $row = $db->Read('test', $id);
    return $row['name'] === 'New Name' && $row['age'] == 26;
}

// test 6: проверка метода Delete()
function testDbDelete() {
    global $config;
    $db = new Database($config['database']['path']);
    setupTestTable($db);
    $id = $db->Create('test', ['name' => 'Delete Me', 'age' => 20]);
    $db->Delete('test', $id);
    $row = $db->Read('test', $id);
    return $row === false;
}

// test 7: проверка метода Fetch()
function testDbFetch() {
    global $config;
    $db = new Database($config['database']['path']);
    setupTestTable($db);
    $db->Create('test', ['name' => 'Fetch Test', 'age' => 28]);
    $rows = $db->Fetch("SELECT * FROM test WHERE name = 'Fetch Test'");
    return count($rows) === 1 && $rows[0]['age'] == 28;
}

// test 8: проверка метода Execute()
function testDbExecute() {
    global $config;
    $db = new Database($config['database']['path']);
    $result = $db->Execute("CREATE TABLE IF NOT EXISTS exec_test (id INTEGER PRIMARY KEY)");
    return $result !== false;
}

// test 9: проверка конструктора Page и метода Render()
function testPageRender() {
    $templatePath = __DIR__ . '/../templates/test.tpl';
    file_put_contents($templatePath, '<h1>{{title}}</h1><p>{{message}}</p>');

    $page = new Page($templatePath);
    ob_start();
    $page->Render([
        'title' => 'Test Title',
        'message' => 'Test Message'
    ]);
    $output = ob_get_clean();

    unlink($templatePath); // Удалим тестовый шаблон

    return strpos($output, 'Test Title') !== false && strpos($output, 'Test Message') !== false;
}

// Добавление тестов
$testFramework->add('Database connection', 'testDbConnection');
$testFramework->add('Database count()', 'testDbCount');
$testFramework->add('Database create()', 'testDbCreate');
$testFramework->add('Database read()', 'testDbRead');
$testFramework->add('Database update()', 'testDbUpdate');
$testFramework->add('Database delete()', 'testDbDelete');
$testFramework->add('Database fetch()', 'testDbFetch');
$testFramework->add('Database execute()', 'testDbExecute');
$testFramework->add('Page render()', 'testPageRender');

// Запуск тестов
$testFramework->run();

// Вывод результатов
echo $testFramework->getResult();

