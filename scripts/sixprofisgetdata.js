const puppeteer = require('puppeteer');
const mysql = require('mysql2/promise');

async function scrapeSixprofi() {
    let connection;
    let browser;

    try {
        // Connect to MySQL database (replace with your MySQL connection details)
        connection = await mysql.createConnection({
            host: 'localhost',
            user: 'sail',
            password: 'password',
            database: 'laravel'
        });
        console.log('Connected to MySQL');

        // Fetch all URLs from sixprofis_urls table that have not been scraped (scraped = false)
        const [rows] = await connection.execute('SELECT * FROM sixprofis_urls WHERE scraped = false');
        const urls = rows.map(row => ({url: row.url, id: row.id}));

        // Initialize Puppeteer
        browser = await puppeteer.launch();
        const page = await browser.newPage();

        function formatPhoneNumber(phoneNumber) {

            const formattedNumber = phoneNumber.replace(/[^\d+]/g, '');


            if (formattedNumber.startsWith('+49') || isGermanMobileNumber(formattedNumber)) {
                return formattedNumber;
            } else {
                return null;
            }
        }


        function isGermanMobileNumber(phoneNumber) {

            const germanMobilePattern = /^(?:\+?49)?(?:0)?(?:1[5-7]|1[016789][0-9])[\d]{8}$/;
            return germanMobilePattern.test(phoneNumber);
        }

        // Loop through each URL and scrape data
        for (const {url, id} of urls) {
            console.log(`Scraping URL: ${url}`);
            await page.goto(url, {waitUntil: 'domcontentloaded'});

            // Example: Extract data from the page
            const data = await page.evaluate(() => {
                // Initialize variables with null
                let adTitle = null;
                let phoneNumber = null;
                let postcode = null;
                let city = null;

                // Check if elements exist before fetching their text content
                const adTitleElement = document.querySelector('h1');
                if (adTitleElement) {
                    adTitle = adTitleElement.textContent.trim();
                }

                const phoneNumberElement = document.querySelector('span[itemprop="telephone"]');
                if (phoneNumberElement) {
                    phoneNumber = phoneNumberElement.textContent.trim();
                }

                const postcodeElement = document.querySelector('span[itemprop="postalCode"]');
                if (postcodeElement) {
                    postcode = postcodeElement.textContent.trim();
                }

                const cityElement = document.querySelector('span[itemprop="addressLocality"]');
                if (cityElement) {
                    city = cityElement.textContent.trim();
                }

                return {adTitle, phoneNumber, postcode, city};
            });

            if (data.phoneNumber) {
                data.phoneNumber = formatPhoneNumber(data.phoneNumber);
            }
            if (data.phoneNumber) {
                console.log('Extracted Data:', data);

                // Check if the phone number already exists in the numbers table
                const [existingNumbers] = await connection.execute('SELECT COUNT(*) AS count FROM numbers WHERE number = ?', [data.phoneNumber]);
                const phoneNumberExists = existingNumbers[0].count > 0;

                if (phoneNumberExists) {
                    console.log(`Phone number ${data.phoneNumber} already exists. Skipping URL: ${url}`);
                    await connection.execute('UPDATE sixprofis_urls SET scraped = true WHERE id = ?', [id]);
                    console.log(`Marked URL as scraped: ${url}`);
                    continue; // Skip to the next URL if phone number exists
                }

                // Insert extracted data into numbers table along with url_id
                const insertQuery = `
                    INSERT INTO numbers (ad_title, city, postcode, number, site_id, url_id)
                    VALUES (?, ?, ?, ?, ?, ?)
                `;
                const {adTitle, city, postcode, phoneNumber} = data;
                const siteId = 1; // Replace with your actual site ID

                await connection.execute(insertQuery, [adTitle, city, postcode, phoneNumber, siteId, id]);
                console.log(`Inserted data for URL: ${url}`);

            }
            // Update the scraped column to true for the current URL
            await connection.execute('UPDATE sixprofis_urls SET scraped = true WHERE id = ?', [id]);
            console.log(`Marked URL as scraped: ${url}`);
        }

        console.log('Scraping complete');
    } catch (error) {
        console.error('Error during scraping:', error);
    } finally {
        // Close MySQL connection and Puppeteer browser
        if (connection) await connection.end();
        if (browser) await browser.close();
        process.exit();
    }
}

// Call the scraping function
scrapeSixprofi()
    .then(() => {
        console.log('Script execution complete');
    })
    .catch(error => {
        console.error('Script execution error:', error);
    });
