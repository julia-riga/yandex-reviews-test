import puppeteer from "puppeteer-extra";
import StealthPlugin from "puppeteer-extra-plugin-stealth";

puppeteer.use(StealthPlugin());

const url = process.argv[2];
if (!url) {
    console.error("URL required");
    process.exit(1);
}

(async () => {
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();
    await page.goto(url, { waitUntil: "networkidle2" });

    // ... ваш код извлечения данных
    const data = await page.evaluate(() => {
        // извлечение данных
    });

    console.log(JSON.stringify(data));
    await browser.close();
})();
