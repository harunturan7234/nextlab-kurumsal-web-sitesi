const { chromium } = require('playwright');
const fs = require('node:fs');
const path = require('node:path');

(async () => {
  const base = process.argv[2] || 'http://127.0.0.1:8080';
  const stage = process.argv[3] || 'final';
  const dir = path.join(__dirname, 'screenshots', stage);
  fs.mkdirSync(dir, { recursive: true });
  const browser = await chromium.launch({ headless: true });
  const report = { stage, base, pages: [], errors: [] };
  const page = await browser.newPage();
  page.setDefaultTimeout(10000);
  page.setDefaultNavigationTimeout(10000);
  page.on('pageerror', error => report.errors.push(error.message));
  for (const width of stage === 'before' ? [1440] : [1440, 768, 390, 320]) {
    await page.setViewportSize({ width, height: 1000 });
    const response = await page.goto(base, { waitUntil: 'domcontentloaded' });
    await page.locator('main').waitFor();
    await page.evaluate(async () => { await Promise.all(Array.from(document.images).map(i => { i.loading = 'eager'; return i.decode().catch(() => {}); })); });
    await page.screenshot({ path: path.join(dir, `home-${width}.png`), fullPage: true });
    report.pages.push({ url: '/', width, status: response.status(), ...await page.evaluate(() => ({
      title: document.title, h1: document.querySelectorAll('h1').length,
      overflow: document.documentElement.scrollWidth > innerWidth,
      broken: Array.from(document.images).filter(i => !i.naturalWidth).map(i => i.getAttribute('src')),
      unsafeLinks: Array.from(document.querySelectorAll('a[target="_blank"]')).filter(a => !a.rel.includes('noopener')).map(a => a.href)
    })) });
  }
  if (stage !== 'before') {
    const sitemap = await (await page.request.get(base + '/sitemap.xml')).text();
    const routes = [...sitemap.matchAll(/<loc>(.*?)<\/loc>/g)].map(m => new URL(m[1]).pathname);
    for (const route of [...new Set([...routes, '/arama?q=Everyday', '/urun/yok', '/kategori/yok'])]) {
      const response = await page.goto(base + route, { waitUntil: 'domcontentloaded' });
      await page.evaluate(async () => { await Promise.all(Array.from(document.images).map(i => { i.loading = 'eager'; return i.decode().catch(() => {}); })); });
      report.pages.push({ url: route, width: 320, status: response.status(), ...await page.evaluate(() => ({
        h1: document.querySelectorAll('h1').length,
        overflow: document.documentElement.scrollWidth > innerWidth,
        broken: Array.from(document.images).filter(i => !i.naturalWidth).map(i => i.getAttribute('src')),
        jsonld: Array.from(document.querySelectorAll('script[type="application/ld+json"]')).every(s => { try { JSON.parse(s.textContent); return true; } catch { return false; } })
      })) });
    }
    for (const [name, route] of [['category', '/kategori/temizlik-ve-hijyen'], ['product', '/urun/everyday-nose-patch-6li']]) {
      for (const width of [1440, 390]) {
        await page.setViewportSize({ width, height: 1000 });
        await page.goto(base + route, { waitUntil: 'domcontentloaded' });
        await page.evaluate(async () => { await Promise.all(Array.from(document.images).map(i => { i.loading = 'eager'; return i.decode().catch(() => {}); })); });
        await page.screenshot({ path: path.join(dir, `${name}-${width}.png`), fullPage: true });
      }
    }
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto(base);
    await page.locator('.menu-toggle').click();
    report.menuOpens = await page.locator('.main-nav').evaluate(el => el.classList.contains('open'));
    await page.locator('.menu-toggle').click();
    await page.locator('.hero-next').click();
    report.sliderWorks = await page.locator('[data-slide="1"]').evaluate(el => el.classList.contains('is-active'));
    await page.locator('.hero-next').click();
    await page.screenshot({ path: path.join(dir, 'hero-care-390.png') });
    const chat = await page.request.post(base + '/api/chat', { data: { message: 'Everyday burun bandı' } });
    report.chat = { status: chat.status(), body: await chat.json() };
  }
  fs.writeFileSync(path.join(__dirname, `${stage}-results.json`), JSON.stringify(report, null, 2));
  console.log(JSON.stringify(report, null, 2));
  await browser.close();
})().catch(error => { console.error(error); process.exit(1); });
