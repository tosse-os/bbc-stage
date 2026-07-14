import fs from 'node:fs';
import path from 'node:path';
import {
  isBlockedRelativePath,
  normalizeRelative,
  safeTargetPath,
  startsWithAllowedPrefix,
} from './fs-utils.mjs';

export function mapImportFile(file, extractionRoot, config) {
  const sourceRelative = normalizeRelative(path.relative(extractionRoot, file));
  const size = fs.statSync(file).size;

  if (size > config.maxFileBytes) {
    return invalidPlan(sourceRelative, `Datei ist größer als ${config.maxFileBytes} Bytes.`);
  }

  if (isBlockedRelativePath(sourceRelative, config)) {
    return invalidPlan(sourceRelative, 'Pfad ist blockiert.');
  }

  if (isMetaPath(sourceRelative, config)) {
    return metaPlan(sourceRelative);
  }

  const structuredRules = [
    ['theme-files/', 'theme', isThemeAllowed],
    ['theme/', 'theme', isThemeAllowed],
    [`web/app/themes/${config.themeName}/`, 'theme', isThemeAllowed],
    [`wp-content/themes/${config.themeName}/`, 'theme', isThemeAllowed],
    ['themes/', 'themes', isNamedThemeAllowed],
    ['web/app/themes/', 'themes', isNamedThemeAllowed],
    ['wp-content/themes/', 'themes', isNamedThemeAllowed],
    ['plugins/', 'plugins', isPluginAllowed],
    ['web/app/plugins/', 'plugins', isPluginAllowed],
    ['wp-content/plugins/', 'plugins', isPluginAllowed],
    ['mu-plugins/', 'muPlugins', isPluginAllowed],
    ['web/app/mu-plugins/', 'muPlugins', isPluginAllowed],
    ['wp-content/mu-plugins/', 'muPlugins', isPluginAllowed],
    ['project-files/', 'project', isProjectAllowed],
    ['project/', 'project', isProjectAllowed],
  ];

  for (const [prefix, rootKey, allowed] of structuredRules) {
    const plan = routeStructuredFile(sourceRelative, prefix, rootKey, allowed, config);

    if (plan) {
      return plan;
    }
  }

  if (startsWithAllowedPrefix(sourceRelative, config.legacyThemePrefixes) && isThemeAllowed(sourceRelative, config)) {
    return installPlan(sourceRelative, 'theme', sourceRelative);
  }

  if (isThemeAllowed(sourceRelative, config)) {
    return installPlan(sourceRelative, 'theme', sourceRelative);
  }

  return invalidPlan(sourceRelative, 'Keiner erlaubten Import-Route zugeordnet.');
}

export function validatePlanItem(item, config) {
  if (item.action !== 'install') {
    return item;
  }

  const root = config.roots[item.rootKey];

  if (!root) {
    return invalidPlan(item.sourceRelative, `Unbekanntes Ziel: ${item.rootKey}`);
  }

  const target = safeTargetPath(root, item.targetRelative);

  if (!target) {
    return invalidPlan(item.sourceRelative, 'Zielpfad verlässt den erlaubten Root.');
  }

  return {
    ...item,
    target,
  };
}

function routeStructuredFile(relativePath, prefix, rootKey, allowed, config) {
  if (!relativePath.startsWith(prefix)) {
    return null;
  }

  const targetRelative = normalizeRelative(relativePath.slice(prefix.length));

  if (!targetRelative) {
    return invalidPlan(relativePath, 'Leerer Zielpfad.');
  }

  if (!allowed(targetRelative, config)) {
    return invalidPlan(relativePath, `Nicht erlaubt für ${rootKey}: ${targetRelative}`);
  }

  return installPlan(relativePath, rootKey, targetRelative);
}

function isMetaPath(relativePath, config) {
  return config.metaFiles.includes(relativePath) || startsWithAllowedPrefix(relativePath, config.metaPrefixes);
}

function isThemeAllowed(relativePath, config) {
  return !isBlockedRelativePath(relativePath, config) && startsWithAllowedPrefix(relativePath, config.themePrefixes);
}

function isProjectAllowed(relativePath, config) {
  return !isBlockedRelativePath(relativePath, config) && startsWithAllowedPrefix(relativePath, config.projectPrefixes);
}

function isNamedThemeAllowed(relativePath, config) {
  if (isBlockedRelativePath(relativePath, config)) {
    return false;
  }

  const parts = relativePath.split('/').filter(Boolean);

  if (parts.length < 2 || !isSafePackageDirectory(parts[0])) {
    return false;
  }

  return isThemeAllowed(parts.slice(1).join('/'), config);
}

function isPluginAllowed(relativePath, config) {
  if (isBlockedRelativePath(relativePath, config)) {
    return false;
  }

  const parts = relativePath.split('/').filter(Boolean);

  if (parts.length < 2) {
    return false;
  }

  return isSafePackageDirectory(parts[0]);
}

function isSafePackageDirectory(name) {
  return /^[a-zA-Z0-9._-]+$/.test(name) && !name.startsWith('.');
}

function invalidPlan(relativePath, reason) {
  return {
    action: 'invalid',
    sourceRelative: relativePath,
    reason,
  };
}

function metaPlan(relativePath) {
  return {
    action: 'meta',
    sourceRelative: relativePath,
  };
}

function installPlan(relativePath, rootKey, targetRelative) {
  return {
    action: 'install',
    sourceRelative: relativePath,
    rootKey,
    targetRelative,
  };
}
