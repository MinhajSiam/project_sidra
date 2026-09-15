<?php

declare(strict_types=1);

namespace App\Core;

class View {
    public static function render(string $viewPath, array $data = [], ?string $layout = null): string {
        $viewFile = self::resolvePath($viewPath);
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View template not found: {$viewPath} ({$viewFile})");
        }

        // Global view data
        $systemSettings = self::getSystemSettings();
        $globalData = [
            'appName' => $systemSettings['platform_name'] ?? config('app.name', 'Sidra Event Platform'),
            'authUser' => Auth::user(),
            'authCustomer' => Auth::customer(),
            'flashSuccess' => Session::getFlash('success'),
            'flashError' => Session::getFlash('error'),
            'flashWarning' => Session::getFlash('warning'),
            'flashInfo' => Session::getFlash('info'),
            'errors' => Session::getFlash('errors', []),
            'settings' => $systemSettings,
        ];

        $mergedData = array_merge($globalData, $data);
        extract($mergedData, EXTR_SKIP);

        // Render view content
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Check if view defined a layout variable or layout parameter was passed
        $activeLayout = $layout ?? ($data['layout'] ?? ($layoutName ?? null));

        if ($activeLayout) {
            $layoutFile = self::resolvePath($activeLayout);
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout not found: {$activeLayout} ({$layoutFile})");
            }
            ob_start();
            include $layoutFile;
            return ob_get_clean();
        }

        return $content;
    }

    private static function resolvePath(string $dotNotation): string {
        $relativePath = str_replace('.', DIRECTORY_SEPARATOR, $dotNotation);
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . $relativePath . '.php';
    }

    private static function getSystemSettings(): array {
        static $settings = null;
        if ($settings === null) {
            try {
                $rows = Database::fetchAll("SELECT setting_key, setting_value FROM system_settings");
                $settings = [];
                foreach ($rows as $row) {
                    $settings[$row['setting_key']] = $row['setting_value'];
                }
            } catch (\Throwable) {
                $settings = [];
            }
        }
        return $settings;
    }
}
