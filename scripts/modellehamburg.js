const puppeteer = require('puppeteer');
const mysql = require('mysql2/promise');

async function scrapeWebsiteAndExtractData(url) {
    console.log('Starting scraping process...');

    let browser;
    let connection;

    try {
        browser = await puppeteer.launch({ headless: true });
        const page = await browser.newPage();

        connection = await mysql.createConnection({
            host: 'localhost',
            user: 'sail',
            password: 'password',
            database: 'laravel'
        });
        console.log('Connected to MySQL');

        let currentPage = 1; // Starting page number
        const basePageUrl = url;

        while (true) {
            const pageUrl = `${basePageUrl}&page=${currentPage}`;
            console.log(`Processing page ${currentPage}...`);

            await page.goto(pageUrl, { waitUntil: 'domcontentloaded' });

            // Wait for specific content to ensure page is fully loaded
            await page.waitForSelector('h4.model-name > a[href]');

            // Extract href URLs using page.evaluate()
            const hrefUrls = await page.evaluate(() => {
                const urls = [];
                const links = document.querySelectorAll('h4.model-name > a[href]');
                links.forEach(link => {
                    urls.push(link.href);
                });
                return urls;
            });

            // If no URLs are found on the current page, break the loop
            if (hrefUrls.length === 0) {
                console.log('No more links found. Scraping complete.');
                break;
            }

            // Process each URL
            for (const url of hrefUrls) {
                console.log(`Processing URL: ${url}`);

                // Insert URL into the database
                const [rows] = await connection.execute('SELECT COUNT(*) AS count FROM modelle_hamburg_urls WHERE url = ?', [url]);
                const urlExists = rows[0].count > 0;

                if (!urlExists) {
                    await connection.execute('INSERT INTO modelle_hamburg_urls (url) VALUES (?)', [url]);
                    console.log(`Inserted URL: ${url}`);
                } else {
                    console.log(`URL already exists: ${url}`);
                }

                // Navigate to the individual URL and extract additional data
                await page.goto(url, { waitUntil: 'networkidle0' });

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
                    const adTitleElement = document.querySelector('h4.model-name > a');
                    const phoneNumberElement = document.querySelector('div.model-phone > a');
                    const cityElement = document.querySelector('address > b > a.sexlink');

                    const adTitle = adTitleElement ? adTitleElement.textContent.trim() : null;
                    const phoneNumber = phoneNumberElement ? phoneNumberElement.textContent.trim() : null;
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

                    await connection.execute(insertQuery, [adTitle, city, postcode, phoneNumber, siteId, url]);
                    console.log(`Inserted data for URL: ${url}`);
                }
            }

            currentPage++;
        }

        console.log('Scraping complete');

    } catch (error) {
        console.error('Error:', error);
    } finally {
        if (connection) await connection.end(); // Close MySQL connection if open
        if (browser) await browser.close();
        console.log('Script execution complete');
    }
}

// Example usage
const baseUrl = 'https://www.modelle-hamburg.de/modelle.html?data[start]=';

// Call the scrape function
scrapeWebsiteAndExtractData(baseUrl)
    .catch(error => {
        console.error('Script execution error:', error);
    });

// Helper function for phone number formatting
function formatPhoneNumber(phoneNumber) {
    const formattedNumber = phoneNumber.replace(/[^\d+]/g, '');
    return formattedNumber.startsWith('+49') || isGermanMobileNumber(formattedNumber) ? formattedNumber : null;
}

function isGermanMobileNumber(phoneNumber) {
    const germanMobilePattern = /^(?:\+?49)?(?:0)?(?:1[5-7]|1[016789][0-9])[\d]{8}$/;
    return germanMobilePattern.test(phoneNumber);
}
