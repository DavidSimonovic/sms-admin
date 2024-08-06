const puppeteer = require('puppeteer');
const mysql = require('mysql2/promise');

async function scrapeWebsiteAndExtractData(url) {
    console.log('Starting scraping process...');

    let browser;
    let connection;

    try {
        browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });

        const page = await browser.newPage();

        connection = await mysql.createConnection({
            host: 'localhost',
            user: 'sail',
            password: 'password',
            database: 'laravel'
        });
        console.log('Connected to MySQL');

        let currentPage = 0; // Starting page number
        const basePageUrl = url;

        while (true) {
            const pageUrl = `${basePageUrl}${currentPage}&data[view]=list`;
            console.log(`Processing page ${currentPage}...`);

            await page.goto(pageUrl, { waitUntil: 'domcontentloaded' });

            try {
                // Specify a timeout value in milliseconds (e.g., 5000ms for 5 seconds)
                await page.waitForSelector('div.panel-heading', { timeout: 5000 });
                console.log('Selector found');
            } catch (error) {
                console.error('No more pages or timeout occurred');
                break; // Break out of the loop
            }

            // Extract all entries using page.evaluate()
            const entries = await page.evaluate(async () => {
                const items = [];
                const panels = document.querySelectorAll('div.panel.panel-default.mh-panel.panel-model-list-item');

                for (const panel of panels) {
                    const nameElement = panel.querySelector('div.panel-heading h4 a');
                    const cityElement = panel.querySelector('div.panel-heading h5 a.sexlink');
                    const detailsElement = panel.querySelector('div.panel-footer ul.list-inline');

                    if (nameElement && cityElement && detailsElement) {
                        const name = nameElement.textContent.trim();
                        const href = nameElement.getAttribute('href');
                        const city = cityElement.textContent.trim();

                        // Clicking to reveal phone number and other details
                        const clickElement = panel.querySelector('input.phonebutton');
                        if (clickElement) {
                            clickElement.click();
                            await new Promise(resolve => setTimeout(resolve, 3000)); // Wait for 3 seconds
                        }

                        const phoneNumberElement = panel.querySelector('div.model-phone > a');
                        const phoneNumber = phoneNumberElement ? phoneNumberElement.textContent.trim() : null;

                        const details = {};
                        detailsElement.querySelectorAll('li').forEach(li => {
                            const [value, label] = li.textContent.split(/(?<=\d)\s/); // Split by the space after the number
                            if (label) {
                                details[label] = value;
                            }
                        });

                        items.push({
                            name,
                            href,
                            city,
                            phoneNumber,
                            details
                        });
                    }
                }

                return items;
            });

            // If no entries are found on the current page, break the loop
            if (entries.length === 0) {
                console.log('No more items found. Scraping complete.');
                break;
            }

            for (const entry of entries) {
                const { name, href, city, phoneNumber, details } = entry;
                const fullUrl = `https://www.modelle-hamburg.de${href}`; // Construct full URL

                console.log(`Processing URL: ${fullUrl}`);

                // Insert URL into the database if not already present
                const [rows] = await connection.execute('SELECT COUNT(*) AS count FROM modelle_hamburg_urls WHERE url = ?', [fullUrl]);
                const urlExists = rows[0].count > 0;

                if (!urlExists) {
                    await connection.execute('INSERT INTO modelle_hamburg_urls (url) VALUES (?)', [fullUrl]);
                    console.log(`Inserted URL: ${fullUrl}`);
                } else {
                    console.log(`URL already exists: ${fullUrl}`);
                }

                if (phoneNumber) {
                    const formattedPhoneNumber = formatPhoneNumber(phoneNumber);

                    if (formattedPhoneNumber) {
                        console.log('Extracted Data:', { name, city, phoneNumber: formattedPhoneNumber });

                        const [existingNumbers] = await connection.execute('SELECT COUNT(*) AS count FROM numbers WHERE number = ?', [formattedPhoneNumber]);
                        const phoneNumberExists = existingNumbers[0].count > 0;

                        if (phoneNumberExists) {
                            console.log(`Phone number ${formattedPhoneNumber} already exists. Skipping URL: ${fullUrl}`);
                            continue;
                        }

                        const insertQuery = `
                            INSERT INTO numbers (ad_title, city, postcode, number, site_id, url_id)
                            VALUES (?, ?, ?, ?, ?, ?)
                        `;
                        const siteId = 4;

                        await connection.execute(insertQuery, [name, city, null, formattedPhoneNumber, siteId, fullUrl]);
                        console.log(`Inserted data for URL: ${fullUrl}`);
                    }
                }
            }

            currentPage++;
        }

        console.log('Scraping complete');

    } catch (error) {
        console.error('Error:', error);
    } finally {
        if (connection) {
            try {
                await connection.end(); // Close MySQL connection if open
            } catch (err) {
                console.error('Error closing MySQL connection:', err);
            }
        }
        if (browser) {
            try {
                await browser.close();
            } catch (err) {
                console.error('Error closing browser:', err);
            }
        }
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
