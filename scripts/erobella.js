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
            host: 'localhost',
            user: 'sail',
            password: 'password',
            database: 'laravel'
        });
        console.log('Connected to MySQL');

        let currentPage = 1;

        while (true) {
            const pageUrl = url + `/#?page=${currentPage}`;
            console.log(`Processing page ${currentPage}...`);

            await page.goto(pageUrl, { waitUntil: 'domcontentloaded' });

            // Wait for a specific condition or delay to ensure content is loaded
            await new Promise(resolve => setTimeout(resolve, 6000)); // Example: wait for 2 seconds

            // Extract href URLs using page.evaluate() within the context of the page
            const hrefUrls = await page.evaluate(() => {
                // Array to store the extracted href URLs
                const urls = [];

                // Select all <a> elements that are children of div.item.gallery
                const links = document.querySelectorAll('div.profile-block-wrap--main > div.profile-block > a[href]');

                // Loop through each <a> element and extract the href attribute
                links.forEach(link => {
                    // Exclude links that contain 'page=' in their href attribute
                    if (!link.href.includes('/#?page=') && !link.href.includes('impressum') && !link.href.includes('datensutz') && !link.href.includes('webcams') && !link.href.includes('/directre') && !link.href.includes('telefonsex') && !link.href.includes('stars') && !link.href.includes('/rt?') && !link.href.includes('/users/registration') ) {
                        urls.push(link.href);
                    }
                });

                return urls;
            });

            // If no URLs are found on the current page, break the loop
            if (hrefUrls.length === 0) {
                console.log('No more links found. Scraping complete.');
                break;
            }

            // Insert each URL into the MySQL database if it does not already exist
            for (const url of hrefUrls) {
                try {
                    const [rows] = await connection.execute('SELECT COUNT(*) AS count FROM erobella_urls WHERE url = ?', [url]);
                    const urlExists = rows[0].count > 0;

                    if (!urlExists) {
                        await connection.execute('INSERT INTO erobella_urls (url) VALUES (?)', [url]);
                        console.log(`Inserted URL: ${url}`);
                    } else {
                        console.log(`URL already exists: ${url}`);
                    }
                } catch (error) {
                    console.error(`Error inserting URL ${url}:`, error);
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
const baseUrl = 'https://erobella.com/sex/'; // Replace with your desired base URL

// Call the scrape function
scrapeWebsite(baseUrl)
    .catch(error => {
        console.error('Script execution error:', error);
    });
