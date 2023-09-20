<?php

if (str_ends_with(parse_url($_SERVER['REQUEST_URI'])['path'], "ErrorMessage.php")) {
    http_response_code(404);
    die();
}

class ErrorMessage implements Component {
    private string $message;
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function render()
    {
        return <<<HTML
            <span class="text-red-400 text-lg">{$this->message}</span>
        HTML;
    }
}