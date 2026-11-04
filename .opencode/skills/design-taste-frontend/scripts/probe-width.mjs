import { Browser } from './lib/cdp.mjs';

const b = await Browser.launch({ width: 1440, height: 900 });
const page = await b.newPage({ width: 1440, height: 900 });
await page.goto(process.argv[2], { settleMs: 3000 });
const expr = `(() => {
  let el = document.querySelector('#pjax-container');
  let node = el; const chain = [];
  while (node && node !== document.documentElement) {
    const r = node.getBoundingClientRect();
    const cs = getComputedStyle(node);
    chain.push(node.tagName + '[' + String(node.className).slice(0, 60) + '] w=' + Math.round(r.width) + ' disp=' + cs.display + ' mw=' + cs.maxWidth + ' ml=' + cs.marginLeft);
    node = node.parentElement;
  }
  return chain.join('\\n');
})()`;
const res = await page.send('Runtime.evaluate', { expression: expr, returnByValue: true });
console.log(res.result.value);
try { await b.close(); } catch { b.proc.kill(); }
