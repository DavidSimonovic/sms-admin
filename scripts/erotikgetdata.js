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

        const [rows] = await connection.execute('SELECT * FROM erotik_urls WHERE scraped = false');
        const urls = rows.map(row => ({ url: row.url, id: row.id }));

        browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });

        const page = await browser.newPage();

        function formatPhoneNumber(phoneNumber) {
            const formattedNumber = phoneNumber.replace(/[^\d+]/g, '');
            if (formattedNumber.startsWith('+49') || isGermanMobileNumber(formattedNumber)) {
                return formattedNumber;
            } else {
                return null;
            }
        }

        function separatePostalCodeAndCity(text) {
            // Regular expression to match postal code and city name
            const regex = /^(\d+)\s+(.+)$/;
            const match = text.match(regex);

            if (match) {
                const postalCode = match[1].trim();
                const city = match[2].trim();
                return { postalCode, city };
            } else {
                return { postalCode: null, city: null };
            }
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
                // Click on the element that reveals or loads the phone number
                const clickElement = document.querySelector('a.clsy-c-btn--secondary-restrained');
                if (clickElement) {
                    clickElement.click();
                    // Wait for the phone number to appear; adjust selector as needed
                    await new Promise(resolve => setTimeout(resolve, 3000)); // Wait for 3 seconds for phone number to load
                }
            });

            // Extract data after clicking
            const data = await page.evaluate(() => {
                let adTitle = null;
                let phoneNumber = null;
                let postcode = null;
                let city = null;

                const adTitleElement = document.querySelector('h1.clsy-c-expose__subject');
                if (adTitleElement) {
                    adTitle = adTitleElement.textContent.trim();
                }

                const phoneNumberElement = document.querySelector('span.clsy-attribute-list__description > a.clsy-c-btn--secondary-restrained[href]');
                if (phoneNumberElement) {
                    phoneNumber = phoneNumberElement.textContent.trim();
                }

                // const postcodeElement = document.querySelector('span[itemprop="postalCode"]');
                // if (postcodeElement) {
                //     postcode = postcodeElement.textContent.trim();
                // } else {
                //     postcode = '0000';
                // }

                function splitStringBySpace(inputString) {
                    return inputString.trim().split(/\s+/);
                }

                const cityElement = document.querySelector('div.clsy-c-expandable ul.clsy-attribute-list > li.clsy-attribute-list__item > span.clsy-attribute-list__description');
                if (cityElement) {
                     result = splitStringBySpace(cityElement.textContent.trim());

                     postcode = result[0];
                     city = result[1];
                }

                return { adTitle, phoneNumber, postcode, city };
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
                    await connection.execute('UPDATE erotik_urls SET scraped = true WHERE id = ?', [id]);
                    console.log(`Marked URL as scraped: ${url}`);
                    continue;
                }

                const insertQuery = `
                    INSERT INTO numbers (ad_title, city, postcode, number, site_id, url_id)
                    VALUES (?, ?, ?, ?, ?, ?)
                `;
                const { adTitle, city, postcode, phoneNumber } = data;
                const siteId = 5;

                await connection.execute(insertQuery, [adTitle, city, postcode, phoneNumber, siteId, id]);
                console.log(`Inserted data for URL: ${url}`);
            }

            await connection.execute('UPDATE erotik_urls SET scraped = true WHERE id = ?', [id]);
            console.log(`Marked URL as scraped: ${url}`);
        }

        console.log('Scraping complete');

    } catch (error) {
        console.error('Error during scraping:', error);
    } finally {
        if (connection) await connection.end();
        if (browser) await browser.close();
        process.exit();
    }
}

scrapeLadies()
    .then(() => {
        console.log('Script execution complete');
    })
    .catch(error => {
        console.error('Script execution error:', error);
    });
