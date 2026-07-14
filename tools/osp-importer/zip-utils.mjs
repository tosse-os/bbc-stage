import { execFileSync } from 'node:child_process';

export async function extractZip(zipPath, targetDir) {
  try {
    const module = await import('adm-zip');
    const AdmZip = module.default || module;
    const zip = new AdmZip(zipPath);

    validateZipEntries(zip.getEntries().map((entry) => entry.entryName));
    zip.extractAllTo(targetDir, true);

    return;
  } catch (error) {
    try {
      const output = execFileSync('unzip', ['-Z1', zipPath], {
        encoding: 'utf8',
        stdio: ['ignore', 'pipe', 'pipe'],
      });

      validateZipEntries(output.split(/\r?\n/).filter(Boolean));

      execFileSync('unzip', ['-q', '-o', zipPath, '-d', targetDir], {
        stdio: 'pipe',
      });

      return;
    } catch (unzipError) {
      throw new Error('ZIP konnte nicht entpackt werden. Installiere entweder adm-zip per npm oder unzip im System.');
    }
  }
}

function validateZipEntries(entries) {
  const invalid = entries.filter((entry) => isUnsafeZipEntryName(entry));

  if (invalid.length > 0) {
    throw new Error(`ZIP enthält unsichere Pfade: ${invalid.join(', ')}`);
  }
}

function isUnsafeZipEntryName(entryName) {
  const normalized = entryName.replaceAll('\\', '/');
  const parts = normalized.split('/').filter(Boolean);

  if (!normalized || normalized.includes('\0')) {
    return true;
  }

  if (normalized.startsWith('/') || /^[a-zA-Z]:/.test(normalized)) {
    return true;
  }

  return parts.includes('..');
}
