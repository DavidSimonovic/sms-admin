const puppeteer = require('puppeteer');
const mysql = require('mysql2/promise'); // Using mysql2 library for MySQL database

async function scrapeWebsite(url) {
    const browser = await puppeteer.launch({
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const page = await browser.newPage();
    let connection; // Declare connection variable outside the try block

    try {
        connection = await mysql.createConnection({
            host: '127.0.0.1',
            user: 'smsadmin',
            password: 'New_Str0ng_P@ssw0rd!',
            database: 'sms_admin'
        });
        console.log('Connected to MySQL');

        let currentPage = 1;

        while (true) {
            const pageUrl = url + `?page=${currentPage}`;
            console.log(`Processing page ${currentPage}...`);

            try {
                // Navigate to the page and wait until DOM content is fully loaded
                await page.goto(pageUrl, { waitUntil: 'domcontentloaded', timeout: 30000 });
            } catch (navError) {
                console.error(`Error navigating to ${pageUrl}:`, navError);
                break; // Exit the loop if page navigation fails
            }

            // Wait for a specific element to appear before evaluating the page content
            try {
                await page.waitForSelector('div.item.gallery', { timeout: 10000 });
            } catch (selectorError) {
                console.log(`Element not found on page ${currentPage}. Stopping scrape.`);
                break; // Exit the loop if the target element isn't found
            }

            // Extract href URLs using page.evaluate() within the context of the page
            const hrefUrls = await page.evaluate(() => {
                const urls = [];
                const links = document.querySelectorAll('div.item.gallery a[href]');

                links.forEach(link => {
                    if (!link.href.includes('page=') && !link.href.includes('impressum') && !link.href.includes('datensutz') && link.href.includes('https://www.ladies.de') && !link.href.includes('webcams') && !link.href.includes('/directre') && !link.href.includes('telefonsex') && !link.href.includes('stars')) {
                        urls.push(link.href);
                    }
                });

                return urls;
            });

            if (hrefUrls.length === 0) {
                console.log('No more links found. Scraping complete.');
                break;
            }

            // Insert each URL into the MySQL database if it does not already exist
            for (const hrefUrl of hrefUrls) {
                try {
                    const [rows] = await connection.execute('SELECT COUNT(*) AS count FROM ladies_urls WHERE url = ?', [hrefUrl]);
                    const urlExists = rows[0].count > 0;

                    if (!urlExists) {
                        await connection.execute('INSERT INTO ladies_urls (url) VALUES (?)', [hrefUrl]);
                        console.log(`Inserted URL: ${hrefUrl}`);
                    } else {
                        console.log(`URL already exists: ${hrefUrl}`);
                    }
                } catch (dbError) {
                    console.error(`Error inserting URL ${hrefUrl}:`, dbError);
                }
            }

            currentPage++;
        }

    } catch (error) {
        console.error('Error:', error);
    } finally {
        await browser.close();
        if (connection) await connection.end(); // Close MySQL connection if open
        console.log('Script execution complete');
        process.exit();
    }
}

// Example usage
const baseUrl = 'https://www.ladies.de/sex-anzeigen'; // Replace with your desired base URL

// Call the scrape function
scrapeWebsite(baseUrl)
    .catch(error => {
        console.error('Script execution error:', error);
    });
