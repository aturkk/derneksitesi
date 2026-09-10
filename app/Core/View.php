<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    /**
     * app/Views/{template}.php dosyasını çalıştırır; layout verilirse çıktıyı
     * $content değişkeniyle yerleşim içine koyup döndürür.
     */
    public static function render(string $template, array $data = [], ?string $layout = null): string
    {
        $file = BASE_PATH . '/app/Views/' . $template . '.php';
        if (!is_file($file)) {
            throw new RuntimeException("Görünüm bulunamadı: $template");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        $content = ob_get_clean();

        if ($layout !== null) {
            $layoutFile = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
            if (!is_file($layoutFile)) {
                throw new RuntimeException("Yerleşim bulunamadı: $layout");
            }
            ob_start();
            require $layoutFile;
            return ob_get_clean();
        }

        return $content;
    }
}
