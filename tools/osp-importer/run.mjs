import fs from 'node:fs';
import path from 'node:path';
import {
  archiveMetaFiles,
  cleanupInbox,
  collectFiles,
  copyFileWithBackup,
  ensureDir,
  nowStamp,
  removeDir,
  resolveExtractionRoot,
  safeName,
  waitForFile,
} from './fs-utils.mjs';
import { mapImportFile, validatePlanItem } from './routing.mjs';
import { extractZip } from './zip-utils.mjs';

export async function runImporter(config) {
  let isProcessing = false;
  let watcher = null;

  async function importZip(zipPath) {
    await waitForFile(zipPath);

    const fileName = path.basename(zipPath);

    if (!fileName.endsWith('.zip')) {
      return true;
    }

    removeDir(config.stagingDir);
    ensureDir(config.stagingDir);
    ensureDir(config.backupDir);
    ensureDir(config.roots.plugins);
    ensureDir(config.roots.muPlugins);

    const backupRoot = path.join(config.backupDir, `${nowStamp()}-${safeName(fileName.replace(/\.zip$/i, ''))}`);

    await extractZip(zipPath, config.stagingDir);

    const extractionRoot = resolveExtractionRoot(config.stagingDir);
    const files = collectFiles(extractionRoot);
    const plannedFiles = files.map((file) => validatePlanItem(mapImportFile(file, extractionRoot, config), config));
    const invalidFiles = plannedFiles.filter((item) => item.action === 'invalid');

    if (invalidFiles.length > 0) {
      console.error('Import abgebrochen. Nicht erlaubte Dateien:');
      invalidFiles.forEach((item) => console.error(`${item.sourceRelative} — ${item.reason}`));
      removeDir(config.stagingDir);
      return false;
    }

    logPlan(plannedFiles);

    if (config.dryRun) {
      console.log('Dry-Run aktiv. Es wurde nichts kopiert.');
      removeDir(config.stagingDir);
      return true;
    }

    writeImportRecord(config, backupRoot, zipPath, plannedFiles);
    archiveMetaFiles(backupRoot, plannedFiles, extractionRoot);

    for (const item of plannedFiles.filter((entry) => entry.action === 'install')) {
      const source = path.join(extractionRoot, item.sourceRelative);
      copyFileWithBackup(source, item.target, backupRoot, item.rootKey, item.targetRelative);
      console.log(`Importiert [${item.rootKey}]: ${item.targetRelative}`);
    }

    fs.unlinkSync(zipPath);
    removeDir(config.stagingDir);

    console.log(`OSP-Update fertig: ${fileName}`);
    console.log(`Backup/Import-Protokoll: ${backupRoot}`);

    return true;
  }

  async function processInbox() {
    if (isProcessing) {
      return;
    }

    isProcessing = true;

    const entries = fs.readdirSync(config.inboxDir, { withFileTypes: true });
    const zipFiles = entries
      .filter((entry) => entry.isFile() && entry.name.endsWith('.zip'))
      .map((entry) => path.join(config.inboxDir, entry.name))
      .sort();

    if (zipFiles.length === 0) {
      isProcessing = false;
      console.log('Keine ZIP-Dateien gefunden.');
      return;
    }

    for (const zipFile of zipFiles) {
      const success = await importZip(zipFile);

      if (!success) {
        isProcessing = false;

        if (watcher) {
          watcher.close();
        }

        console.error('OSP-Importer beendet wegen Fehler.');
        process.exit(1);
      }
    }

    if (!config.dryRun) {
      cleanupInbox(config.inboxDir);
    }

    isProcessing = false;
    console.log(config.dryRun ? 'OSP-Importer Dry-Run fertig.' : 'OSP-Importer fertig. Inbox wurde bereinigt.');
  }

  async function startWatcher() {
    console.log('Watch-Modus aktiv.');

    await processInbox();

    watcher = fs.watch(config.inboxDir, { persistent: true }, (eventType, fileName) => {
      if (!fileName || !fileName.toLowerCase().endsWith('.zip')) {
        return;
      }

      processInbox().catch((error) => {
        console.error(error);
        removeDir(config.stagingDir);
        process.exitCode = 1;
      });
    });
  }

  ensureDir(config.inboxDir);

  console.log(`OSP-Importer läuft: ${config.inboxDir}`);
  console.log(`Theme: ${config.themeRoot}`);
  console.log(`WP-Content: ${config.wpContentRoot}`);
  console.log(`Projekt: ${config.projectRoot}`);
  console.log(config.dryRun ? 'Modus: Dry-Run' : 'Modus: Import');

  if (config.watchMode) {
    await startWatcher();
  } else {
    await processInbox();
  }
}

function logPlan(plannedFiles) {
  const installFiles = plannedFiles.filter((item) => item.action === 'install');
  const meta = plannedFiles.filter((item) => item.action === 'meta');

  console.log('Import-Plan:');

  if (installFiles.length === 0) {
    console.log('Keine installierbaren Dateien gefunden.');
  }

  for (const item of installFiles) {
    console.log(`[${item.rootKey}] ${item.targetRelative}`);
  }

  if (meta.length > 0) {
    console.log('Metadaten:');
    meta.forEach((item) => console.log(`[meta] ${item.sourceRelative}`));
  }
}

function writeImportRecord(config, backupRoot, zipPath, plannedFiles) {
  ensureDir(backupRoot);

  const record = {
    importedAt: new Date().toISOString(),
    zip: path.basename(zipPath),
    dryRun: config.dryRun,
    themeRoot: config.themeRoot,
    wpContentRoot: config.wpContentRoot,
    projectRoot: config.projectRoot,
    inboxDir: config.inboxDir,
    roots: config.roots,
    files: plannedFiles.map((item) => {
      const copy = { ...item };
      delete copy.target;
      return copy;
    }),
  };

  fs.writeFileSync(path.join(backupRoot, 'import-plan.json'), `${JSON.stringify(record, null, 2)}\n`, 'utf8');
}
