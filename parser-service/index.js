const express = require("express");
const puppeteer = require("puppeteer-extra");
const StealthPlugin = require("puppeteer-extra-plugin-stealth");
puppeteer.use(StealthPlugin());

const app = express();
app.use(express.json());

app.post("/parse", async (req, res) => {
    const { url } = req.body;
    if (!url) return res.status(400).json({ error: "URL required" });

    try {
        const browser = await puppeteer.launch({
            headless: true,
            args: ["--no-sandbox", "--disable-setuid-sandbox"],
        });
        const page = await browser.newPage();
        await page.setUserAgent(
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        );
        await page.goto(url, { waitUntil: "networkidle2", timeout: 60000 });

        // Прокрутка до конца
        let previousHeight = 0;
        for (let i = 0; i < 30; i++) {
            await page.evaluate(
                "window.scrollTo(0, document.body.scrollHeight)",
            );
            await new Promise((resolve) => setTimeout(resolve, 2000)); // замена waitForTimeout
            const newHeight = await page.evaluate("document.body.scrollHeight");
            if (newHeight === previousHeight) break;
            previousHeight = newHeight;
        }

        const data = await page.evaluate(() => {
            const name =
                document.querySelector("h1")?.textContent?.trim() || "";
            const ratingEl = document.querySelector(
                ".business-rating-badge-view__rating",
            );
            const rating = ratingEl
                ? parseFloat(ratingEl.textContent.replace(",", "."))
                : 0;
            const countEl = document.querySelector(
                ".business-rating-badge-view__rating-count",
            );
            const ratingCount = countEl
                ? parseInt(countEl.textContent.replace(/\D/g, ""))
                : 0;

            const reviewItems = document.querySelectorAll(
                ".business-review-view",
            );
            const reviews = [];
            reviewItems.forEach((item) => {
                const author =
                    item
                        .querySelector(".business-review-view__author")
                        ?.textContent?.trim() || "";
                const date =
                    item
                        .querySelector(".business-review-view__date")
                        ?.textContent?.trim() || "";
                const text =
                    item
                        .querySelector(".business-review-view__body")
                        ?.textContent?.trim() || "";
                const stars = item.querySelectorAll(
                    ".business-rating-star-view__star_active",
                ).length;
                reviews.push({ author, date, text, rating: stars });
            });

            return {
                name,
                rating,
                ratingCount,
                reviewCount: reviews.length,
                reviews,
            };
        });

        await browser.close();
        res.json(data);
    } catch (error) {
        console.error(error);
        res.status(500).json({ error: error.message });
    }
});

app.listen(3000, () => console.log("Parser service running on port 3000"));
