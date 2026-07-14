import fs from 'node:fs';
import path from 'node:path';

export function createConfig() {
  const startDir = path.resolve(process.env.OSP_PROJECT_ROOT || process.cwd());
  const projectRoot = findProjectRoot(startDir);
  const wpContentRoot = findWpContentRoot(projectRoot);
  const themeName = process.env.OSP_THEME_NAME || 'bbc-theme';
  const themeRoot = path.resolve(process.env.OSP_THEME_ROOT || path.join(wpContentRoot, 'themes', themeName));
  const inboxDir = path.resolve(process.env.OSP_IMPORT_INBOX || path.join(projectRoot, '_incoming/osp-updates'));
  const backupDir = path.resolve(process.env.OSP_IMPORT_BACKUPS || path.join(projectRoot, '.osp-backups/osp-importer'));
  const stagingDir = path.resolve(process.env.OSP_IMPORT_STAGING || path.join(projectRoot, '.osp-staging/osp-importer'));
  const dryRun = process.argv.includes('--dry-run') || process.env.OSP_IMPORT_DRY_RUN === '1';
  const watchMode = process.argv.includes('--watch') || process.env.OSP_IMPORT_WATCH === '1';

  const roots = {
    project: projectRoot,
    theme: themeRoot,
    themes: path.join(wpContentRoot, 'themes'),
    plugins: path.join(wpContentRoot, 'plugins'),
    muPlugins: path.join(wpContentRoot, 'mu-plugins'),
  };

  return {
    startDir,
    themeRoot,
    wpContentRoot,
    projectRoot,
    inboxDir,
    backupDir,
    stagingDir,
    dryRun,
    watchMode,
    themeName,
    roots,
    maxFileBytes: Number(process.env.OSP_IMPORT_MAX_FILE_BYTES || 25000000),
    legacyThemePrefixes: [
      'README.txt',
      'README.md',
      'manifest.json',
      'app/osp-patterns.php',
      'resources/css/components/osp/',
      'resources/css/editor.css',
      'resources/js/app.js',
      'resources/patterns/osp/',
      'resources/images/osp/',
      'resources/views/sections/header.blade.php',
      'resources/views/sections/footer.blade.php',
      'resources/views/page.blade.php',
      'tools/osp-importer.mjs',
      'tools/osp-importer/',
    ],
    themePrefixes: [
      'app/',
      'assets/',
      'inc/',
      'languages/',
      'parts/',
      'patterns/',
      'public/',
      'resources/',
      'src/',
      'templates/',
      'tools/',
      'functions.php',
      'index.php',
      'package.json',
      'package-lock.json',
      'composer.json',
      'composer.lock',
      'postcss.config.js',
      'screenshot.png',
      'style.css',
      'tailwind.config.js',
      'theme.json',
      'vite.config.js',
    ],
    projectPrefixes: [
      'README.md',
      'README.txt',
      'OS-README.md',
      'docs/',
      'tools/',
    ],
    metaFiles: [
      'manifest.json',
      'osp-import.json',
      'README.md',
      'README.txt',
      'EXPORT_FOR_CHATGPT.md',
      'file-tree.txt',
    ],
    metaPrefixes: [
      'checks/',
      'pages/',
    ],
    blockedSegments: [
      '.git',
      '.hg',
      '.svn',
      '.idea',
      '.vscode',
      '__MACOSX',
      'node_modules',
      'vendor',
      'cache',
      'storage',
      'logs',
      'tmp',
      'temp',
    ],
    blockedNames: [
      '.env',
      '.env.local',
      '.env.production',
      '.env.development',
      'auth.json',
      'wp-config.php',
      'debug.log',
      'error_log',
      'access.log',
    ],
  };
}

function findProjectRoot(startDir) {
  let current = path.resolve(startDir);

  for (let index = 0; index < 12; index += 1) {
    if (fs.existsSync(path.join(current, 'web/app')) || fs.existsSync(path.join(current, 'wp-content'))) {
      return current;
    }

    if (fs.existsSync(path.join(current, 'composer.json')) && fs.existsSync(path.join(current, 'web'))) {
      return current;
    }

    const parent = path.dirname(current);

    if (parent === current) {
      break;
    }

    current = parent;
  }

  throw new Error(`Bedrock-/WordPress-Projekt-Root nicht gefunden, Startpunkt: ${startDir}`);
}

function findWpContentRoot(projectRoot) {
  const bedrockRoot = path.join(projectRoot, 'web/app');

  if (fs.existsSync(bedrockRoot)) {
    return bedrockRoot;
  }

  const classicRoot = path.join(projectRoot, 'wp-content');

  if (fs.existsSync(classicRoot)) {
    return classicRoot;
  }

  throw new Error(`WordPress-Content-Verzeichnis nicht gefunden: ${projectRoot}`);
}
