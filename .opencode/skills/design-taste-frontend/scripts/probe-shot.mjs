import { Browser } from './lib/cdp.mjs';
import { writeFileSync } from 'node:fs';

const b = await Browser.launch({ width: 1440, height: 900 });
const page = await b.newPage({ width: 1440, height: 900 });
await page.goto(process.argv[2], { settleMs: 6000 });
const expr = `(() => {
  const el = document.querySelector('#pjax-container');
  return el ? 'pjax width=' + Math.round(el.getBoundingClientRect().width) : 'NO PJAX';
})()`;
const res = await page.send('Runtime.evaluate', { expression: expr, returnByValue: true });
console.log(res.result.value);
const shot = await page.send('Page.captureScreenshot', { format: 'jpeg', quality: 70 });
writeFileSync(process.argv[3] || 'C:/Users/bimar/AppData/Local/Temp/opencode/snap/probe-shot.jpg', Buffer.from(shot.data, 'base64'));
try { await b.close(); } catch { b.proc.kill(); }
