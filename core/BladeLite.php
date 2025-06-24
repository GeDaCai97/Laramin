<?php

namespace Core;

class BladeLite
{
    protected string $viewPath;
    protected array $sections = [];
    protected string $parentLayout = '';
    protected array $rawSections = [];
    protected string $cachePath;

    public function __construct(string $viewPath, string $cachePath)
    {
        $this->viewPath = rtrim($viewPath, '/');
        $this->cachePath = rtrim($cachePath, '/');
    }

    public function render(string $view, array $data = [])
    {
        $viewPath = $this->viewPath . '/' . str_replace('.', '/', $view) . '.blade.php';
        $compiledFile = $this->cachePath . '/' . md5($view) . '.php';

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }

        // Llama a compile para que se regenere si es necesario
        $compiledFile = $this->compile($view);

        extract($data);
        include $compiledFile;
    }

    protected function compile(string $view): string
    {
        $viewFile = $this->viewPath . '/' . str_replace('.', '/', $view) . '.blade.php';
        $compiledFile = $this->cachePath . '/' . md5($view) . '.php';

        // Cargar contenido de la vista
        if (!file_exists($viewFile)) {
            throw new \Exception("La vista '$view' no existe.");
        }

        $template = file_get_contents($viewFile);

        // Detectar layout padre
        $template = $this->processExtends($template);

        // Detectar includes
        $includes = $this->getIncludesRecursive($view);

        // Layout padre también se verifica
        if ($this->parentLayout) {
            $includes[] = $this->parentLayout;
        }

        // Determinar si se debe recompilar
        $shouldCompile = !file_exists($compiledFile) || filemtime($viewFile) > filemtime($compiledFile);

        foreach ($includes as $includeView) {
            $includePath = $this->viewPath . '/' . str_replace('.', '/', $includeView) . '.blade.php';
            if (file_exists($includePath) && filemtime($includePath) > filemtime($compiledFile)) {
                $shouldCompile = true;
                break;
            }
        }

        // Compilar si es necesario
        if ($shouldCompile) {
            if (file_exists($compiledFile)) {
                unlink($compiledFile); // Borra el archivo cacheado anterior
            }

            // Procesar layout
            $template = $this->processExtends($template);

            // Procesar secciones y directivas
            $compiled = $this->compileString($template);

            // Si hay layout, procesarlo con las secciones
            if ($this->parentLayout) {
                $compiled = $this->injectIntoLayout($this->parentLayout, $compiled);
            }

            file_put_contents($compiledFile, $compiled);
        }

        return $compiledFile;
    }

    protected function processExtends(string $template): string
    {
        if (preg_match('/@extends\([\'"](.+)[\'"]\)/', $template, $match)) {
            $this->parentLayout = $match[1];
            $template = str_replace($match[0], '', $template);
        }
        return $template;
    }

    protected function compileString(string $template): string
    {
        // Captura de secciones simples @section('title', 'Texto')
        $template = preg_replace_callback(
            '/@section\([\'"](.+)[\'"],\s*[\'"](.+)[\'"]\)/',
            function ($matches) {
                $this->sections[$matches[1]] = $matches[2];
                return '';
            },
            $template
        );

        // Captura de bloques @section() ... @endsection
        $template = preg_replace_callback(
            '/@section\([\'"](.+)[\'"]\)(.*?)@endsection/s',
            function ($matches) {
                $this->sections[$matches[1]] = $matches[2];
                return '';
            },
            $template
        );

        // Variables {{ }}
        $template = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?= htmlspecialchars($1) ?>', $template);

        // Directivas
        $patterns = [
            '/@if\s*\((.+?)\)/'      => '<?php if ($1): ?>',
            '/@elseif\s*\((.+?)\)/'  => '<?php elseif ($1): ?>',
            '/@else/'                => '<?php else: ?>',
            '/@endif/'               => '<?php endif; ?>',
            '/@foreach\s*\((.+?)\)/' => '<?php foreach ($1): ?>',
            '/@endforeach/'          => '<?php endforeach; ?>',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $template = preg_replace($pattern, $replacement, $template);
        }

        // Reemplazo personalizado para @include
        $template = preg_replace_callback(
            '/@include\([\'"](.+?)[\'"]\)/',
            function ($matches) {
                $view = $matches[1];
                $viewFile = $this->viewPath . '/' . str_replace('.', '/', $view) . '.blade.php';
                $compiledFile = $this->cachePath . '/' . md5($view) . '.php';

                // Verifica si se debe recompilar el include
                $shouldRecompile = !file_exists($compiledFile)
                    || (file_exists($viewFile) && filemtime($viewFile) > filemtime($compiledFile));

                if ($shouldRecompile) {
                    if (file_exists($viewFile)) {
                        $templateIncluded = file_get_contents($viewFile);
                        $compiledContent = $this->compileString($templateIncluded);
                        file_put_contents($compiledFile, $compiledContent);
                    } else {
                        return "<?php /* Archivo incluido no encontrado: $view */ ?>";
                    }
                }

                return "<?php include '$compiledFile'; ?>";
            },
            $template
        );

        return $template;
    }

    protected function injectIntoLayout(string $layout, string $compiled): string
    {
        $layoutFile = $this->viewPath . '/' . str_replace('.', '/', $layout) . '.blade.php';
        $layoutContent = file_get_contents($layoutFile);

        // Reemplazar @yield con el contenido de secciones
        $layoutContent = preg_replace_callback(
            '/@yield\([\'"](.+?)[\'"]\)/',
            fn($m) => $this->sections[$m[1]] ?? '',
            $layoutContent
        );

        return $this->compileString($layoutContent);
    }

    protected function getIncludesRecursive(string $view): array
    {
        $checked = [];
        $toCheck = [$view];
        $found = [];

        while (!empty($toCheck)) {
            $current = array_pop($toCheck);
            if (in_array($current, $checked)) continue;
            $checked[] = $current;

            $path = $this->viewPath . '/' . str_replace('.', '/', $current) . '.blade.php';
            if (!file_exists($path)) continue;

            $content = file_get_contents($path);

            // Buscar includes
            preg_match_all('/@include\([\'"](.+?)[\'"]\)/', $content, $matches);
            foreach ($matches[1] as $inc) {
                $found[] = $inc;
                $toCheck[] = $inc;
            }

            // Buscar layout padre
            if (preg_match('/@extends\([\'"](.+?)[\'"]\)/', $content, $layoutMatch)) {
                $layout = $layoutMatch[1];
                $found[] = $layout;
                $toCheck[] = $layout;
            }
        }

        return array_unique($found);
    }
}