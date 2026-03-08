import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { readFileSync, writeFileSync, existsSync } from 'fs';
import { resolve } from 'path';

function swManifestPlugin() {
    return {
        name: 'sw-manifest',
        closeBundle() {
            const manifestPath = resolve('public/build/.vite/manifest.json');
            if (!existsSync(manifestPath)) {
                console.log('[sw-manifest] No Vite manifest found, skipping');
                return;
            }

            try {
                const manifest = JSON.parse(readFileSync(manifestPath, 'utf-8'));
                const assets = [];

                for (const entry of Object.values(manifest)) {
                    if (entry.file) {
                        assets.push(`/build/${entry.file}`);
                    }
                    if (entry.css) {
                        entry.css.forEach((css) => assets.push(`/build/${css}`));
                    }
                }

                const swManifest = {
                    assets: [...new Set(assets)],
                    version: Date.now().toString(),
                };

                writeFileSync(
                    resolve('public/sw-manifest.json'),
                    JSON.stringify(swManifest, null, 2)
                );

                console.log(`[sw-manifest] Generated with ${swManifest.assets.length} assets`);
            } catch (error) {
                console.error('[sw-manifest] Error:', error.message);
            }
        },
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        swManifestPlugin(),
    ],
});
