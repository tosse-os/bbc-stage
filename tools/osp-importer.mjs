import { createConfig } from './osp-importer/config.mjs';
import { runImporter } from './osp-importer/run.mjs';

const config = createConfig();

runImporter(config).catch((error) => {
  console.error(error);
  process.exit(1);
});
