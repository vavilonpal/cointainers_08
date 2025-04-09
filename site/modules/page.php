<?php

class Page {
    private $template;

    // Конструктор: принимает путь к файлу шаблона
    public function __construct($template) {
        if (!file_exists($template)) {
            die("Шаблон не найден: $template");
        }
        $this->template = file_get_contents($template);
    }

    // Метод Render: подставляет данные в шаблон и отображает страницу
    public function Render($data) {
        $output = $this->template;

        foreach ($data as $key => $value) {
            // Заменяем {{ключ}} в шаблоне на значение
            $output = str_replace('{{' . $key . '}}', htmlspecialchars($value), $output);
        }

        echo $output;
    }
}

?>
