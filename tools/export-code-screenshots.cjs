const fs = require("fs-extra");
const path = require("path");
const hljs = require("highlight.js");
const puppeteer = require("puppeteer");

async function exportFolder(folderPath, outputDir) {
    await fs.ensureDir(outputDir);

    const files = fs.readdirSync(folderPath);

    const browser = await puppeteer.launch();
    const page = await browser.newPage();

    for (const file of files) {
        const fullPath = path.join(folderPath, file);
        const stat = fs.statSync(fullPath);

        if (stat.isDirectory()) {
            // rekursif ke subfolder
            await exportFolder(fullPath, path.join(outputDir, file));
            continue;
        }

        // filter ekstensi yang mau di-export
        if (!/\.(php|js|ts|html|css|json|md|blade\.php)$/i.test(file)) {
            continue;
        }

        const code = fs.readFileSync(fullPath, "utf8");

        // ========== 1) SIMPAN RAW TEXT / MD ==========
        const safeName = file.replace(/[\/\\:?*"<>|]/g, "_");
        const textOutPath = path.join(outputDir, safeName + ".txt");
        await fs.outputFile(textOutPath, code);

        // (opsional) kalau mau markdown juga:
        // const mdOutPath = path.join(outputDir, safeName + ".md");
        // await fs.outputFile(mdOutPath, "```" + "\n" + code + "\n```");

        // ========== 2) BUAT HTML DENGAN HIGHLIGHT ==========
        const highlighted = hljs.highlightAuto(code).value;

        const html = `
            <html>
            <head>
                <meta charset="utf-8" />
                <style>
                    body { margin: 20px; background: #1e1e1e; }
                    pre {
                        padding: 20px;
                        border-radius: 10px;
                        color: #fff;
                        background: #1e1e1e;
                        font-size: 14px;
                        line-height: 1.5;
                        font-family: Consolas, Menlo, Monaco, monospace;
                        white-space: pre-wrap;
                        word-break: break-word;
                    }
                    .hljs-keyword { color: #569CD6; }
                    .hljs-string  { color: #CE9178; }
                    .hljs-comment { color: #6A9955; }
                    .hljs-function{ color: #DCDCAA; }
                </style>
            </head>
            <body>
                <pre>${highlighted}</pre>
            </body>
            </html>
        `;

        await page.setContent(html, { waitUntil: "networkidle0" });

        const pngOutPath = path.join(outputDir, safeName + ".png");
        await page.screenshot({ path: pngOutPath, fullPage: true });

        console.log("✔ Saved PNG:", pngOutPath);
        console.log("✔ Saved TXT:", textOutPath);
    }

    await browser.close();
}

(async () => {
    const input = process.argv[2] || "app";
    const output = process.argv[3] || "docs/screenshots";

    console.log("📸 Exporting code screenshots + text...");
    console.log("Source:", input);
    console.log("Output:", output);

    await exportFolder(input, output);

    console.log("🚀 Done!");
})();
