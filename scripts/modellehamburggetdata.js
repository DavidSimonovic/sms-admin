const puppeteer = require('puppeteer');
const mysql = require('mysql2/promise');

async function scrapeLadies() {
    let connection;
    let browser;

    try {
        connection = await mysql.createConnection({
            host: 'localhost',
            user: 'sail',
            password: 'password',
            database: 'laravel'
        });
        console.log('Connected to MySQL');

        const [rows] = await connection.execute('SELECT * FROM modelle_hamburg_urls WHERE scraped = false');
        const urls = rows.map(row => ({ url: row.url, id: row.id }));

        browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });

        const page = await browser.newPage();

        function formatPhoneNumber(phoneNumber) {
            const formattedNumber = phoneNumber.replace(/[^\d+]/g, '');
            return formattedNumber.startsWith('+49') || isGermanMobileNumber(formattedNumber) ? formattedNumber : null;
        }

        function isGermanMobileNumber(phoneNumber) {
            const germanMobilePattern = /^(?:\+?49)?(?:0)?(?:1[5-7]|1[016789][0-9])[\d]{8}$/;
            return germanMobilePattern.test(phoneNumber);
        }

        for (const { url, id } of urls) {
            console.log(`Scraping URL: ${url}`);

            const maxRetries = 3;
            let retries = 0;
            let success = false;

            while (!success && retries < maxRetries) {
                try {
                    await page.goto(url, { waitUntil: 'networkidle0' });
                    success = true;
                } catch (error) {
                    console.error(`Error navigating to URL: ${url}`, error);
                    retries++;
                    await new Promise(resolve => setTimeout(resolve, 6000)); // Wait for 6 seconds before retrying
                }
            }

            if (!success) {
                console.error(`Failed to navigate to URL after ${maxRetries} retries: ${url}`);
                continue;
            }

            // Trigger click event and wait for the phone number to appear
            await page.evaluate(async () => {
                const clickElement = document.querySelector('input.phonebutton');
                if (clickElement) {
                    clickElement.click();
                    await new Promise(resolve => setTimeout(resolve, 3000)); // Wait for 3 seconds
                }
            });

            // Extract data after clicking
            const data = await page.evaluate(() => {
                const adTitleElement = document.querySelector('h1.model-name');
                const thElements = document.querySelectorAll('th');
                const cityElement = document.querySelector('a.sexlink');

                let phoneNumber = null;
                for (let th of thElements) {
                    if (th.textContent.trim() === 'Telefon') {
                        const td = th.nextElementSibling;
                        const a = td.querySelector('a');
                        phoneNumber = a ? a.textContent.trim() : null;
                        break;
                    }
                }

                const adTitle = adTitleElement ? adTitleElement.textContent.trim() : null;
                const city = cityElement ? cityElement.textContent.trim() : null;

                return { adTitle, phoneNumber, postcode: null, city };
            });

            if (data.phoneNumber) {
                data.phoneNumber = formatPhoneNumber(data.phoneNumber);
            }

            if (data.phoneNumber) {
                console.log('Extracted Data:', data);

                const [existingNumbers] = await connection.execute('SELECT COUNT(*) AS count FROM numbers WHERE number = ?', [data.phoneNumber]);
                const phoneNumberExists = existingNumbers[0].count > 0;

                if (phoneNumberExists) {
                    console.log(`Phone number ${data.phoneNumber} already exists. Skipping URL: ${url}`);
                    continue;
                }

                const insertQuery = `
                    INSERT INTO numbers (ad_title, city, postcode, number, site_id, url_id)
                    VALUES (?, ?, ?, ?, ?, ?)
                `;
                const { adTitle, city, postcode, phoneNumber } = data;
                const siteId = 4;

                await connection.execute(insertQuery, [adTitle, city, postcode, phoneNumber, siteId, id]);
                console.log(`Inserted data for URL: ${url}`);
            }

            // Mark URL as scraped
            await connection.execute('UPDATE modelle_hamburg_urls SET scraped = true WHERE id = ?', [id]);
            console.log(`Marked URL as scraped: ${url}`);
        }

        console.log('Scraping complete');

    } catch (error) {
        console.error('Error during scraping:', error);
    } finally {
        if (connection) await connection.end();
        if (browser) await browser.close();
    }
}

scrapeLadies()
    .then(() => {
        console.log('Script execution complete');
    })
    .catch(error => {
        console.error('Script execution error:', error);
    });
