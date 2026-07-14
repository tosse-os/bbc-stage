import fs from 'node:fs';
import path from 'node:path';

export function nowStamp() {
  return new Date().toISOString().replace(/[:.]/g, '-');
}

export function safeName(name) {
  return name.replace(/[^a-zA-Z0-9._-]/g, '-');
}

export function normalizeRelative(relativePath) {
  return relativePath.replaceAll('\\', '/').replace(/^\.\//, '').replace(/\/+/g, '/').replace(/^\/+/, '');
}

export function startsWithAllowedPrefix(relativePath, prefixes) {
  return prefixes.some((allowed) => relativePath === allowed || relativePath.startsWith(allowed));
}

export function ensureDir(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

export function removeDir(dir) {
  if (fs.existsSync(dir)) {
    fs.rmSync(dir, { recursive: true, force: true });
  }
}

export function isBlockedRelativePath(relativePath, config) {
  const normalized = normalizeRelative(relativePath);
  const parts = normalized.split('/').filter(Boolean);
  const base = path.basename(normalized);

  if (!normalized || normalized.includes('\0') || path.isAbsolute(normalized)) {
    return true;
  }

  if (parts.includes('..')) {
    return true;
  }

  if (config.blockedNames.includes(base) || base.startsWith('.env')) {
    return true;
  }

  if (/\.(sql|sqlite|sqlite3|db|pem|key|crt|p12|pfx|log)$/i.test(base)) {
    return true;
  }

  return parts.some((part) => config.blockedSegments.includes(part));
}

export function safeTargetPath(root, relativePath) {
  const target = path.resolve(root, relativePath);
  const rootWithSep = root.endsWith(path.sep) ? root : `${root}${path.sep}`;

  if (target !== root && !target.startsWith(rootWithSep)) {
    return null;
  }

  return target;
}

export function copyFileWithBackup(source, target, backupRoot, rootKey, relativeTarget) {
  ensureDir(path.dirname(target));

  if (fs.existsSync(target)) {
    if (!fs.statSync(target).isFile()) {
      throw new Error(`Ziel ist keine Datei: ${target}`);
    }

    const backupTarget = path.join(backupRoot, 'previous', rootKey, relativeTarget);
    ensureDir(path.dirname(backupTarget));
    fs.copyFileSync(target, backupTarget);
  }

  fs.copyFileSync(source, target);
}

export function waitForFile(filePath) {
  return new Promise((resolve) => {
    let lastSize = -1;

    const timer = setInterval(() => {
      if (!fs.existsSync(filePath)) {
        return;
      }

      const size = fs.statSync(filePath).size;

      if (size > 0 && size === lastSize) {
        clearInterval(timer);
        resolve();
      }

      lastSize = size;
    }, 500);
  });
}

export function collectFiles(dir) {
  const files = [];

  function walk(currentDir) {
    for (const entry of fs.readdirSync(currentDir, { withFileTypes: true })) {
      if (entry.name === '.DS_Store' || entry.name === '__MACOSX') {
        continue;
      }

      const fullPath = path.join(currentDir, entry.name);

      if (entry.isDirectory()) {
        walk(fullPath);
      } else if (entry.isFile()) {
        files.push(fullPath);
      }
    }
  }

  walk(dir);

  return files;
}

export function resolveExtractionRoot(dir) {
  const entries = fs.readdirSync(dir, { withFileTypes: true }).filter((entry) => {
    return entry.name !== '.DS_Store' && entry.name !== '__MACOSX';
  });

  const routeRoots = new Set([
    'theme-files',
    'theme',
    'themes',
    'plugins',
    'mu-plugins',
    'project-files',
    'project',
    'web',
    'wp-content',
    'app',
    'assets',
    'inc',
    'languages',
    'parts',
    'patterns',
    'public',
    'resources',
    'src',
    'templates',
    'tools',
  ]);

  if (entries.length === 1 && entries[0].isDirectory() && !routeRoots.has(entries[0].name)) {
    return path.join(dir, entries[0].name);
  }

  return dir;
}

export function cleanupInbox(inboxDir) {
  if (!fs.existsSync(inboxDir)) {
    return;
  }

  for (const entry of fs.readdirSync(inboxDir, { withFileTypes: true })) {
    const fullPath = path.join(inboxDir, entry.name);

    if (entry.isDirectory()) {
      fs.rmSync(fullPath, { recursive: true, force: true });
    } else {
      fs.unlinkSync(fullPath);
    }
  }
}

export function archiveMetaFiles(backupRoot, plannedFiles, extractionRoot) {
  for (const item of plannedFiles.filter((entry) => entry.action === 'meta')) {
    const source = path.join(extractionRoot, item.sourceRelative);
    const target = path.join(backupRoot, 'meta', item.sourceRelative);
    ensureDir(path.dirname(target));
    fs.copyFileSync(source, target);
  }
}
