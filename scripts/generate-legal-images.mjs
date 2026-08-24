import { chromium } from 'playwright';
import { mkdir } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');
const uploadDir = path.join(root, 'upload');
const baseUrl = process.env.LEGAL_EXPORT_BASE_URL || 'http://127.0.0.1:8087';
const viewportWidth = Number(process.env.LEGAL_EXPORT_WIDTH || 1000);
const deviceScaleFactor = Number(process.env.LEGAL_EXPORT_SCALE || 2);

const documents = [
	{ doc: 'personal_data', file: 'politics-oftalmag.png' },
	{ doc: 'consent', file: 'consent-oftalmag.png' },
	{ doc: 'cookie', file: 'cookies-oftalmag.png' },
	{ doc: 'recommendation', file: 'recommendations-oftalmag.png' },
];

await mkdir(uploadDir, { recursive: true });

const browser = await chromium.launch({ headless: true });
const context = await browser.newContext({
	viewport: { width: viewportWidth, height: 1400 },
	deviceScaleFactor,
});
const page = await context.newPage();

const results = [];

for (const item of documents) {
	const url = `${baseUrl}/legal/export/?doc=${item.doc}`;
	const outputPath = path.join(uploadDir, item.file);

	await page.goto(url, { waitUntil: 'networkidle', timeout: 120000 });
	await page.waitForSelector('.legal-export', { timeout: 30000 });
	await page.evaluate(() => document.fonts.ready);
	await page.screenshot({
		path: outputPath,
		fullPage: true,
		type: 'png',
		animations: 'disabled',
		caret: 'hide',
		scale: 'device',
	});

	results.push({ doc: item.doc, path: `/upload/${item.file}`, file: outputPath });
	console.log(`OK ${item.file} <= ${url} (${viewportWidth}px @ ${deviceScaleFactor}x)`);
}

await browser.close();

console.log('\nGenerated images:');
for (const row of results) {
	console.log(`${row.doc}: ${row.path}`);
}
