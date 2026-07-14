# OSP Importer für Projekt-Root

Installation im Bedrock-Projekt-Root:

```bash
unzip -o /PFAD/ZU/bbc-osp-importer-root-20260714.zip -d ~/sites/bbc-stage
```

Aufruf:

```bash
cd ~/sites/bbc-stage && node tools/osp-importer.mjs
```

Dry-Run:

```bash
cd ~/sites/bbc-stage && node tools/osp-importer.mjs --dry-run
```

Watch-Modus:

```bash
cd ~/sites/bbc-stage && node tools/osp-importer.mjs --watch
```

Unterstützte ZIP-Strukturen:

- Theme des Projekts: `resources/...`, `app/...` oder `theme/...`
- Benanntes Theme: `themes/THEME-NAME/...`
- Plugins: `plugins/PLUGIN-NAME/...`
- MU-Plugins: `mu-plugins/PLUGIN-NAME/...`
- Projektdateien: `project/tools/...` oder `project/docs/...`
- Vollständige Bedrock-Pfade: `web/app/themes/...`, `web/app/plugins/...`, `web/app/mu-plugins/...`

Umgebungsvariablen:

- `OSP_PROJECT_ROOT`
- `OSP_THEME_NAME`
- `OSP_THEME_ROOT`
- `OSP_IMPORT_INBOX`
- `OSP_IMPORT_BACKUPS`
- `OSP_IMPORT_STAGING`
- `OSP_IMPORT_MAX_FILE_BYTES`

Der Importer verwendet im normalen Betrieb keine npm-Abhängigkeiten. ZIP-Dateien werden über das Systemprogramm `unzip` verarbeitet, sofern `adm-zip` nicht vorhanden ist.
