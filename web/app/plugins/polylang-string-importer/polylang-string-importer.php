<?php

/**
 * Plugin Name: Polylang String Importer
 * Description: Importiert Polylang-String-Übersetzungen aus einer CSV-Datei im Plugin-Verzeichnis.
 * Version: 1.0.2
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

final class Polylang_String_Importer
{
    private const PAGE_SLUG = 'polylang-string-importer';
    private const META_KEY = '_pll_strings_translations';
    private const IMPORT_FILE = __DIR__ . '/imports/translations.csv';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerAdminPage'], 99);
        add_action('admin_post_polylang_string_importer_run', [$this, 'runImport']);
    }

    public function registerAdminPage(): void
    {
        add_management_page(
            'Polylang String-Import',
            'Polylang String-Import',
            'manage_options',
            self::PAGE_SLUG,
            [$this, 'renderAdminPage']
        );
    }

    public function renderAdminPage(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $notice = isset($_GET['psi_notice'])
            ? sanitize_key(wp_unslash($_GET['psi_notice']))
            : '';
        $message = isset($_GET['psi_message'])
            ? sanitize_text_field(wp_unslash($_GET['psi_message']))
            : '';
        $fileReadable = is_readable(self::IMPORT_FILE);
        $languages = $this->getLanguages();
        ?>
        <div class="wrap">
            <h1>Polylang String-Import</h1>

            <?php if ('' !== $message): ?>
                <div class="notice <?php echo 'error' === $notice ? 'notice-error' : 'notice-success'; ?> is-dismissible">
                    <p><?php echo esc_html($message); ?></p>
                </div>
            <?php endif; ?>

            <table class="widefat striped" style="max-width: 1000px; margin: 20px 0;">
                <tbody>
                    <tr>
                        <th style="width: 220px;">Importdatei</th>
                        <td><code><?php echo esc_html(self::IMPORT_FILE); ?></code></td>
                    </tr>
                    <tr>
                        <th>Dateistatus</th>
                        <td><?php echo $fileReadable ? 'Datei gefunden und lesbar' : 'Datei fehlt oder ist nicht lesbar'; ?></td>
                    </tr>
                    <tr>
                        <th>Erkannte Sprachen</th>
                        <td>
                            <?php
                            echo esc_html(
                                implode(
                                    ', ',
                                    array_map(
                                        static fn(array $language): string => $language['name'] . ' (' . $language['slug'] . ')',
                                        $languages
                                    )
                                )
                            );
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p>CSV-Kopfzeile: <code>source;de;en</code>. Die Sprachspalten müssen den Polylang-Sprachkürzeln entsprechen.</p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="polylang_string_importer_run">
                <?php wp_nonce_field('polylang_string_importer_run', 'polylang_string_importer_nonce'); ?>

                <fieldset style="margin: 20px 0;">
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="checkbox" name="overwrite" value="1" checked>
                        Vorhandene Übersetzungen überschreiben
                    </label>
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="checkbox" name="validate_placeholders" value="1" checked>
                        Platzhalter vor dem Import prüfen
                    </label>
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="checkbox" name="create_backup" value="1" checked>
                        Vor dem Import ein Backup im Upload-Verzeichnis erstellen
                    </label>
                </fieldset>

                <?php
                submit_button(
                    'Import starten',
                    'primary',
                    'submit',
                    false,
                    $fileReadable && [] !== $languages ? [] : ['disabled' => 'disabled']
                );
                ?>
            </form>
        </div>
        <?php
    }

    public function runImport(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Keine Berechtigung.');
        }

        check_admin_referer('polylang_string_importer_run', 'polylang_string_importer_nonce');

        try {
            if (!is_readable(self::IMPORT_FILE)) {
                throw new RuntimeException('Die Importdatei wurde nicht gefunden oder ist nicht lesbar.');
            }

            $languages = $this->getLanguages();

            if ([] === $languages) {
                throw new RuntimeException('Polylang ist nicht aktiv oder es wurden keine Sprachen gefunden.');
            }

            $rows = $this->readCsv(self::IMPORT_FILE);

            if ([] === $rows) {
                throw new RuntimeException('Die CSV-Datei enthält keine importierbaren Zeilen.');
            }

            $overwrite = isset($_POST['overwrite']);
            $validatePlaceholders = isset($_POST['validate_placeholders']);
            $createBackup = isset($_POST['create_backup']);

            if ($createBackup) {
                $this->createBackup($languages);
            }

            $result = $this->importRows($rows, $languages, $overwrite, $validatePlaceholders);

            $message = sprintf(
                '%d Übersetzungen importiert, %d unverändert, %d übersprungen, %d Fehler.',
                $result['imported'],
                $result['unchanged'],
                $result['skipped'],
                $result['errors']
            );

            $this->redirect('success', $message);
        } catch (Throwable $exception) {
            $this->redirect('error', $exception->getMessage());
        }
    }

    private function getLanguages(): array
    {
        $terms = get_terms([
            'taxonomy' => 'language',
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms) || !is_array($terms)) {
            return [];
        }

        $languages = [];

        foreach ($terms as $term) {
            if (!$term instanceof WP_Term) {
                continue;
            }

            $languages[(string) $term->slug] = [
                'slug' => (string) $term->slug,
                'name' => (string) $term->name,
                'term_id' => (int) $term->term_id,
            ];
        }

        return $languages;
    }

    private function readCsv(string $file): array
    {
        $handle = fopen($file, 'rb');

        if (false === $handle) {
            throw new RuntimeException('Die CSV-Datei konnte nicht geöffnet werden.');
        }

        $firstLine = fgets($handle);

        if (false === $firstLine) {
            fclose($handle);
            return [];
        }

        $delimiter = $this->detectDelimiter($firstLine);
        rewind($handle);

        $header = fgetcsv($handle, 0, $delimiter);

        if (!is_array($header)) {
            fclose($handle);
            return [];
        }

        $header = array_map(
            static function (mixed $value): string {
                $value = preg_replace('/^\xEF\xBB\xBF/', '', (string) $value);
                return trim($value);
            },
            $header
        );

        if (!in_array('source', $header, true)) {
            fclose($handle);
            throw new RuntimeException('Die CSV-Kopfzeile benötigt eine Spalte namens "source".');
        }

        $rows = [];
        $line = 1;

        while (($values = fgetcsv($handle, 0, $delimiter)) !== false) {
            ++$line;

            if ([null] === $values || [] === $values) {
                continue;
            }

            if (count($values) !== count($header)) {
                $rows[] = ['__error' => 'Ungültige Spaltenanzahl in Zeile ' . $line];
                continue;
            }

            $row = array_combine($header, $values);

            if (is_array($row)) {
                $row['__line'] = $line;
                $rows[] = $row;
            }
        }

        fclose($handle);

        return $rows;
    }

    private function detectDelimiter(string $line): string
    {
        $delimiters = [';' => 0, ',' => 0, "\t" => 0];

        foreach ($delimiters as $delimiter => $count) {
            $delimiters[$delimiter] = substr_count($line, $delimiter);
        }

        arsort($delimiters);

        return (string) array_key_first($delimiters);
    }

    private function importRows(array $rows, array $languages, bool $overwrite, bool $validatePlaceholders): array
    {
        $catalogs = [];
        $changed = [];
        $result = [
            'imported' => 0,
            'unchanged' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        foreach ($languages as $slug => $language) {
            $catalogs[$slug] = $this->loadCatalog($language['term_id']);
            $changed[$slug] = false;
        }

        foreach ($rows as $row) {
            if (isset($row['__error'])) {
                ++$result['errors'];
                continue;
            }

            $source = isset($row['source']) ? (string) $row['source'] : '';

            if ('' === trim($source)) {
                ++$result['skipped'];
                continue;
            }

            foreach ($languages as $slug => $language) {
                if (!array_key_exists($slug, $row)) {
                    continue;
                }

                $translation = (string) $row[$slug];

                if ('' === trim($translation)) {
                    ++$result['skipped'];
                    continue;
                }

                if ($validatePlaceholders && !$this->placeholdersMatch($source, $translation)) {
                    ++$result['errors'];
                    continue;
                }

                $existing = $catalogs[$slug][$source] ?? null;

                if (null !== $existing && !$overwrite) {
                    ++$result['skipped'];
                    continue;
                }

                if ($existing === $translation) {
                    ++$result['unchanged'];
                    continue;
                }

                $catalogs[$slug][$source] = $translation;
                $changed[$slug] = true;
                ++$result['imported'];
            }
        }

        foreach ($languages as $slug => $language) {
            if (!$changed[$slug]) {
                continue;
            }

            $this->saveCatalog($language['term_id'], $catalogs[$slug]);
        }

        delete_transient('pll_languages_list');
        wp_cache_flush();

        return $result;
    }

    private function loadCatalog(int $termId): array
    {
        $stored = get_term_meta($termId, self::META_KEY, true);

        if (!is_array($stored)) {
            return [];
        }

        $catalog = [];

        foreach ($stored as $entry) {
            if (!is_array($entry) || !array_key_exists(0, $entry)) {
                continue;
            }

            $source = (string) $entry[0];
            $translation = array_key_exists(1, $entry) ? (string) $entry[1] : '';
            $catalog[$source] = $translation;
        }

        return $catalog;
    }

    private function saveCatalog(int $termId, array $catalog): void
    {
        $stored = [];

        foreach ($catalog as $source => $translation) {
            $stored[] = [(string) $source, (string) $translation];
        }

        $result = update_term_meta($termId, self::META_KEY, $stored);

        if (is_wp_error($result)) {
            throw new RuntimeException($result->get_error_message());
        }

        clean_term_cache($termId);
        wp_cache_delete($termId, 'term_meta');
    }

    private function createBackup(array $languages): void
    {
        $upload = wp_upload_dir();

        if (!empty($upload['error'])) {
            throw new RuntimeException('Backup-Verzeichnis konnte nicht ermittelt werden: ' . $upload['error']);
        }

        $directory = trailingslashit($upload['basedir']) . 'polylang-string-importer';

        if (!wp_mkdir_p($directory)) {
            throw new RuntimeException('Backup-Verzeichnis konnte nicht erstellt werden.');
        }

        $backup = [
            'created_at' => current_time('mysql'),
            'site_url' => home_url('/'),
            'meta_key' => self::META_KEY,
            'languages' => [],
        ];

        foreach ($languages as $slug => $language) {
            $backup['languages'][$slug] = [
                'term_id' => $language['term_id'],
                'name' => $language['name'],
                'data' => get_term_meta($language['term_id'], self::META_KEY, true),
            ];
        }

        $filename = trailingslashit($directory) . 'backup-' . gmdate('Ymd-His') . '.json';
        $json = wp_json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (false === $json || false === file_put_contents($filename, $json, LOCK_EX)) {
            throw new RuntimeException('Backup-Datei konnte nicht geschrieben werden.');
        }
    }

    private function placeholdersMatch(string $source, string $translation): bool
    {
        return $this->extractPlaceholders($source) === $this->extractPlaceholders($translation);
    }

    private function extractPlaceholders(string $value): array
    {
        $patterns = [
            '/%(?:\d+\$)?[-+0\' #]*\d*(?:\.\d+)?[bcdeEfFgGosuxX]/',
            '/\{\{\s*.*?\s*\}\}/s',
            '/\{!!\s*.*?\s*!!\}/s',
            '/(?<!:):[A-Za-z_][A-Za-z0-9_]*/',
        ];
        $placeholders = [];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $value, $matches);

            foreach ($matches[0] ?? [] as $match) {
                $placeholders[] = preg_replace('/\s+/', ' ', trim((string) $match));
            }
        }

        sort($placeholders);

        return $placeholders;
    }

    private function redirect(string $notice, string $message): never
    {
        wp_safe_redirect(
            add_query_arg(
                [
                    'page' => self::PAGE_SLUG,
                    'psi_notice' => $notice,
                    'psi_message' => $message,
                ],
                admin_url('tools.php')
            )
        );

        exit;
    }
}

new Polylang_String_Importer();
